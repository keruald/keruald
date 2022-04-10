<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings;

use Keruald\OmniTools\Strings\SorensenDiceCoefficient;
use PHPUnit\Framework\TestCase;

class SorensenDiceCoefficientTest extends TestCase {

    public function testCoefficient () : void {
        $actual = new SorensenDiceCoefficient('night', 'nacht');

        $this->assertEquals(0.25, $actual->compute());
    }

    public function testComputeFor () : void {
        $score = SorensenDiceCoefficient::computeFor('night', 'nacht');

        $this->assertGreaterThan(0, $score);
        $this->assertLessThan(1, $score);
    }

}
