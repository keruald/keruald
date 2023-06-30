<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Collections\ArrayUtilities;
use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\DateTime\DateStamp;
use Keruald\OmniTools\HTTP\Requests\Request;
use Keruald\OmniTools\Network\IPv4Range;
use Keruald\OmniTools\Reflection\CodeClass;

use PHPUnit\Framework\TestCase;

use InvalidArgumentException;

class CodeClassTest extends TestCase {

    private CodeClass $class;

    protected function setUp () : void {
        $this->class = new CodeClass(AcmeApplication::class);
    }

    public function testFrom () {
        $date = new DateStamp(2010, 11, 25);
        $class = CodeClass::from($date);

        $this->assertInstanceOf(CodeClass::class, $class);
        $this->assertEquals(DateStamp::class, $class->getClassName());
    }

    public function testGetClassName () {
        $this->assertEquals(
            AcmeApplication::class,
            $this->class->getClassName(),
        );
    }

    public function testGetShortClassName () {
        $this->assertEquals(
            "AcmeApplication",
            $this->class->getShortClassName(),
        );
    }

    public function testNewInstanceFromServices () {
        $services = [
            // Some objects (in different order than the constructor)
            "request" => new Request(),
            "date" => DateStamp::fromUnixTime(), // another name than in class
            "session" => new HashMap(),

            // Scalar values
            "counter" => 666,
            "isSecure" => false,
            "temperature" => 26.6,
            "inventory" => [],

            // An object not needed by the controller
            "ip_range" => IPv4Range::from("127.0.0.1/32"),
        ];

        $app = $this->class->newInstanceFromServices($services);

        $this->assertInstanceOf(AcmeApplication::class, $app);
        $this->assertEquals($services["date"], $app->getDateStamp());
    }

    public function testNewInstanceFromServicesWithMissingService () {
        $incompleteServices = [
            "foo",
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->class->newInstanceFromServices($incompleteServices);
    }

    public function testNewInstanceFromServicesWithoutConstructor () {
        $services = [
            "foo",
        ];

        $class = new CodeClass(ArrayUtilities::class); // No constructor
        $utilities = $class->newInstanceFromServices($services);

        $this->assertInstanceOf(ArrayUtilities::class, $utilities);
    }

    public function testGetConstructorArgumentsTypes () {
        $expected = Vector::from([
            Request::class,
            HashMap::class,
            DateStamp::class,
            "int",
            "array",
            "float",
            "bool",
        ]);

        $actual = $this->class->getConstructorArgumentsTypes();
        $this->assertEquals($expected, $actual);
    }

    public function testGetConstructorArgumentsTypesWhenNotExisting () {
        $class = new CodeClass(ArrayUtilities::class); // No constructor

        $this->assertEquals(new Vector, $class->getConstructorArgumentsTypes());
    }

    public function testGetConstructor () {
        $constructor = $this->class->getConstructor();

        $this->assertEquals("__construct", $constructor->getName());
        $this->assertEquals(
            AcmeApplication::class,
            $constructor->getDeclaringClass()->getName()
        );
    }

    public function testGetConstructorWhenNotExisting () {
        $this->expectException(InvalidArgumentException::class);

        $class = new CodeClass(ArrayUtilities::class); // No constructor
        $class->getConstructor();
    }

}
