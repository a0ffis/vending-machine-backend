<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class User
 *
 * @property uuid $id
 * @property string $username
 * @property string $password_hash
 * @property string $role
 * @property string|null $tel
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|MachineTransaction[] $machine_transactions
 *
 * @package App\Models
 */
class User extends BaseUuidModel
{
    protected $table = 'user';
    public $incrementing = false;

    protected $casts = [];

    protected $fillable = [
        'username',
        'password_hash',
        'role',
        'tel'
    ];

    public function machine_transactions()
    {
        return $this->hasMany(MachineTransaction::class);
    }
}
