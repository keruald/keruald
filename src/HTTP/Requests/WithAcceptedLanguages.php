<?php
declare(strict_types=1);


namespace Keruald\OmniTools\HTTP\Requests;


trait WithAcceptedLanguages {

    /**
     * Gets the languages accepted by the browser, by order of priority.
     *
     * This will read the HTTP_ACCEPT_LANGUAGE variable sent by the browser in the
     * HTTP request.
     *
     * @return string[] each item a language accepted by browser
     */
    public static function getAcceptedLanguages () : array {
        return AcceptedLanguages::fromServer()->getLanguageCodes();
    }

}
