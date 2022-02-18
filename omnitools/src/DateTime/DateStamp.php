<?php
declare(strict_types=1);

namespace Keruald\OmniTools\DateTime;

use Keruald\OmniTools\Collections\Vector;

use DateTime;
use InvalidArgumentException;

class DateStamp {

    ///
    /// Private members
    ///

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
     */
    private $day;

    ///
    /// Constructors
    ///

    public function __construct (int $year, int $month, int $day) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public static function fromUnixTime (?int $unixtime = null) : self {
        $dateStamp = date('Y-m-d', $unixtime ?? time());
        return self::parse($dateStamp);
    }

    public static function parse (string $date) : self {
        if (preg_match("/^[0-9]{4}\-[0-1][0-9]\-[0-3][0-9]$/", $date)) {
            // YYYY-MM-DD
            $args = Vector::explode("-", $date)
                        ->toIntegers()
                        ->toArray();

            return new DateStamp(...$args);
        }

        if (preg_match("/^[0-9]{4}[0-1][0-9][0-3][0-9]$/", $date)) {
            // YYYYMMDD
            return new DateStamp(
                (int)substr($date, 0, 4), // YYYY
                (int)substr($date, 4, 2), // MM
                (int)substr($date, 6, 2)  // DD
            );
        }

        throw new InvalidArgumentException("YYYYMMDD or YYYY-MM-DD format expected, $date received.");
    }

    ///
    /// Convert methods
    ///

    public function toUnixTime () : int {
        return mktime(0, 0, 0, $this->month, $this->day, $this->year);
    }

    public function toDateTime () : DateTime {
        return new DateTime($this->__toString());
    }

    public function toShortString () : string {
        return date('Ymd', $this->toUnixTime());
    }

    public function __toString () : string {
        return date('Y-m-d', $this->toUnixTime());
    }

}
