<?php

namespace App\Http\Services\Vending;

use App\Models\MachineCash;
use App\Models\MachineProduct;
use App\Models\MachineTransaction;
use App\Models\MachineTransactionCharge;
use App\Models\MachineTransactionPaid;
use App\Models\MasCash;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB; // DB Facade ยังคงใช้ Static เพราะเป็น Global Helper

class PurchaseService
{
    // 1. Declare properties to hold injected model instances
    protected MachineProduct $machineProductModel;
    protected MasCash $masCashModel;
    protected MachineCash $machineCashModel;
    protected MachineTransaction $machineTransactionModel;
    protected MachineTransactionPaid $machineTransactionPaidModel;
    protected MachineTransactionCharge $machineTransactionChargeModel;

    // 2. Add a constructor to inject model instances
    // Laravel's Service Container will automatically resolve these dependencies
    public function __construct(
        MachineProduct $machineProductModel,
        MasCash $masCashModel,
        MachineCash $machineCashModel,
        MachineTransaction $machineTransactionModel,
        MachineTransactionPaid $machineTransactionPaidModel,
        MachineTransactionCharge $machineTransactionChargeModel
    ) {
        $this->machineProductModel = $machineProductModel;
        $this->masCashModel = $masCashModel;
        $this->machineCashModel = $machineCashModel;
        $this->machineTransactionModel = $machineTransactionModel;
        $this->machineTransactionPaidModel = $machineTransactionPaidModel;
        $this->machineTransactionChargeModel = $machineTransactionChargeModel;
    }

    /**
     * Executes the product purchase logic.
     *
     * @param string $vendingMachineId Vending machine ID
     * @param string $machineProductId machine_product_id
     * @param int    $quantity         quantity
     * @param array  $insertedCashData Array of ['mas_cash_id' => ..., 'quantity' => ...]
     *
     * @return array Success or error details
     *
     * @throws \Exception If purchase fails
     */
    public function purchaseProduct(
        string $vendingMachineId,
        string $machineProductId,
        int $quantity,
        array $insertedCashData
    ): array {
        // 1. Find Machine Product - Changed to use injected model instance
        $machineProduct = $this->machineProductModel->where('id', $machineProductId)
            ->where('vending_machine_id', $vendingMachineId)
            ->first();

        if (!$machineProduct) {
            throw new Exception("Machine Product Not Found", 404);
        }

        // 2. Process Inserted Cash - Calls a helper method that also uses injected models
        $totalInsertedCash = $this->_calculateTotalInsertedCash($insertedCashData);

        // 3. Check Stock
        if ($machineProduct->quantity_in_stock < $quantity) {
            throw new Exception("Not enough stock available for the requested quantity.", 422);
        }

        // 4. Calculate Total Price
        $totalPrice = $machineProduct->current_price * $quantity;

        // 5. Check Sufficient Funds
        if ($totalInsertedCash < $totalPrice) {
            throw new Exception("Not enough cash inserted to complete the purchase.", 422);
        }

        // 6. Calculate Change (Use specific vending machine's cash) - Calls a helper method
        $changeDetails = $this->_calculateChange($totalInsertedCash, $totalPrice, $vendingMachineId);

        // Check if change was expected but couldn't be calculated (e.g., machine ran out of specific change denominations)
        if (empty($changeDetails) && $totalInsertedCash > $totalPrice) {
            throw new Exception("No suitable change available for the inserted cash.", 422);
        }

        $transactionId = null; // Declare transactionId here for scope

        // Use DB::transaction for atomicity
        DB::transaction(
            function () use (
                $machineProduct,
                $quantity,
                $vendingMachineId,
                $totalPrice,
                $totalInsertedCash,
                $changeDetails,
                $insertedCashData,
                &$transactionId // Pass by reference to update outside the closure
            ) {
                // 7. Reduce Product Stock
                $machineProduct->quantity_in_stock -= $quantity;
                $machineProduct->save();

                // 8. Log Transaction - Changed to use injected model instance
                $transaction = $this->machineTransactionModel->create(
                    [
                        'vending_machine_id' => $vendingMachineId,
                        'machine_product_id' => $machineProduct->id,
                        'quantity_purchased' => $quantity,
                        'price_per_unit_at_transaction' => $machineProduct->current_price,
                        'total_amount_due' => $totalPrice,
                        'total_amount_paid' => $totalInsertedCash,
                        'total_change_given' => $totalInsertedCash - $totalPrice,
                        'status' => 'completed',
                        'transaction_at' => now(),
                    ]
                );

                if (!$transaction) {
                    throw new Exception("Failed to log the transaction.", 500);
                }

                // Save the transaction ID for later use in the outer scope
                $transactionId = $transaction->id;

                // 9. Log Transaction Paid (ลูกค้าจ่ายเป็น เหรียญหรือธนบัตรมาอย่างละกี่อัน) - Changed to use injected model instance
                foreach ($insertedCashData as $cashItem) {
                    $this->machineTransactionPaidModel->create(
                        [
                            'machine_transaction_id' => $transaction->id,
                            'mas_cash_id' => $cashItem['mas_cash_id'],
                            'quantity' => $cashItem['quantity'],
                        ]
                    );
                }

                // 10. Log Transaction Change (ทอนไปเท่าไหร่ อะไรบ้าง) - Changed to use injected model instance
                foreach ($changeDetails as $changeItem) {
                    $this->machineTransactionChargeModel->create(
                        [
                            'machine_transaction_id' => $transaction->id,
                            'mas_cash_id' => $changeItem['mas_cash_id'],
                            'quantity' => $changeItem['quantity'],
                        ]
                    );
                }

                // 11. Update Machine Cash (Reduce the quantity of cash in the machine) - Changed to use injected model instance
                foreach ($changeDetails as $changeItem) {
                    // Use the injected model to find and update
                    $machineCashEntry = $this->machineCashModel->where('vending_machine_id', $vendingMachineId)
                        ->where('mas_cash_id', $changeItem['mas_cash_id'])
                        ->first();
                    if ($machineCashEntry) {
                        $machineCashEntry->quantity -= $changeItem['quantity'];
                        $machineCashEntry->save();
                    } else {
                        // This case should ideally not happen if _calculateChange is robust
                        throw new Exception("Machine cash entry not found for denomination " . $changeItem['mas_cash_id'] . " during stock update.", 500);
                    }
                }
            }
        ); // End DB::transaction

        // Return the final response with transaction_id now correctly populated
        return [
            'message' => 'Purchase successful.',
            'transaction_id' => $transactionId,
            'change' => $changeDetails,
            'total_price' => $totalPrice,
            'total_inserted_cash' => $totalInsertedCash,
            'machine_product_id' => $machineProduct->id,
            'quantity_purchased' => $quantity,
            'total_change_given' => $totalInsertedCash - $totalPrice,
        ];
    }

