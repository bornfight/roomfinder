# Room resource managment, finding and reserving available meeting rooms
<br />
A php wrapper around something that can handle rooms and their availability(google calendar for instance)
<br>

# How to install (Composer)

    composer require "degordian/roomfinder":"dev-master"
    
or add to your project's composer.json

    "require": {
        "degordian/roomfinder": "*"
    }

# How to use
First you have to atuthenticate yourself for using google calendar for instance
<br>
A google calendar example is in examples/auth
<br>
You can add new adapters for room resourcing, currently only Google Calendar is supported


Create a google app here:<br>
https://console.developers.google.com/apis/credentials/oauthclient
<br>
Export the client_secret.json file
<br>
Save it to disk, and paste the path to the file into ```$clientSecretPath```
<br>
For ```$credentialsPath```, use the location on your disk where you want the auth.php script to create your auth file
<br>
Through the command line, call 

```
   php examples/auth.php
```
This will serve you a  google link to authenticate yourself and create a credential file you can then use in your project
<br>

Using that file you can now you can initialize an adapter for those rooms
```php
$roomResourceAdapter = new RoomResourceGoogleCalendar();
$roomResourceAdapter->setConfig([
    'applicationName' => 'FindARoom',
    'credentialsPath' => '/Users/tonymrakovcic/credentials/calendar-findaroom.json',
    'clientSecretPath' => '/Users/tonymrakovcic/credentials/client_secret.json',
    'scopes' => [\Google_Service_Calendar::CALENDAR],
    'accessType' => 'offline',
])->init();

```
<br>
Initialize a room handler, and register the adapter

```php
$roomHandler = new RoomHandler();
$roomHandler->setRoomResourceAdapter($roomResourceAdapter);
```

<br>
Create a room from some data

```php
$rooms  = [
    [
        'name' => 'Meeting room one',
        'id' => 'YOUR_CALENDAR_ID_1@group.calendar.google.com',
        'size' => Room::SIZE_BIG,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ],
    [
        'name' => 'Meeting room two',
        'id' => 'YOUR_CALENDAR_ID_2@group.calendar.google.com',
        'size' => Room::SIZE_MEDIUM,
        'resourceClass' => RoomResourceGoogleCalendar::class,
    ]
];
```


<br>
Add the rooms to the room handler

```php
$roomHandler->addRooms($rooms);
```

<br>
Find the availability of all rooms

```php
$roomsAvailable = $roomHandler->getAllRoomsAvailability();
```

<br>
Find an availble room, that wont be occupied in the next x minutes

```php
$availableRoomNow = $roomHandler->findAvailableRoom(RoomHandler::HOUR);
```

<br>
Find an available room, that wont be occupied in the next x minutes, and filter by size

```php
$roomsAvailable = $roomHandler->findAvailableBigRoom();
```

<br>

Reserve a room in the calendar

```php
$reservation = $roomHandler->reserveRoom($roomsAvailable[0]);
```


# Contribute

Contributions and comments are more than welcome :) <br />

# Questions, problems?

We will do our best to answer all issues

# License
[MIT License](LICENSE)
