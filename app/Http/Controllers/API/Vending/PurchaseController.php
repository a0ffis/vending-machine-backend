<?php
namespace App\Http\Controllers\API\Vending;

use App\Http\Controllers\API\BaseController;
use App\Http\Services\Vending\PurchaseService; // Import the service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PurchaseController extends BaseController
{
    protected PurchaseService $purchaseService;

    /**
     * PurchaseController constructor.
     *
     * @param PurchaseService $purchaseService purchase service instance
     */
    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function purchaseProduct(Request $request)
    {
        $vendingMachineId = $request->header('x-api-key');

        $validator = Validator::make(
            $request->all(),
            [
                'machine_product_id' => 'required|uuid|exists:machine_products,id',
                'quantity' => 'required|integer|min:1',
                'inserted_cash' => 'required|array|min:1',
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

        try {
            // Call the service to handle the business logic
            $result = $this->purchaseService->purchaseProduct(
                $vendingMachineId,
                $request->input('machine_product_id'),
                $request->input('quantity'),
                $request->input('inserted_cash')
            );

            return $this->sendResponse($result, 200);
        } catch (Exception $e) {
            // Handle specific exceptions or general ones
            $statusCode = 500;
            $errorMessage = 'Purchase failed.';

            if ($e->getCode() === 404) {
                $statusCode = 404;
                $errorMessage = 'Machine Product Not Found.';
            } elseif ($e->getCode() === 422) {
                $statusCode = 422;
                $errorMessage = $e->getMessage(); // For validation-like errors from service
            }

            return $this->sendError(
                $errorMessage,
                ['error' => $e->getMessage()],
                $statusCode
            );
        }
    }
}
