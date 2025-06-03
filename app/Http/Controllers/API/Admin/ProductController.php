<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MachineProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request The request containing the product details
     * @param string $request->mas_products_id The ID of the product from the master products table
     * @param int $request->quantity The quantity of the product to be added
     * @param float $request->current_price The current price of the product
     * @param string $request->slot_number The slot number where the product will be added
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mas_products_id' => 'required|exists:mas_products,id',
                'quantity' => 'required|integer|min:1',
                'current_price' => 'required|numeric|min:0',
                'slot_number' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 422);
        }

        $machineId = $request->header('x-api-key');
        $existingProduct = MachineProduct::where('vending_machine_id', $machineId)
            ->where('slot_number', $request->input('slot_number'))
            ->first();

        if ($existingProduct) {
            return $this->sendError(
                'Product already exists in this slot.',
                ['slot_number' => 'This slot is already occupied by another product.'],
                409
            );
        }

        $input = $request->only(['mas_products_id', 'quantity', 'current_price', 'slot_number']);
        $input['vending_machine_id'] = $machineId;

        DB::beginTransaction();

        try {

            $result = MachineProduct::create($input);

            DB::commit();
            // DB::rollBack(); // Rollback Transaction ถ้าเกิด Exception (ถ้าใช้ Transaction)

            return $this->sendResponse(
                $result,
                'Product added successfully.',
                201
            );
        } catch (Exception $e) {
            DB::rollBack(); // Rollback Transaction ถ้าเกิด Exception (ถ้าใช้ Transaction)
            return $this->sendError('Error occurred while adding product.', [$e->getMessage()], 500);
        }
    }
}
