<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MachineCash;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MachineCashController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cashInVendingMachine(): JsonResponse
    {
        try {

            $vendingMachineId = request()->header('x-api-key');

            $result = MachineCash::where('vending_machine_id', $vendingMachineId)
                ->with(['mas_cash'])
                ->get();

            return $this->sendResponse(
                $result,
                'Machine cash data retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError(
                'Error retrieving machine cash data.',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Update or create cash in vending machine.
     *
     * @param \Illuminate\Http\Request $request The request containing the cash details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCashInVendingMachine(Request $request): JsonResponse
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'inserted_cash' => 'required|array',
                    'inserted_cash.*.mas_cash_id' => 'required|uuid|exists:mas_cash,id',
                    'inserted_cash.*.quantity' => 'required|integer|min:1',
                ]
            );

            if ($validator->fails()) {
                return $this->sendError(
                    'Validation Error',
                    $validator->errors()->all(),
                    422
                );
            }

            $vendingMachineId = request()->header('x-api-key');


            DB::beginTransaction();

            $insertedCash = $request->input('inserted_cash', []);

            foreach ($insertedCash as $cashItem) {
                $masCashId = $cashItem['mas_cash_id'];
                $quantity = $cashItem['quantity'];

                $machineCash = MachineCash::where('vending_machine_id', $vendingMachineId)
                    ->where('mas_cash_id', $masCashId)
                    ->first();

                if ($machineCash) {
                    $machineCash->quantity += $quantity;
                    $machineCash->save();
                } else {
                    // If the record does not exist, create a new one
                    $machineCash = MachineCash::create(
                        [
                            'vending_machine_id' => $vendingMachineId,
                            'mas_cash_id' => $masCashId,
                            'quantity' => $quantity,
                        ]
                    );
                }
            }

            $currentCash = MachineCash::where('vending_machine_id', $vendingMachineId)
                ->with(['mas_cash'])
                ->get();

            DB::commit();
            // DB::rollBack();

            return $this->sendResponse(
                $currentCash,
                'Machine cash updated successfully.'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError(
                'Error updating machine cash.',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
