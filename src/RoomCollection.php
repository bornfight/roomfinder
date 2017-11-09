<?php

namespace degordian\RoomFinder;

/**
 * Class RoomCollection
 * @package degordian\RoomFinder
 */
class RoomCollection implements \Iterator, \ArrayAccess
{
    /**
     * @var Room[]
     */
    protected $rooms = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param Room $room
     */
    public function addRoom(Room $room)
    {
        $this->rooms[] = $room;
    }

    /**
     * @param $rooms
     */
    public function addRooms($rooms)
    {
        foreach ($rooms as $room) {
            $this->addRoom($room);
        }
    }
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * @param $roomId
     * @return Room|null
     */
    public function getRoom($roomId)
    {
        foreach ($this as $room) {
            if ($room->getId() === $roomId) {
                return $room;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAllRoomIds()
    {
        $roomsIds = [];
        foreach ($this as $room) {
            $roomsIds[] = $room->getId();
        }
        return $roomsIds;
    }

    /**
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return Room
     */
    public function current()
    {
        return $this->rooms[$this->position];
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     *
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->rooms[$this->position]);
    }

    /**
     * @param mixed $position
     * @return bool
     */
    public function offsetExists($position)
    {
        return isset($this->rooms[$position]);
    }

    /**
     * @param mixed $position
     * @return Room
     */
    public function offsetGet($position)
    {
        return $this->rooms[$position];
    }

    /**
     * @param mixed $position
     * @param mixed $value
     */
    public function offsetSet($position, $value)
    {
        $this->rooms[$position] = $value;
    }

    /**
     * @param mixed $position
     */
    public function offsetUnset($position)
    {
        unset($this->rooms[$position]);
    }
}
