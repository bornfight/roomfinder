<?php

namespace degordian\RoomFinder\Adapters;

use degordian\RoomFinder\RoomCollection;

/**
 * Interface RoomResourceAdapterInterface
 * @package degordian\RoomFinder\Adapters
 */
interface RoomResourceAdapterInterface
{
    const DEFAULT_MEET_LENGTH = 30 * 60;
    const DEFAULT_SUMMARY = 'Meet';

    function getService();
    function getAllRoomsAvailability();
    function setTimeFrame($from, $to);
    function setRoomCollection(RoomCollection $rooms);
    function getRoomCollection();
    function reserveRoom($room, $length = self::DEFAULT_MEET_LENGTH, $summary = self::DEFAULT_SUMMARY);
}
