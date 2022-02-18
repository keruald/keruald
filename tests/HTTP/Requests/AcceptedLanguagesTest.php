<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\HTTP\Requests;

use Keruald\OmniTools\HTTP\Requests\AcceptedLanguages;
use PHPUnit\Framework\TestCase;

class AcceptedLanguagesTest extends TestCase {

    /**
     * @var AcceptedLanguages
     */
    private $languages;

    protected function setUp () : void {
        $this->languages = new AcceptedLanguages("en-US,en;q=0.9,fr;q=0.8");
    }

    public function testExtractFromHeaders () : void {
        $this->assertEquals("", AcceptedLanguages::extractFromHeaders());

        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "de";
        $this->assertEquals("de", AcceptedLanguages::extractFromHeaders());
    }

    public function testFromServer () : void {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = "de";
        $languages = AcceptedLanguages::fromServer();

        $this->assertEquals(["de"], $languages->getLanguageCodes());
    }

    public function testGetLanguageCodes () : void {
        $this->assertEquals(
            ["en-US", "en", "fr"],
            $this->languages->getLanguageCodes()
        );
    }

    public function testGetLanguageCodesWithBlankInformation () : void {
        $languages = new AcceptedLanguages;

        $this->assertEquals([], $languages->getLanguageCodes());
    }

}
