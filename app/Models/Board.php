<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
			'0', '1', '2', '3', '4', '5', '6', '7', '8',
	];

	protected $hidden = [
		'created_at',
		'updated_at',
		'id'
	];
	
	protected $table = 'board';

}
