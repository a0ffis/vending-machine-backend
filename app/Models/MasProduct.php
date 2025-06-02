<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MasProduct
 *
 * @property uuid $id
 * @property string $name
 * @property float $default_price
 * @property string|null $description
 * @property string|null $image_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|MachineProduct[] $machine_products
 *
 * @package App\Models
 */
class MasProduct extends BaseUuidModel
{
    protected $table = 'mas_products';
    public $incrementing = false;

    protected $casts = [
        'default_price' => 'float'
    ];

    protected $fillable = [
        'name',
        'default_price',
        'description',
        'image_url'
    ];

    public function machine_products()
    {
        return $this->hasMany(MachineProduct::class, 'mas_products_id');
    }
}
