<?php

namespace degordian\roomfinder\Adapters;

use degordian\roomfinder\RoomCollection;
use degordian\roomfinder\Room;

/**
 * Class RoomResourceGoogleCalendar
 * @package degordian
 */
class RoomResourceGoogleCalendar implements RoomResourceAdapterInterface
{
    const DEFAULT_TIME_ZONE = 'Europe/Zagreb';

    private $config = [];

    /**
     * @var \Google_Service_Calendar
     */
    private $service;

    /**
     * @var \Google_Client
     */
    private $client;

    private $userPrimaryCalendarId = null;
    protected $timeFrameFrom = null;
    protected $timeFrameTo = null;
    protected $calendars = [];
    protected $roomIds = [];
    protected $timeZone = self::DEFAULT_TIME_ZONE;

    /**
     * @var RoomCollection
     */
    protected $roomCollection;

    /**
     *
     */
    public function init()
    {
        $this->setService();
        $this->authenticate();
        $this->setUserPrimaryCalendarId();
    }

    /**
     *
     */
    public function setService()
    {
        $this->service = new \Google_Service_Calendar($this->getClient());
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
        return self::DEFAULT_TIME_ZONE;
    }


    /**
     * @return mixed|null
     */
    protected function getActiveAuthentication()
    {
        if (isset($this->config['credentialsPath'])) {
            $accessToken = json_decode(file_get_contents($this->config['credentialsPath']), true);
        } else {
            $accessToken = null;
        }
        return $accessToken;
    }

    /**
     *
     */
    protected function refreshAuthentication()
    {
        $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        file_put_contents($this->config['credentialsPath'], json_encode($this->client->getAccessToken()));
    }

    /**
     * @return bool
     */
    protected function authenticationExpired()
    {
        if ($this->client->isAccessTokenExpired()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    protected function credentialsExists()
    {
        if (isset($this->config['credentialsPath']) && file_exists($this->config['credentialsPath'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    private function getCode()
    {
        return trim(fgets(STDIN));
    }

    /**
     * @param $accessToken
     * @return bool|int
     */
    private function saveAuthentication($accessToken)
    {
        if (!file_exists(dirname($this->config['credentialsPath']))) {
            mkdir(dirname($this->config['credentialsPath']), 0700, true);
        }
        return file_put_contents($this->config['credentialsPath'], json_encode($accessToken));
    }

    /**
     * @return bool|int
     */
    public function createAuthentication()
    {
        $this->getClient();
        $authUrl = $this->client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = $this->getCode();

        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
        return $this->saveAuthentication($accessToken);
    }


    /**
     * @throws \Exception
     */
    private function authenticate()
    {
        if ($this->credentialsExists()) {
            $accessToken = $this->getActiveAuthentication();
        } else {
            throw new \Exception('Credentials file does not exist');
        }

        $this->client->setAccessToken($accessToken);

        if ($this->authenticationExpired()) {
            $this->refreshAuthentication();
        }
    }

    /**
     *
     */
    private function setupClient()
    {
        $this->client = new \Google_Client();
        if (isset($this->config['applicationName'])) {
            $this->client->setApplicationName($this->config['applicationName']);
        }
        if (isset($this->config['scopes'])) {
            $this->client->setScopes($this->config['scopes']);
        }
        if (isset($this->config['clientSecretPath'])) {
            $this->client->setAuthConfig($this->config['clientSecretPath']);
        }
        if (isset($this->config['accessType'])) {
            $this->client->setAccessType($this->config['accessType']);
        }
    }

    /**
     * @return \Google_Client
     */
    private function getClient()
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $this->setupClient();

        return $this->client;
    }


    /**
     * @return $this
     */
    public function setUserPrimaryCalendarId()
    {
        if ($this->userPrimaryCalendarId === null) {
            $this->userPrimaryCalendarId = $this->getService()->calendars->get('primary')->getId();
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserPrimaryCalendarId()
    {
        return $this->userPrimaryCalendarId;
    }

    /**
     * @return \Google_Service_Calendar|null
     */
    public function getService()
    {
        if ($this->service === null) {
            $this->setService();
        }
        return $this->service;
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
        return $this;
    }

    /**
     * @return array
     */
    public function getTimeFrame()
    {
        if ($this->timeFrameFrom === null) {
            $from = date('c', strtotime('today'));
        } else {
            $from = $this->timeFrameFrom;
        }
        if ($this->timeFrameTo === null) {
            $to = date('c', strtotime('tomorrow'));
        } else {
            $to =  $this->timeFrameTo;
        }

        $timeFrame = [
            'from' => $from,
            'to' => $to,
        ];

        return $timeFrame;
    }


    /**
     * @return RoomCollection
     */
    public function getRoomCollection()
    {
        return $this->roomCollection;
    }

    /**
     * @param RoomCollection $rooms
     * @return $this
     */
    public function setRoomCollection(RoomCollection $rooms)
    {
        $this->roomCollection = $rooms;
        return $this;
    }

    /**
     * @param $items
     * @return array
     */
    private function formatItems($items)
    {
        $returnArray = [];
        foreach ($items as $item) {
            $returnArray[]['id'] = $item;
        }
        return $returnArray;
    }

    /**
     * @return RoomCollection
     */
    public function getAllRoomsAvailability()
    {
        $timeFrame = $this->getTimeFrame();

        $query = [
            'timeMin' => $timeFrame['from'],
            'timeMax' => $timeFrame['to'],
            'items' => $this->formatItems($this->getRoomCollection()->getAllRoomIds())
        ];

        $ask = new \Google_Service_Calendar_FreeBusyRequest($query);

        $calendars = $this->getService()->freebusy->query($ask)->getCalendars();

        if (empty($this->calendars)) {
            $this->calendars = [];
        }

        $this->calendars = $calendars;

        foreach ($this->calendars as $room => $calendar) {
            $roomObject = $this->getRoomCollection()->getRoom($room);
            foreach ((array)$calendar->busy as $meeting) {
                $roomObject->addBusyTime([
                    'start' => $meeting['start'],
                    'end' => $meeting['end'],
                ]);
            }
        }

        return $this->getRoomCollection();
    }

    /**
     * @param Room $room
     * @param int $length
     * @param string $summary
     * @return \Google_Service_Calendar_Event
     */
    public function reserveRoom($room, $length = self::DEFAULT_MEET_LENGTH, $summary = self::DEFAULT_SUMMARY)
    {
        $event = new \Google_Service_Calendar_Event([
            'summary' => $summary,
            'start' => [
                'dateTime' => date('c'),
                'timeZone' => $this->getTimeZone(),
            ],
            'end' => [
                'dateTime' => date('c', time() + $length),
                'timeZone' =>  $this->getTimeZone(),
            ],
            'attendees' => [
                ['email' => $this->getUserPrimaryCalendarId()],
            ]
        ]);

        $event = $this->getService()->events->insert($room->getId(), $event);
        return $event;
    }
}
