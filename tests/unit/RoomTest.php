<?php
namespace degordian\roomfinder\tests;


use degordian\roomfinder\Room;

class RoomTest extends \Codeception\Test\Unit
{
    /**
     * @var \degordian\roomfinder\tests\UnitTester
     */
    protected $tester;

    /** @var Room */
    private $room;

    protected function _before()
    {
        $this->room = new Room();
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {

    }
}