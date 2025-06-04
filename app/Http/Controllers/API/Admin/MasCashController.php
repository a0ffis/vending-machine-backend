<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\Controller;
use App\Services\MasCashService;
use Exception;
use Illuminate\Http\Request;

class MasCashController extends BaseController
{

    protected MasCashService $masCashService;

    public function __construct(MasCashService $masCashService)
    {
        $this->masCashService = $masCashService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $cashData = $this->masCashService->getAllMasCash();

            if (empty($cashData)) {
                return $this->sendError(
                    'No cash data found',
                    [],
                    404
                );
            }

            return $this->sendResponse(
                $cashData,
                'Cash data retrieved successfully'
            );

        } catch (Exception $e) {
            return $this->sendError(
                'Error fetching cash data',
                ['error' => $e->getMessage()],
                500
            );
        }
    }
}
