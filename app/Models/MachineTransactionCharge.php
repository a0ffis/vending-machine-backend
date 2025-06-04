<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class MachineTransactionCharge
 *
 * @property uuid $id
 * @property uuid $machine_transaction_id
 * @property uuid $mas_cash_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property MachineTransaction $machine_transaction
 * @property MasCash $mas_cash
 *
 * @package App\Models
 */
class MachineTransactionCharge extends BaseUuidModel
{
    protected $table = 'machine_transaction_charge';
    public $incrementing = false;

    protected $casts = [
        'quantity' => 'int'
    ];

    protected $fillable = [
        'machine_transaction_id',
        'mas_cash_id',
        'quantity'
    ];

    public function machine_transaction()
    {
        return $this->belongsTo(MachineTransaction::class);
    }

    public function mas_cash()
    {
        return $this->belongsTo(MasCash::class);
    }
}
