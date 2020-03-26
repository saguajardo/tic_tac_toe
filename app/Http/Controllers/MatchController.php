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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id, Request $request) {
        // Get position
        $position = $request->input('position');
        
        // Find match by ID
        $match = $this->match->move($id, $position);

        if($match == 'not_available') {
            throw new \Exception('Not available');
        } else {
            // Return match data
            return response()->json([
                'id'        => $match->id,
                'name'      => $match->name,
                'next'      => $match->next,
                'winner'    => $match->winner,
                'board'     => $match->board,
            ]);
        }
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
