<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Match extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
			'id', 'name', 'next', 'winner', 'board_id', 'active',
	];
	
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'match';
	
	/**
	 * Get the Board for the Match.
	 */
	public function board()
	{
		return $this->hasOne('App\Models\Board', 'id', 'board_id');
	}

	/**
	 * Get Matches
	 *
	 * @param int $id
	 * @return void
	 */
	public function getMatches($id = null) {
		$collect = [];
		// Get matches
		$matches = self::where('active', true);

		if($id) {
			// Get one match
			$match = $matches->where('id', $id)->first();

			// If not exists
			if(!$match) {
				return 'not_found';
			}

			// Format response
			$collect = [
				'id'    => $match->id,
				'name'    => $match->name,
				'next'    => $match->next,
				'winner'    => $match->winner,
				'board'    => $match->board,
			];
		} else {
			// Format response
			foreach($matches->get() as $match) {
				$collect[] = [
					'id'    => $match->id,
					'name'    => $match->name,
					'next'    => $match->next,
					'winner'    => $match->winner,
					'board'    => $match->board,
				];
			}
		}

		return $collect;
	}

}
