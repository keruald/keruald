<?php

namespace Keruald\OmniTools\DateTime;

class TimeRange {

    private Time $start;
    private Time $end;

    ///
    /// Constructors
    ///

    public function __construct (Time $start, Time $end) {
        $this->start = $start;
        $this->end = $end;

        $this->normalize();
    }

    public static function fromDuration (Time $start,
                                         int $hoursToAdd = 0,
                                         int $minutesToAdd = 0) : self {
        $end = clone $start;
        $end
            ->addHours($hoursToAdd)
            ->addMinutes($minutesToAdd);

        return new self($start, $end);
    }

    public static function parse (string $expression) : self {
        $range = explode("-", $expression);

        if (count($range) !== 2) {
            throw new \InvalidArgumentException;
        }

        return new self(Time::Parse($range[0]), Time::Parse($range[1]));
    }

    ///
    /// Getters and setters
    ///

    public function getStart () : Time {
        return $this->start;
    }

    public function setStart (Time $start) : self {
        $this->start = $start;

        return $this->normalize();
    }

    public function getEnd () : Time {
        return $this->end;
    }

    public function setEnd (Time $end) : self {
        $this->end = $end;

        return $this->normalize();
    }

    ///
    /// Helper functions
    ///

    public function normalize () : self {
        if ($this->start->compareTo($this->end) > 0) {
            $swap = $this->end;
            $this->end = $this->start;
            $this->start = $swap;
        }

        return $this;
    }

    public function overlapsWith (TimeRange $other) : bool {
        return $this->getEnd()->compareTo($other->getStart()) == 1
               &&
               $other->getEnd()->compareTo($this->getStart()) == 1;
    }

    public function countMinutes () : int {
        return $this->end->toMinutes() - $this->start->toMinutes();
    }

}
