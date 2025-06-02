<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class MachineCash
 *
 * @property uuid $id
 * @property uuid $vending_machine_id
 * @property uuid $mas_cash_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property VendingMachine $vending_machine
 * @property MasCash $mas_cash
 *
 * @package App\Models
 */
class MachineCash extends BaseUuidModel
{
    protected $table = 'machine_cash';
    public $incrementing = false;

    protected $casts = [
        'vending_machine_id' => 'uuid',
        'mas_cash_id' => 'uuid',
        'quantity' => 'int'
    ];

    protected $fillable = [
        'vending_machine_id',
        'mas_cash_id',
        'quantity'
    ];

    public function vending_machine()
    {
        return $this->belongsTo(VendingMachine::class);
    }

    public function mas_cash()
    {
        return $this->belongsTo(MasCash::class);
    }
}
