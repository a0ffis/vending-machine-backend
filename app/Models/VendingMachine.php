<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class VendingMachine
 *
 * @property uuid $id
 * @property string|null $address
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|MachineCash[] $machine_cashes
 * @property Collection|MachineProduct[] $machine_products
 * @property Collection|MachineTransaction[] $machine_transactions
 *
 * @package App\Models
 */
class VendingMachine extends BaseUuidModel
{
    protected $table = 'vending_machines';
    public $incrementing = false;

    protected $casts = [];

    protected $fillable = [
        'address',
        'status'
    ];

    public function machine_cashes()
    {
        return $this->hasMany(MachineCash::class);
    }

    public function machine_products()
    {
        return $this->hasMany(MachineProduct::class);
    }

    public function machine_transactions()
    {
        return $this->hasMany(MachineTransaction::class);
    }
}
