<?php

namespace App\Services;

use App\Models\MasCash;

/**
 * Class MasCashService.
 */
class MasCashService
{

    protected MasCash $masCashModel;

    public function __construct(MasCash $masCashModel)
    {
        $this->masCashModel = $masCashModel;
    }


    public function getAllMasCash(): array
    {
        return $this->masCashModel->all()->toArray();
    }


}
