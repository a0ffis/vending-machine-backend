<?php

namespace App\Http\Services\Vending;

use App\Models\MachineCash;
use App\Models\MachineProduct;
use App\Models\MachineTransaction;
use App\Models\MachineTransactionCharge;
use App\Models\MachineTransactionPaid;
use App\Models\MasCash;
use Exception;
// พิจารณาใช้ Custom Exceptions เพื่อการจัดการ Error ที่ดีขึ้นใน Controller
// เช่น use App\Exceptions\ProductNotFoundException;
// use App\Exceptions\InsufficientStockException;
// use App\Exceptions\PaymentException;
// use App\Exceptions\ChangeCalculationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    protected MachineProduct $machineProductModel;
    protected MasCash $masCashModel;
    protected MachineCash $machineCashModel;
    protected MachineTransaction $machineTransactionModel;
    protected MachineTransactionPaid $machineTransactionPaidModel;
    protected MachineTransactionCharge $machineTransactionChargeModel;

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
     * @throws \Exception If purchase fails (พิจารณาเปลี่ยนเป็น Custom Exceptions ที่เฉพาะเจาะจง)
     */
    public function purchaseProduct(
        string $vendingMachineId,
        string $machineProductId,
        int $quantity,
        array $insertedCashData
    ): array {
        // 1. Find Machine Product
        $machineProduct = $this->machineProductModel->where('id', $machineProductId)
            ->where('vending_machine_id', $vendingMachineId)
            ->first();

        if (!$machineProduct) {
            // อาจจะใช้ ProductNotFoundException
            throw new Exception("ไม่พบรหัสสินค้านี้ในเครื่อง", 404);
        }

        // 2. Process Inserted Cash
        $totalInsertedCash = $this->_calculateTotalInsertedCash($insertedCashData);

        // 3. Check Stock
        if ($machineProduct->quantity_in_stock < $quantity) {
            // อาจจะใช้ InsufficientStockException
            throw new Exception("สินค้าในสต็อกไม่เพียงพอสำหรับจำนวนที่ต้องการ", 422);
        }

        // 4. Calculate Total Price
        $totalPrice = $machineProduct->current_price * $quantity;

        // 5. Check Sufficient Funds
        if ($totalInsertedCash < $totalPrice) {
            // อาจจะใช้ PaymentException
            throw new Exception("จำนวนเงินที่ใส่ไม่เพียงพอสำหรับการสั่งซื้อ", 422);
        }

        // 6. Calculate Change
        $changeDetails = $this->_calculateChange($totalInsertedCash, $totalPrice, $vendingMachineId);

        if (empty($changeDetails) && $totalInsertedCash > $totalPrice) {
            // อาจจะใช้ ChangeCalculationException
            throw new Exception("เครื่องไม่สามารถจัดเตรียมเงินทอนที่เหมาะสมสำหรับจำนวนเงินที่ใส่ได้", 422);
        }

        $transactionId = null;

        DB::transaction(
            function () use (
                $machineProduct,
                $quantity,
                $vendingMachineId,
                $totalPrice,
                $totalInsertedCash,
                $changeDetails,
                $insertedCashData,
                &$transactionId
            ) {
                // 7. Reduce Product Stock
                $machineProduct->quantity_in_stock -= $quantity;
                $machineProduct->save();

                // 8. Log Transaction
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
                    throw new Exception("บันทึกการทำรายการล้มเหลว", 500);
                }

                $transactionId = $transaction->id;

                // 9. Log Transaction Paid
                foreach ($insertedCashData as $cashItem) {
                    $this->machineTransactionPaidModel->create(
                        [
                            'machine_transaction_id' => $transaction->id,
                            'mas_cash_id' => $cashItem['mas_cash_id'],
                            'quantity' => $cashItem['quantity'],
                        ]
                    );
                }

                // 10. Log Transaction Change
                foreach ($changeDetails as $changeItem) {
                    $this->machineTransactionChargeModel->create(
                        [
                            'machine_transaction_id' => $transaction->id,
                            'mas_cash_id' => $changeItem['mas_cash_id'],
                            'quantity' => $changeItem['quantity'],
                        ]
                    );
                }

                // Update Machine Cash (Add inserted cash)
                foreach ($insertedCashData as $cashItem) {
                    $machineCashEntry = $this->machineCashModel->firstOrNew(
                        [
                            'vending_machine_id' => $vendingMachineId,
                            'mas_cash_id'        => $cashItem['mas_cash_id'],
                        ]
                    );

                    if (!$machineCashEntry->exists) {
                        $machineCashEntry->quantity = 0;
                    }
                    $machineCashEntry->quantity += $cashItem['quantity'];
                    $machineCashEntry->save();
                }

                // 11. Update Machine Cash (Reduce change given)
                foreach ($changeDetails as $changeItem) {
                    $machineCashEntry = $this->machineCashModel->where('vending_machine_id', $vendingMachineId)
                        ->where('mas_cash_id', $changeItem['mas_cash_id'])
                        ->first();
                    if ($machineCashEntry) {
                        $machineCashEntry->quantity -= $changeItem['quantity'];
                        $machineCashEntry->save();
                    } else {
                        throw new Exception("ไม่พบข้อมูลเงินสดในเครื่องสำหรับรหัสชนิดเงิน '{$changeItem['mas_cash_id']}' ระหว่างอัปเดตยอดเงินคงเหลือ", 500);
                    }
                }
            }
        );

        return [
            'message' => 'การสั่งซื้อสำเร็จ', // แก้เป็นภาษาไทย
            'transaction_id' => $transactionId,
            'change' => $changeDetails,
            'total_price' => $totalPrice,
            'total_inserted_cash' => $totalInsertedCash,
            'machine_product_id' => $machineProduct->id,
            'quantity_purchased' => $quantity,
            'total_change_given' => $totalInsertedCash - $totalPrice,
        ];
    }

    private function _calculateTotalInsertedCash(array $insertedCashData): float
    {
        if (empty($insertedCashData)) {
            throw new Exception("ต้องมีรายการเงินที่ใส่อย่างน้อยหนึ่งชนิด", 422);
        }

        $totalInsertedCash = 0;
        foreach ($insertedCashData as $cashItem) {
            $masCashId = $cashItem['mas_cash_id'] ?? null;
            $quantity = $cashItem['quantity'] ?? 0;

            if (!$masCashId || $quantity <= 0) {
                throw new Exception("เงินที่ใส่แต่ละรายการต้องมีรหัสชนิดเงินและจำนวนที่ถูกต้อง", 422);
            }

            $masCash = $this->masCashModel->find($masCashId);
            if (!$masCash) {
                throw new Exception("ไม่พบชนิดเงินสดรหัส '{$masCashId}' ในระบบ", 422);
            }
            $totalInsertedCash += $masCash->value * $quantity;
        }
        return $totalInsertedCash;
    }

    private function _calculateChange(float $totalInsertedCash, float $totalPrice, ?string $vendingMachineId = null): array
    {
        $changeAmount = $totalInsertedCash - $totalPrice;

        if ($changeAmount <= 0) {
            return [];
        }

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
            $availableQuantity = $machineCash->quantity;

            while ($changeAmount >= $denominationValue && $availableQuantity > 0) {
                $changeAmount -= $denominationValue;
                $availableQuantity--;

                $changeKey = $machineCash->mas_cash_id;
                if (!isset($changeItems[$changeKey])) {
                    $changeItems[$changeKey] = [
                        'mas_cash_id' => $machineCash->mas_cash_id,
                        'type' => $machineCash->mas_cash->type,
                        'value' => $denominationValue,
                        'quantity' => 0,
                    ];
                }
                $changeItems[$changeKey]['quantity']++;
            }
        }

        if (round($changeAmount, 2) > 0) {
            // อาจจะใช้ ChangeCalculationException
            throw new Exception("ชนิดของเงินทอนในเครื่องไม่เพียงพอที่จะทอนให้พอดี จำนวนที่ขาด: " . round($changeAmount, 2), 422);
        }

        return array_values($changeItems);
    }
}