    /**
     * Helper to calculate total cash inserted.
     *
     * @param array $insertedCashData // Array of ['mas_cash_id' => ..., 'quantity' => ...]
     *
     * @return float total_inserted_cash
     * @throws Exception
     */
    private function _calculateTotalInsertedCash(array $insertedCashData): float
    {
        if (empty($insertedCashData)) {
            throw new Exception("At least one cash item is required.", 422);
        }

        $totalInsertedCash = 0;
        foreach ($insertedCashData as $cashItem) {
            $masCashId = $cashItem['mas_cash_id'] ?? null;
            $quantity = $cashItem['quantity'] ?? 0;

            if (!$masCashId || $quantity <= 0) {
                throw new Exception("Each cash item must have a valid mas_cash_id and quantity.", 422);
            }

            // Changed to use injected model instance
            $masCash = $this->masCashModel->find($masCashId);
            if (!$masCash) {
                throw new Exception("Cash item with ID {$masCashId} does not exist.", 422);
            }
            $totalInsertedCash += $masCash->value * $quantity;
        }
        return $totalInsertedCash;
    }

    /**
     * Calculate the change to be returned to the user.
     *
     * @param float       $totalInsertedCash total_inserted_cash
     * @param float       $totalPrice        total_price
     * @param string|null $vendingMachineId  vending_machine_id
     *
     * @return array
     * @throws Exception If exact change cannot be made.
     */
    private function _calculateChange(float $totalInsertedCash, float $totalPrice, ?string $vendingMachineId = null): array
    {
        $changeAmount = $totalInsertedCash - $totalPrice;

        if ($changeAmount <= 0) {
            return []; // No physical change needed
        }

        // Load machine cash available for the specific vending machine - Changed to use injected model instance
        $machineCashDenominations = $this->machineCashModel->with('mas_cash')
            ->where('vending_machine_id', $vendingMachineId)
            ->get()
            ->filter(
                function ($cash) {
                    return $cash->mas_cash && $cash->mas_cash->value > 0 && $cash->quantity > 0;
                }
            )
            ->sortByDesc(
                function ($cash) {
                    return $cash->mas_cash->value;
                }
            );

        $changeItems = [];
        foreach ($machineCashDenominations as $machineCash) {
            $denominationValue = $machineCash->mas_cash->value;
            $availableQuantity = $machineCash->quantity; // Quantity in the machine

            while ($changeAmount >= $denominationValue && $availableQuantity > 0) {
                $changeAmount -= $denominationValue;
                $availableQuantity--;

                $changeKey = $machineCash->mas_cash_id; // Use mas_cash_id as key for grouping
                if (!isset($changeItems[$changeKey])) {
                    $changeItems[$changeKey] = [
                        'mas_cash_id' => $machineCash->mas_cash_id,
                        'value' => $denominationValue,
                        'quantity' => 0,
                    ];
                }
                $changeItems[$changeKey]['quantity']++;
            }
        }

        // Check if exact change could be made after trying all denominations
        if (round($changeAmount, 2) > 0) {
            throw new Exception("Insufficient cash denominations in machine to make exact change. Remaining: " . round($changeAmount, 2), 422);
        }

        return array_values($changeItems);
    }
}
