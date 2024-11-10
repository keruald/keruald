<?php
declare(strict_types=1);

namespace Keruald\OmniTools\DateTime;

use Keruald\OmniTools\Collections\Comparable;

class Time implements Comparable {

    private int $hours;
    private int $minutes;

    ///
    /// Constants
    ///

    const MAX_HOURS = 24;

    const MAX_MINUTES = 24 * 60;

    ///
    /// Constructors
    ///

    public function __construct (int $hours = 0, int $minutes = 0) {
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    public static function fromMinutes (int $minutes) : self {
        if ($minutes < 0 || $minutes >= self::MAX_MINUTES) {
            throw new \OutOfRangeException;
        }

        return (new Time)
            ->setHours((int)($minutes / 60))
            ->setMinutes($minutes % 60);
    }

    public static function parse (string $expression) : self {
        $range = explode(":", $expression);

        if (count($range) < 2 || count($range) > 3) {
            throw new \InvalidArgumentException;
        }

        return new self((int)$range[0], (int)$range[1]);
    }

    ///
    /// Getters and setters
    ///

    public function getHours () : int {
        return $this->hours;
    }

    public function setHours (int $hours) : self {
        $this->hours = $hours;

        return $this;
    }

    public function getMinutes () : int {
        return $this->minutes;
    }

    public function setMinutes (int $minutes) : self {
        $this->minutes = $minutes;

        return $this;
    }

    ///
    /// Helper methods
    ///

    public function addMinutes (int $minutes) : self {
        $totalMinutes = $this->toMinutes() + $minutes;

        if ($totalMinutes < 0 || $totalMinutes >= self::MAX_MINUTES) {
            throw new \OutOfRangeException;
        }

        $this->setHours((int)($totalMinutes / 60))
             ->setMinutes($totalMinutes % 60);

        return $this;
    }

    public function addHours (int $hours) : self {
        $totalHours = $this->hours + $hours;

        if ($totalHours >= self::MAX_HOURS) {
            throw new \OutOfRangeException;
        }

        $this->setHours($totalHours);

        return $this;
    }

    public function toMinutes () : int {
        return $this->hours * 60 + $this->minutes;
    }

    public function __toString () : string {
        return sprintf("%02d:%02d", $this->hours, $this->minutes);
    }

    ///
    /// Comparable
    ///

    public function compareTo (object $other) : int {
        if (!$other instanceof Time) {
            throw new \InvalidArgumentException;
        }

        return $this->toMinutes() <=> $other->toMinutes();
    }

}
