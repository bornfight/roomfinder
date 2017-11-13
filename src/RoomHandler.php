<?php

namespace degordian\roomfinder;

use degordian\roomfinder\Adapters\RoomResourceAdapterInterface;

/**
 * Class RoomHandler
 * @package degordian
 */
class RoomHandler
{
    const DEFAULT_MEETING_LENGTH = 60 * 30;
    const GOOGLE_CALENDAR = 1;
    const FIFTEEN_MINUTES = 60 * 15;
    const HALF_HOUR = 60 * 30;
    const HOUR = 60 * 60;

    /**
     * @var RoomResourceAdapterInterface
     */
    private $roomResourceAdapter = null;

    /** @var RoomCollection|Room[] */
    protected $roomCollection = [];
    protected $neededMeetingLength = null;

    protected $timeFrameFrom = null;
    protected $timeFrameTo = null;
    protected $availability = [];

    /**
     * @param RoomResourceAdapterInterface $adapter
     * @return $this
     */
    public function setRoomResourceAdapter(RoomResourceAdapterInterface $adapter)
    {
        $this->roomResourceAdapter = $adapter;
        return $this;
    }

    /**
     * @return RoomResourceAdapterInterface
     */
    public function getRoomResourceAdapter()
    {
        return $this->roomResourceAdapter;
    }

    /**
     * @param array $rooms
     * @return $this
     */
    public function addRooms(array $rooms)
    {
        $this->getRoomCollection()->addRooms($rooms);
        return $this;
    }

    /**
     * @param Room $room
     * @return $this
     */
    public function addRoom(Room $room)
    {
        $this->getRoomCollection()->addRoom($room);
        return $this;
    }

    /**
     * @return Room[]|RoomCollection
     */
    public function getRoomCollection()
    {
        if (!($this->roomCollection instanceof RoomCollection)) {
            $this->roomCollection = new RoomCollection();
        }
        return $this->roomCollection;
    }

    /**
     * @return array|Room[]|RoomCollection
     */
    public function getAllRoomsAvailability()
    {
        if (!empty($this->availability)) {
            return $this->availability;
        }

        $this->getRoomResourceAdapter()->setRoomCollection($this->getRoomCollection());

        $this->availability = $this->getRoomResourceAdapter()->getAllRoomsAvailability();
        return $this->getRoomCollection();
    }

    /**
     * @param $from
     * @param $to
     * @return $this
     */
    public function setTimeFrame($from, $to)
    {
        $this->timeFrameFrom = $from;
        $this->timeFrameTo = $to;
        $this->getRoomResourceAdapter()->setTimeFrame($this->timeFrameFrom, $this->timeFrameTo);

        return $this;
    }

    /**
     * @param int $meetingLength
     * @return Room[]
     */
    public function findAvailableRoom($meetingLength = self::HALF_HOUR)
    {
        $available = [];
        foreach ($this->getRoomCollection() as $room) {
            if ($room->isBusyNextHour() === false && $meetingLength == self::HOUR) {
                $available[] = $room;
                continue;
            }
            if ($room->isBusyNextHalfHour() === false && ($meetingLength == self::HALF_HOUR)) {
                $available[] = $room;
                continue;
            }
            if ($room->isBusyNextFifteenMinutes() === false && ($meetingLength == self::FIFTEEN_MINUTES)) {
                $available[] = $room;
                continue;
            }
        }

        return $available;
    }

    /**
     * @param Room[] $rooms
     * @param $size
     * @return array $filtered
     */
    protected function filterBySize($rooms, $size)
    {
        $filtered = [];

        foreach ((array)$rooms as $room) {
            $methodName = 'is' . ucfirst($size);
            if (method_exists($room, $methodName)) {
                if ($room->$methodName()) {
                    $filtered[] = $room;
                }
            }
        }

        return $filtered;
    }

    /**
     * @param int $meetingLength
     * @return array
     */
    public function findAvailableSmallRoom($meetingLength = self::HALF_HOUR)
    {
        $availableByTime = $this->findAvailableRoom($meetingLength);
        return $this->filterBySize($availableByTime, Room::SIZE_SMALL);
    }

    /**
     * @param int $meetingLength
     * @return array
     */
    public function findAvailableBigRoom($meetingLength = self::HALF_HOUR)
    {
        $availableByTime = $this->findAvailableRoom($meetingLength);
        return $this->filterBySize($availableByTime, Room::SIZE_BIG);
    }

    /**
     * @param int $meetingLength
     * @return array
     */
    public function findAvailableMediumRoom($meetingLength = self::HALF_HOUR)
    {
        $availableByTime = $this->findAvailableRoom($meetingLength);
        return $this->filterBySize($availableByTime, Room::SIZE_MEDIUM);
    }

    /**
     * @param $length
     * @return $this
     */


    public function setNeededMeetingLength($length)
    {
        $this->neededMeetingLength = $length;
        return $this;
    }

    /**
     * @param Room $room
     * @param null $length
     * @param null $summary
     * @return $this
     */
    public function reserveRoom($room, $length = null, $summary = null)
    {
        return $this->getRoomResourceAdapter()->reserveRoom($room, $length, $summary);
    }
}
