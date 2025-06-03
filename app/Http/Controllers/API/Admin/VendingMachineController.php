<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MachineCash;
use App\Models\MasCash;
use App\Models\VendingMachine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendingMachineController extends BaseController
{
    /**
     * Create a new vending machine.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createVendingMachine(Request $request): JsonResponse
    {

        try {

            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                ]
            );

            $prepareBody = [
                'name' => $request->input('name', 'Default Vending Machine'),
                'address' => $request->input('address', 'Unknown Location'),
                'status' => 'active',
            ];

            DB::beginTransaction();

            $masCash = MasCash::all()->where('is_accepted', 'active');

            $result = VendingMachine::create($prepareBody);

            if (!$result) {
                return $this->sendError(
                    'Error creating vending machine.',
                    ['error' => 'Failed to create vending machine.'],
                    500
                );
            }

            if (!$masCash) {
                return $this->sendError(
                    'Error creating vending machine.',
                    ['error' => 'No active cash configuration found.'],
                    500
                );
            }

            $macCashList = [];

            foreach ($masCash as $cash) {

                $macCash = MachineCash::create(
                    [
                        'vending_machine_id' => $result->id,
                        'mas_cash_id' => $cash->id,
                        'quantity' => 0,
                    ]
                );

                $macCashList[] = $macCash;
            }

            DB::commit();
            // DB::rollBack();

            return $this->sendResponse(
                $result,
                'Vending machine created successfully.',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                'Error creating vending machine.',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
