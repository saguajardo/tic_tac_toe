<?php

namespace App\Http\Controllers;

use App\Models\Match;
use Symfony\Component\HttpFoundation\Request;

class MatchController extends Controller {

    /**
     * Model Match
     *
     * @var object
     */
    private $match;

    /**
     * Constructor
     *
     * @param Match $match
     */
    public function __construct(Match $match) {
        $this->match = $match;
    }

    public function index() {
        return view('index');
    }

    /**
     * Returns a list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function matches() {
        return response()->json($this->listMatches());
    }

    /**
     * Returns the state of a single match
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function match($id) {
        return response()->json($this->listMatches($id));
    }

    /**
     * Makes a move in a match
     *
     * TODO it's mocked, make this work :)
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id) {
        $board = [
            1, 0, 2,
            0, 1, 2,
            0, 0, 0,
        ];

        $position = Input::get('position');
        $board[$position] = 2;

        return response()->json([
            'id' => $id,
            'name' => 'Match'.$id,
            'next' => 1,
            'winner' => 0,
            'board' => $board,
        ]);
    }

    /**
     * Creates a new match and returns the new list of matches
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        // Create a new Match
        $create = $this->match->createMatch();

        // If has an error
        if(array_key_exists('error', $create)) {
            throw new \Exception('create error: ' . $create['error']);
        }

        return response()->json($this->listMatches());
    }

    /**
     * Deletes the match and returns the new list of matches
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        // Delete Match
        $create = $this->match->deleteMatch($id);

        // If has an error
        if(array_key_exists('error', $create)) {
            throw new \Exception('delete error: ' . $create['error']);
        }

        // Get new list of matches
        return response()->json($this->listMatches());
    }

    /**
     * Get the array of matches
     */
    private function listMatches($id = null) {
        // Define collect response
        $collect = [];

        // Get Matches
        $collect = $this->match->getMatches($id);
        
        // If Match ID not found
        if($collect == 'not_found') {
            // Throw new Exception
            throw new \Exception('Match ID: ' . $id . ' not found');
        }

        // Return match data
        return $collect;
    }
}
