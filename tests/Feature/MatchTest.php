<?php

namespace Tests\Feature;

use App\Http\Controllers\MatchController;
use App\Models\Board;
use App\Models\Match;
use Exception;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use stdClass;

class MatchTest extends TestCase
{
    /**
     * Instance MatchController
     *
     * @var object
     */
    private $instance;

    /**
     * Match Model
     *
     * @var object
     */
    private $match;

    /**
     * Request
     *
     * @var object
     */
    private $request;

    /**
     * Espected Json structure
     *
     * @var array
     */
    protected $structure = [
            "board" => [],
            "id",
            "name",
            "next",
            "winner"
    ];
    
    /**
     * Test GET match api.
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::matches
     */
    public function testgetMatches()
    {
        // Invoque api
        $response = $this->json('GET', '/api/match');

        // Assert Json Structure
        $response->assertJsonStructure([$this->structure]);
        // Assert Status Code
        $response->assertStatus(200);
    }

    /**
     * Test GET match api by Match ID.
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::match
     */
    public function testgetMatchesById()
    {
        // Get an active Match
        $validMatch = Match::getActiveMatch();

        // If exists
        if($validMatch) {
            // Invoque api
            $response = $this->json('GET', '/api/match/' . $validMatch->id);

            // Assert Json Structure
            $response->assertJsonStructure($this->structure);
            // Assert Status Code
            $response->assertStatus(200);
        }

        $this->assertTrue(true);
    }


    /**
     * Test GET match api by an invalid Match ID.
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::match
     */
    public function testgetMatchesByInvalidId()
    {
        // Invoque api
        $response = $this->json('GET', '/api/match/undefined');

        // Decode Json
        $content = json_decode($response->getContent());

        $this->assertEquals('Match ID: undefined not found', $content->message);
    }

    /**
     * Test Move function
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::move
     */
    public function testMove() {
        // Define Mocks
        $this->match = $this->createMock(Match::class);
        $this->request = $this->createMock(Request::class);

        // Mock method move
        $this->match->method('move')
            ->will($this->returnCallback(array($this, 'callbackMatchMove')));
            
        // Mock method request->input
        $this->request->expects($this->exactly(1))
            ->method('input')
            ->will($this->returnCallback(array($this, 'callbackRequestInput')));

        // Instance MatchController
        $this->instance = new MatchController($this->match);

        // Execute move method
        $response = $this->instance->move(1, $this->request);
        
        // Assert Array has key id
        $this->assertArrayHasKey('id', $response->original);
    }

    /**
     * Test Create
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::create
     */
    public function testCreate() {
        // Define Mocks
        $this->match = $this->createMock(Match::class);

        // Mock method Match create
        $this->match->method('createMatch')
            ->will($this->returnCallback(array($this, 'callbackMatchCreate')));

        // Mock method Match create
        $this->match->method('getMatches')
        ->will($this->returnCallback(array($this, 'callbackGetMatches')));
            
        // Instance MatchController
        $this->instance = new MatchController($this->match);

        // Execute move method
        $response = $this->instance->create();

        // Assert Array has key id
        $this->assertArrayHasKey('id', $response->original);
    }

    /**
     * Test Create with exception
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::create
     * 
     * @expectedException Exception
     */
    public function testCreateError() {
        // Define Mocks
        $this->match = $this->createMock(Match::class);

        // Mock method Match create
        $this->match->method('createMatch')
            ->will($this->returnCallback(array($this, 'callbackMatchCreateError')));

        // Mock method Match create
        $this->match->method('getMatches')
        ->will($this->returnCallback(array($this, 'callbackGetMatchesIdNotFound')));
            
        // Instance MatchController
        $this->instance = new MatchController($this->match);

        // Execute create method
        $this->instance->create();
    }

    /**
     * Test Delete
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::delete
     */
    public function testDelete() {
        // Define Mocks
        $this->match = $this->createMock(Match::class);

        // Mock method Match create
        $this->match->method('deleteMatch')
            ->will($this->returnCallback(array($this, 'callbackMatchCreate')));

        // Mock method Match create
        $this->match->method('getMatches')
        ->will($this->returnCallback(array($this, 'callbackGetMatches')));
            
        // Instance MatchController
        $this->instance = new MatchController($this->match);

        // Execute move method
        $response = $this->instance->delete(1);

        // Assert Array has key id
        $this->assertArrayHasKey('id', $response->original);
    }

    /**
     * Test Delete with exception
     *
     * @return void
     * 
     * @runInSeparateProcess
     * @preserveGblobalState disabled
     * @cover MatchController::delete
     * 
     * @expectedException Exception
     */
    public function testDeleteError() {
        // Define Mocks
        $this->match = $this->createMock(Match::class);

        // Mock method Match create
        $this->match->method('deleteMatch')
            ->will($this->returnCallback(array($this, 'callbackMatchCreateError')));

        // Mock method Match create
        $this->match->method('getMatches')
        ->will($this->returnCallback(array($this, 'callbackGetMatchesIdNotFound')));
            
        // Instance MatchController
        $this->instance = new MatchController($this->match);

        // Execute create method
        $this->instance->delete(1);
    }

    /**
     * Callback Match Move
     * Get an object with match data
     *
     * @return void
     */
    public function callbackMatchMove() {
        $response = new stdClass();
        $response->id = 1;
        $response->name = "Match1";
        $response->next = 2;
        $response->winner = 0;
        $response->board = new stdClass();
        $response->board = (object) array('0' => '0', '1' => '1', '2' => '0');
        
        return $response;
    }

    /**
     * Callback Request Input
     * Return a position for match move
     *
     * @return void
     */
    public function callbackRequestInput() {
        $response = 0;
        
        return $response;
    }

    /**
     * Callback Match Create
     *
     * @return array
     */
    public function callbackMatchCreate() {
        // Return success
        return ['success'];
    }

    /**
     * Callback Match Create error
     *
     * @return array
     */
    public function callbackMatchCreateError() {
        // Return success
        return ['error'];
    }
    
    /**
     * Callback Get Matches
     *
     * @return void
     */
    public function callbackGetMatches() {
        // return success
        return [
            'id'    => 1,
            'name'    => 'Match1',
            'next'    => 1,
            'winner'    => 0,
            'board'    => [0, 0, 0, 0 ,0, 0, 0, 0 ,0],
        ];
    }

    /**
     * Callback Get Matches with ID invalid
     *
     * @return void
     */
    public function callbackGetMatchesIdNotFound() {
        return 'not_found';
    }
}
