<?php

namespace degordian\roomfinder\Adapters;

use degordian\roomfinder\RoomCollection;

/**
 * Interface RoomResourceAdapterInterface
 * @package degordian\RoomFinder\Adapters
 */
interface RoomResourceAdapterInterface
{
    const DEFAULT_MEET_LENGTH = 30 * 60;
    const DEFAULT_SUMMARY = 'Meet';

    public function getService();
    public function getAllRoomsAvailability();
    public function setTimeFrame($from, $to);
    public function setRoomCollection(RoomCollection $rooms);
    public function getRoomCollection();
    public function reserveRoom($room, $length = self::DEFAULT_MEET_LENGTH, $summary = self::DEFAULT_SUMMARY);
}
