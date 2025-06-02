<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class MachineTransactionPaid
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
class MachineTransactionPaid extends BaseUuidModel
{
    protected $table = 'machine_transaction_paid';
    public $incrementing = false;

    protected $casts = [
        'machine_transaction_id' => 'uuid',
        'mas_cash_id' => 'uuid',
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
