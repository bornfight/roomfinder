<?php

namespace degordian\roomfinder;

/**
 * Class Room
 * @package degordian\RoomFinder
 */
class Room
{
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_BIG = 'big';
    const SIZE_DEFAULT = self::SIZE_MEDIUM;

    protected $name;
    protected $id;
    protected $size = self::SIZE_DEFAULT;
    protected $resourceClass;
    protected $busy = [];

    /**
     * @return mixed
     */
    public function getResourceClass()
    {
        return $this->resourceClass;
    }

    /**
     * @param mixed $resourceClass
     * @return Room
     */
    public function setResourceClass($resourceClass)
    {
        $this->resourceClass = $resourceClass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Room
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Room
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return Room
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param $busyInfo
     * @return $this
     */
    public function addBusyTime($busyInfo)
    {
        $this->busy[] = $busyInfo;
        return $this;
    }

    /**
     * @param int $addTime
     * @return bool
     */
    public function isBusy($addTime = 0)
    {
        $now = time();
        $busyTime = $now + $addTime;

        if (empty($this->busy)) {
            return false;
        }

        foreach ($this->busy as $busyInfo) {
            $busyFrom = strtotime($busyInfo['start']);
            $busyTo = strtotime($busyInfo['end']);
            if ($busyFrom < $busyTime && $busyTo > $busyTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isBusyNow()
    {
        return $this->isBusy();
    }

    /**
     * @return bool
     */
    public function isBusyNextFifteenMinutes()
    {
        return $this->isBusy(RoomHandler::FIFTEEN_MINUTES);
    }

    /**
     * @return bool
     */
    public function isBusyNextHalfHour()
    {
        return $this->isBusy(RoomHandler::HALF_HOUR);
    }

    /**
     * @return bool
     */
    public function isBusyNextHour()
    {
        return $this->isBusy(RoomHandler::HOUR);
    }
    /**
     * @return bool
     */
    public function isSmall()
    {
        return $this->getSize() === self::SIZE_SMALL;
    }

    /**
     * @return bool
     */
    public function isMedium()
    {
        return $this->getSize() === self::SIZE_MEDIUM;
    }

    /**
     * @return bool
     */
    public function isBig()
    {
        return $this->getSize() === self::SIZE_BIG;
    }
}
