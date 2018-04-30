<?php
declare(strict_types=1);

namespace Keruald\OmniTools\HTTP\Requests;

use Keruald\OmniTools\Collections\WeightedList;

class AcceptedLanguages {

    /**
     * @var string
     */
    private $acceptedLanguages;

    ///
    /// Constructor
    ///

    public function __construct (string $acceptedLanguages = '') {
        $this->acceptedLanguages = $acceptedLanguages;
    }

    public static function fromServer () : self {
        return new self(self::extractFromHeaders());
    }

    ///
    /// Helper methods to determine the languages
    ///

    public static function extractFromHeaders () : string {
        return $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "";
    }

    public function getLanguageCodes () : array {
        return WeightedList::parse($this->acceptedLanguages)
                           ->toSortedArray();
    }

}
