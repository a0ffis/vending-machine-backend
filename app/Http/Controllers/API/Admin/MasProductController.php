<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Models\MasProduct;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasProductController extends BaseController
{
    public function storeProduct(Request $request)
    {
        // 1. Validate ข้อมูลที่ส่งมา (รวมถึงไฟล์)
        $validator = Validator::make(
            $request->all(),
            [
                'product_name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'product_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation สำหรับไฟล์รูปภาพ
            ]
        );

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->all(), 422);
        }

        DB::beginTransaction();

        try {
            $input = $request->except('product_image');

            $preparedInput = [
                // 'id' => Str::uuid(),
                'name' => $input['product_name'],
                'default_price' => $input['price'],
                'description' => $input['description'] ?? null,
            ];

            $relativePath = null;

            if ($request->hasFile('product_image') && $request->file('product_image')->isValid()) {
                $filename = time() . '_' . Str::random(10) . '.' . $request->file('product_image')->getClientOriginalExtension();
                $destination = public_path('');

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }

                $request->file('product_image')->move($destination, $filename);
                $preparedInput['image_url'] = '/' . $filename; // เก็บ relative path
            } else {
                $preparedInput['image_url'] = null;
            }

            // return $this->sendResponse($preparedInput, 'Product created successfully.', 201);
            $product = MasProduct::create($preparedInput);

            DB::commit(); // Commit Transaction ถ้าทุกอย่างสำเร็จ (ถ้าใช้ Transaction)
            // DB::rollBack();

            // 3. ส่ง Response กลับ
            // ถ้าคุณใช้ API Resource ให้ใช้ new MasProductResource($product->fresh())
            return $this->sendResponse($product, 'Product created successfully.', 201);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback Transaction ถ้าเกิด Exception (ถ้าใช้ Transaction)

            // จัดการ Exception ที่เกิดขึ้น
            // คุณอาจจะ Log error นี้ไว้สำหรับตรวจสอบ
            // Log::error('Error creating product: ' . $e->getMessage());

            // ส่ง Response กลับเป็น Error
            // คุณสามารถปรับแต่ง message หรือ error code ได้ตามชนิดของ Exception ที่เกิดขึ้น
            // $e->getMessage() จะให้รายละเอียดของ Error ซึ่งอาจจะไม่เหมาะกับการแสดงผลให้ User โดยตรงใน Production
            return $this->sendError('An error occurred while creating the product.', ['error_detail' => $e->getMessage()], 500);
        }
    }
}
