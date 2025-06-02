<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;



/**
 * Class CacheLock
 * 
 * @property string $key
 * @property string $owner
 * @property int $expiration
 *
 * @package App\Models
 */
class CacheLock extends BaseUuidModel
{
	protected $table = 'cache_locks';
	protected $primaryKey = 'key';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'expiration' => 'int'
	];

	protected $fillable = [
		'owner',
		'expiration'
	];
}
