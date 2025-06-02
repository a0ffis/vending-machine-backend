<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MachineProduct
 *
 * @property uuid $id
 * @property uuid $vending_machine_id
 * @property uuid $mas_products_id
 * @property float $current_price
 * @property int $quantity_in_stock
 * @property string|null $slot_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property VendingMachine $vending_machine
 * @property MasProduct $mas_product
 * @property Collection|MachineTransaction[] $machine_transactions
 *
 * @package App\Models
 */
class MachineProduct extends BaseUuidModel
{
    protected $table = 'machine_products';
    public $incrementing = false;

    protected $casts = [
        'vending_machine_id' => 'uuid',
        'mas_products_id' => 'uuid',
        'current_price' => 'float',
        'quantity_in_stock' => 'int'
    ];

    protected $fillable = [
        'vending_machine_id',
        'mas_products_id',
        'current_price',
        'quantity_in_stock',
        'slot_number'
    ];

    public function vending_machine()
    {
        return $this->belongsTo(VendingMachine::class);
    }

    public function mas_product()
    {
        return $this->belongsTo(MasProduct::class, 'mas_products_id');
    }

    public function machine_transactions()
    {
        return $this->hasMany(MachineTransaction::class);
    }
}
