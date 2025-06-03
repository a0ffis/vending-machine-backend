<?php

namespace App\Http\Controllers\API\Vending;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\MachineProduct;
use App\Models\MasCash;
use App\Models\MasProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(): JsonResponse
    {
        $vendingMachineId = request()->header('x-api-key');

        if (!$vendingMachineId) {
            return $this->sendError('x-api-key header is required.', [], 400);
        }

        $machineProducts = MachineProduct::where('vending_machine_id', $vendingMachineId)
            ->with(['mas_product'])
            ->get();

        $productsData = $machineProducts->map(
            function ($machineProduct) {
                if (!$machineProduct->mas_product) {
                    return null; // กรณี mas_product ไม่ถูกผูกอยู่ (ควรป้องกันไม่ให้เกิด)
                }

                $masProduct = $machineProduct->mas_product;
                $imageUrl = null;

                // ตรวจสอบว่า mas_product มี image_url และไม่เป็นค่าว่าง
                if (!empty($masProduct->image_url)) {
                    // $imageUrl = Storage::url($masProduct->image_url);
                    $imageUrl = asset('storage/' . $masProduct->image_url); // ใช้ asset() เพื่อสร้าง URL ที่ถูกต้อง
                }

                return [
                    'id' => $machineProduct->id,
                    'name' => $masProduct->name,
                    'description' => $masProduct->description,
                    'image_url' => $imageUrl, // ใช้ URL ที่สร้างขึ้น
                    'current_price' => $machineProduct->current_price,
                    'quantity_in_stock' => $machineProduct->quantity_in_stock,
                    'slot_number' => $machineProduct->slot_number,
                ];

            }
        )->filter();

        return $this->sendResponse($productsData->values(), 'Products retrieved successfully.'); // values() เพื่อ reset array keys
    }
}
