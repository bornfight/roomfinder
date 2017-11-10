<?php

include '../vendor/autoload.php';

use degordian\roomfinder\Room;
use degordian\roomfinder\RoomHandler;
use degordian\roomfinder\Adapters\RoomResourceGoogleCalendar;

$meetingRooms = [
    [
        'name' => 'Meeting room one',
        'id' => 'YOUR_CALENDAR_ID_1@group.calendar.google.com',
        'size' => Room::SIZE_BIG,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ],
    [
        'name' => 'Meeting room two',
        'id' => 'YOUR_CALENDAR_ID_2@group.calendar.google.com',
        'size' => Room::SIZE_BIG,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ],
    [
        'name' => 'Meeting room three',
        'id' => 'YOUR_CALENDAR_ID_3@group.calendar.google.com',
        'size' => Room::SIZE_MEDIUM,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ],
    [
        'name' => 'Meeting room four',
        'id' => 'YOUR_CALENDAR_ID_4@group.calendar.google.com',
        'size' => Room::SIZE_SMALL,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ]
];

$rooms = [];

/**
 * Initialize room objects
 */
foreach ($meetingRooms as $meetingRoom) {
    $oneRoom = new Room();
    $oneRoom
            ->setId($meetingRoom['id'])
            ->setName($meetingRoom['name'])
            ->setSize($meetingRoom['size'])
            ->setResourceClass($meetingRoom['resourceClass']);
    $rooms[] = $oneRoom;
}

/**
 * Initialize an adapter for those rooms
 */
$roomResourceAdapter = new RoomResourceGoogleCalendar();
$roomResourceAdapter->setConfig([
    'applicationName' => 'FindARoom',
    'credentialsPath' => '/Users/tonymrakovcic/credentials/calendar-findaroom.json',
    'clientSecretPath' => '/Users/tonymrakovcic/credentials/client_secret.json',
    'scopes' => [\Google_Service_Calendar::CALENDAR],
    'accessType' => 'offline',
])->init();

/**
 * Initialize a room handler, and register the adapter
 */
$roomHandler = new RoomHandler();
$roomHandler->setRoomResourceAdapter($roomResourceAdapter);

/**
 * Add the rooms to the room handler
 */
$roomHandler->addRooms($rooms);

/**
 *
 * Find availability of all rooms
 */
$roomsAvailable = $roomHandler->getAllRoomsAvailability();

/**
 *  Find an available room, that wont be occupied in the next N minutes
 */
$availableRoomNow = $roomHandler->findAvailableRoom(RoomHandler::HOUR);

/**
 *  Find an available room, that wont be occupied in the next N minutes, and filter by size
 */
$roomsAvailable = $roomHandler->findAvailableBigRoom();

/**
 *  Reserve a room for you
 */
// $reservation = $roomHandler->reserveRoom($roomsAvailable[0]);
