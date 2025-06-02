<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MachineTransaction
 *
 * @property uuid $id
 * @property uuid $vending_machine_id
 * @property uuid $machine_product_id
 * @property uuid|null $user_id
 * @property int $quantity_purchased
 * @property float $price_per_unit_at_transaction
 * @property float $total_amount_due
 * @property float $total_amount_paid
 * @property float $total_change_given
 * @property string $status
 * @property Carbon $transaction_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property VendingMachine $vending_machine
 * @property MachineProduct $machine_product
 * @property User|null $user
 * @property Collection|MachineTransactionPaid[] $machine_transaction_paids
 * @property Collection|MachineTransactionCharge[] $machine_transaction_charges
 *
 * @package App\Models
 */
class MachineTransaction extends BaseUuidModel
{
    protected $table = 'machine_transactions';
    public $incrementing = false;

    protected $casts = [
        'vending_machine_id' => 'uuid',
        'machine_product_id' => 'uuid',
        'user_id' => 'uuid',
        'quantity_purchased' => 'int',
        'price_per_unit_at_transaction' => 'float',
        'total_amount_due' => 'float',
        'total_amount_paid' => 'float',
        'total_change_given' => 'float',
        'transaction_at' => 'datetime'
    ];

    protected $fillable = [
        'vending_machine_id',
        'machine_product_id',
        'user_id',
        'quantity_purchased',
        'price_per_unit_at_transaction',
        'total_amount_due',
        'total_amount_paid',
        'total_change_given',
        'status',
        'transaction_at'
    ];

    public function vending_machine()
    {
        return $this->belongsTo(VendingMachine::class);
    }

    public function machine_product()
    {
        return $this->belongsTo(MachineProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function machine_transaction_paids()
    {
        return $this->hasMany(MachineTransactionPaid::class);
    }

    public function machine_transaction_charges()
    {
        return $this->hasMany(MachineTransactionCharge::class);
    }
}
