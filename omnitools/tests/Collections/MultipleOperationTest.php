<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Collections;

use Keruald\OmniTools\Collections\MultipleOperation;

use PHPUnit\Framework\TestCase;

class MultipleOperationTest extends TestCase {

    public function testIsOkAtFirst () {
        $operation = new MultipleOperation([]);
        $this->assertTrue($operation->isOk());
    }

    public function testIsOkWhenItIs () {
        $operation = new MultipleOperation([1, 2, 3]);

        $operation->apply(fn () => true);

        $this->assertTrue($operation->isOk());
    }

    public function testIsOkWhenItIsNever () {
        $operation = new MultipleOperation([1, 2, 3]);

        $operation->apply(fn () => false);
        $this->assertFalse($operation->isOk());
    }

    public function testIsOkWhenItIsNot () {
        $operation = new MultipleOperation([1, 2, 3]);

        $fn = fn($n) => match ($n) {
            2 => false, // second operation will fail
            default => true,
        };

        $operation->apply($fn);
        $this->assertFalse($operation->isOk());
    }

    public function testApply () {
        $toProcess = [1, 2, 3];
        $processed = [];

        $fn = function ($n) use (&$processed) {
            $processed[] = $n;

            return true;
        };

        $operation = new MultipleOperation($toProcess);
        $operation->apply($fn);
        $this->assertEquals([1, 2, 3], $processed);
    }

    public function testDo () {
        $toProcess = [1, 2, 3];
        $processed = [];

        $fn = function ($n) use (&$processed) {
            if ($n == 2) {
                // second operation will fail
                return false;
            }

            $processed[] = $n;
            return true;
        };

        $result = MultipleOperation::do($toProcess, $fn);
        $this->assertFalse($result);
        $this->assertEquals([1, 3], $processed);
    }

}
