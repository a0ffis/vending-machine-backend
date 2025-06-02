<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MasCash
 *
 * @property uuid $id
 * @property int $value
 * @property string $type
 * @property string $currency
 * @property bool $is_accepted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|MachineCash[] $machine_cashes
 * @property Collection|MachineTransactionPaid[] $machine_transaction_paids
 * @property Collection|MachineTransactionCharge[] $machine_transaction_charges
 *
 * @package App\Models
 */
class MasCash extends BaseUuidModel
{
    protected $table = 'mas_cash';
    public $incrementing = false;

    protected $casts = [
        'value' => 'int',
        'is_accepted' => 'bool'
    ];

    protected $fillable = [
        'value',
        'type',
        'currency',
        'is_accepted'
    ];

    public function machine_cashes()
    {
        return $this->hasMany(MachineCash::class);
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
