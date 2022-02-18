<?php

namespace Keruald\OmniTools\Tests\OS;

use Keruald\OmniTools\OS\CurrentProcess;
use PHPUnit\Framework\TestCase;

class CurrentProcessTest extends TestCase {

    // Probably more usernames are valid, but why tests
    // would run in accounts using spaces or UTF-8 emojis?
    const USERNAME_REGEXP = "/^([a-zA-Z][a-zA-Z0-9_]*)$/";

    public function testGetUsername () {
        $actual = CurrentProcess::getUsername();

        $this->assertMatchesRegularExpression(self::USERNAME_REGEXP, $actual);
    }

}
