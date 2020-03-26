<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Match extends Model
{
	/**
     * Victory Conditions
     *
     * @var array
     */
	private $victoryConditions = [
		[0, 1, 2],
		[3, 4, 5],
		[6, 7, 8],
		[0, 3, 6],
		[1, 4, 7],
		[2, 5, 8],
		[0, 4, 8],
		[6, 4, 2],
	];

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

	public function move($matchId, $position) {
		// Find match by ID
		$match = self::find($matchId);
		
		if($match->winner) {
			return 'not_available';
		}

		// Get board information
		$board = collect($match->board)->all();
		
        // Update board position
        if($board[$position] != 0) {
            return 'not_available';
        } else {
            $match->board->$position = $match->next;
            $match->board->update();
        }

        // Validate victory condition
        if($this->checkVictoryCondition($match->board, $match->next)) {
            $match->winner = $match->next;
		}
		
        // Set next player move
        $match->next = ($match->next == 1) ? 2 : 1;
		$match->update();
		
		return $match;
	}

	/**
     * Validate victory condition
     *
     * @param string $move
     * @return void
     */
    private function checkVictoryCondition($board, $move) {
		// Get occupied spaces by player
		$spacesByPlayer = (array) $this->getSpacesByPlayer(collect($board)->all(), $move);

		// Check victory
        foreach($this->victoryConditions as $vc) {
			if ($vc == array_intersect($vc, $spacesByPlayer)) {
                // Victory
				return true;
			}
        }
        
        // Match continue
        return false;
    }

    /**
     * Get occupied spaces by Player
     *
     * @param array $board
     * @param string $type
     * @return void
     */
    private function getSpacesByPlayer($board, $type) {
        return array_keys($board, $type);
	}
	
	/**
	 * Create a new Match
	 *
	 * @return void
	 */
	public function createMatch() {
		// Begin Transaction
		DB::beginTransaction();

		try {
			// Create a new Board
			$board = \App\Models\Board::create();

			// Create a new Match
			self::create([
				'name'      => 'Match ' . $board->id,
				'next'      => 1,
				'winner'    => 0,
				'board_id'  => $board->id,
			]);
		} catch(\Exception $e) {
			// Rollback
			DB::rollback();

			// Return error message
			return [
				'error' => $e->getMessage()
			];
		}

		// Commit
		DB::commit();
		return ['success'];
	}

	 /**
	  * Delete Match
	  *
	  * @param int $id
	  * @return void
	  */
	public function deleteMatch($id) {
		// Begin Transaction
		DB::beginTransaction();

		try {
			// Find match by ID
			$match = self::findOrFail($id);

			// Change active state
			$match->active = false;

			// Update field
			$match->update();
		} catch(\Exception $e) {
			// Rollback
			DB::rollback();

			// Return error message
			return [
				'error' => $e->getMessage()
			];
		}
        
		// Commit
		DB::commit();
		return ['success'];
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

	/**
	 * Get Active Match
	 *
	 * @return void
	 */
	public static function getActiveMatch() {
		return self::where('active', true)->first();
	}
}
