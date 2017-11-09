<?php

namespace degordian\roomfinder\tests;

use Codeception\Util\Stub;
use degordian\roomfinder\Room;
use degordian\roomfinder\RoomCollection;

class RoomCollectionTest extends \Codeception\Test\Unit
{
    /**
     * @var \degordian\roomfinder\tests\UnitTester
     */
    protected $tester;

    /** @var RoomCollection */
    private $roomCollection;

    protected function _before()
    {
        $this->roomCollection = new RoomCollection();
    }

    public function testAddRoom_AddingToEmptyCollection_ExpectsOneRoomInCollection()
    {
        $room = new Room();
        $this->roomCollection->addRoom($room);

        $result = $this->roomCollection->getRooms();
        $expected = [$room];
        $this->assertSame($expected, $result);
    }

    public function testAddRoom_AddingMultipleRooms_ExpectsCorrectOrder()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room3 = new Room();

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $expected = [$room1, $room2, $room3];
        $result = $this->roomCollection->getRooms();
        $this->assertSame($expected, $result);
    }

    public function testAddRoom_WhenAddingDuplicateRooms()
    {
        $this->markTestSkipped('Should we allowed duplicate rooms or throw an exception?');
    }

    public function testAddRoom_WhenAddingNull_ExpectsTypeError()
    {
        $this->expectException(\TypeError::class);
        $this->roomCollection->addRoom(null);
    }

    public function testAddRooms_ShouldCallAddSingleRoom()
    {
        //Not sure if I should keep this test
        $input = [
            new Room(),
            new Room(),
            new Room(),
        ];

        $roomCount = count($input);

        /** @var RoomCollection $roomCollectionStub */
        $roomCollectionStub = Stub::make(RoomCollection::class, [
            'addRoom' => Stub::exactly($roomCount),
        ]);

        $roomCollectionStub->addRooms($input);
    }

    public function testAddRooms_EmptyCollection()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room3 = new Room();

        $input = [
            $room1,
            $room2,
            $room3,
        ];

        $this->roomCollection->addRooms($input);

        $expected = [$room1, $room2, $room3];

        $result = $this->roomCollection->getRooms();
        $this->assertSame($expected, $result);
    }

    public function testAddRooms_NonEmptyCollection()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room3 = new Room();
        $this->roomCollection->addRoom($room1);

        $input = [
            $room2,
            $room3,
        ];

        $this->roomCollection->addRooms($input);

        $expected = [$room1, $room2, $room3];

        $result = $this->roomCollection->getRooms();
        $this->assertSame($expected, $result);
    }

    public function testAddRooms_WhenNullGiven()
    {
        $this->expectException(\TypeError::class);
        $this->roomCollection->addRooms(null);
    }

    public function testAddRooms_WhenEmptyArrayGiven()
    {
        $this->roomCollection->addRooms([]);

        $result = $this->roomCollection->getRooms();

        $this->assertEmpty($result);
    }

    public function testAddRooms_WhenArrayContainsInvalidObjectType()
    {
        $this->expectException(\TypeError::class);

        $input = ['foo', 'bar'];
        $this->roomCollection->addRooms($input);
    }

    public function testGetRooms_EmptyCollection()
    {
        $result = $this->roomCollection->getRooms();

        $this->assertEmpty($result);
    }

    public function testGetRooms_Encapsulation()
    {
        //Also not sure to keep this
        $room1 = new Room();
        $this->roomCollection->addRoom($room1);

        $rooms = $this->roomCollection->getRooms();
        array_push($rooms, new Room());

        $result = $this->roomCollection->getRooms();
        $this->assertSame([$room1], $result);
    }

    public function testGetRoom_WhenRoomIdDoesNotExist_ExpectsNull()
    {
        $result = $this->roomCollection->getRoom(150);

        $this->assertNull($result);
    }

    public function testGetRoom_WhenRoomIdExists()
    {
        $roomId = 1;
        $room = new Room();
        $room->setId($roomId);

        $this->roomCollection->addRoom($room);

        $result = $this->roomCollection->getRoom($roomId);

        $this->assertSame($room, $result);
    }

    public function testGetAllRoomIds()
    {
        $room1 = new Room();
        $room1->setId(1);

        $room2 = new Room();
        $room2->setId(2);

        $room3 = new Room();
        $room3->setId(3);

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $expected = [1, 2, 3];

        $result = $this->roomCollection->getAllRoomIds();

        $this->assertSame($expected, $result);
    }

    public function testIterator()
    {
        //Break into smaller test methods
        $room1 = new Room();
        $room1->setId(1);

        $room2 = new Room();
        $room2->setId(2);

        $room3 = new Room();
        $room3->setId(3);

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $this->roomCollection->rewind();

        $this->assertSame(0, $this->roomCollection->key());
        $this->assertSame($room1, $this->roomCollection->current());

        $this->roomCollection->next();
        $this->assertTrue($this->roomCollection->valid());
        $this->assertSame(1, $this->roomCollection->key());
        $this->assertSame($room2, $this->roomCollection->current());

        $this->roomCollection->next();
        $this->assertTrue($this->roomCollection->valid());
        $this->assertSame(2, $this->roomCollection->key());
        $this->assertSame($room3, $this->roomCollection->current());

        $this->roomCollection->next();
        $this->assertFalse($this->roomCollection->valid());
    }

    public function testArrayAccess()
    {
        //Break into smaller test methods. But tomorrow
        $room1 = new Room();
        $room1->setId(1);

        $room2 = new Room();
        $room2->setId(2);

        $room3 = new Room();
        $room3->setId(3);

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $this->assertTrue($this->roomCollection->offsetExists(0));
        $this->assertTrue($this->roomCollection->offsetExists(1));
        $this->assertTrue($this->roomCollection->offsetExists(2));
        $this->assertFalse($this->roomCollection->offsetExists(3));

        $this->assertSame($room1, $this->roomCollection->offsetGet(0));
        $this->assertSame($room2, $this->roomCollection->offsetGet(1));
        $this->assertSame($room3, $this->roomCollection->offsetGet(2));

        $newRoom = new Room();
        $this->roomCollection->offsetSet(150, $newRoom);

        $newRoomOffsetGet = $this->roomCollection->offsetGet(150);
        $this->assertSame($newRoom, $newRoomOffsetGet);

        $this->roomCollection->offsetUnset(150);
        $this->assertFalse($this->roomCollection->offsetExists(150));
    }

    public function testIterator_WhenElementsHaveBeenUnset()
    {
        $room1 = new Room();
        $room1->setId(1);

        $room2 = new Room();
        $room2->setId(2);

        $room3 = new Room();
        $room3->setId(3);

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $this->roomCollection->offsetUnset(1);

        $expected = [$room1, $room3];
        $result = [];

        foreach ($this->roomCollection as $index => $room) {
            $result[] = $room;
        }
        $this->assertSame($expected, $result);
    }

    public function testGetRooms_WhenElementsHaveBeenUnset()
    {
        $room1 = new Room();
        $room1->setId(1);

        $room2 = new Room();
        $room2->setId(2);

        $room3 = new Room();
        $room3->setId(3);

        $this->roomCollection->addRoom($room1);
        $this->roomCollection->addRoom($room2);
        $this->roomCollection->addRoom($room3);

        $this->roomCollection->offsetUnset(1);

        $expected = [$room1, $room3];
        $result = $this->roomCollection->getRooms();

        $this->assertSame($expected, $result);
    }
}
