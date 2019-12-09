<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small\PHPUnit;

use EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function file_put_contents;

/**
 * Class CheckForLargeAndMediumAnnotationsTest.
 *
 * @coversDefaultClass \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
 *
 * @internal
 *
 * @small
 */
final class CheckAnnotationsTest extends TestCase
{
    /**
     * @var CheckAnnotations
     */
    private $checker;

    public function setUp(): void
    {
        $this->checker = new CheckAnnotations();
    }

    /**
     * @test
     * @covers ::main()
     * @small
     */
    public function itThrowAnExceptionIfTestsPathIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->checker->main('/invalid/path');
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itReturnsNoErrorsIfItsAllGood(): void
    {
        $pathToTestsDirectory = __DIR__ . '/../../assets/phpunitAnnotations/projectAllGood/tests';
        $expected             = [];
        $actual               = $this->checker->main($pathToTestsDirectory);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingSmallAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__ . '/../../assets/phpunitAnnotations/projectMissingSmall/tests';
        /* CS Fixer will add the tag automatically if the file is run through QA */
        $testFile      = $this->getSmallTestWithNoAnnotation();
        $smallTestName = $pathToTestsDirectory . '/Small/SomethingTest.php';
        file_put_contents($smallTestName, $testFile);

        $expected = [
            'SomethingTest.php' => [
                'Failed finding @small for method: itDoesSomething',
            ],
        ];
        $actual   = $this->checker->main($pathToTestsDirectory);
        /* Clean up the file once we are finished with it */
        unlink($smallTestName);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingMediumAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__ . '/../../assets/phpunitAnnotations/projectMissingMedium/tests';
        $expected             = [
            'SomethingTest.php' => [
                'Failed finding @medium for method: itDoesSomething',
            ],
        ];
        $actual               = $this->checker->main($pathToTestsDirectory);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingLargeAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__ . '/../../assets/phpunitAnnotations/projectMissingLarge/tests';
        $expected             = [
            'SomethingTest.php' => [
                'Failed finding @large for method: itDoesSomething',
                'Failed finding @large for method: testSomethingHappens',
            ],
        ];
        $actual               = $this->checker->main($pathToTestsDirectory);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itReturnsNoErrorsIfNotApplicableToProject(): void
    {
        $pathToTestsDirectory = __DIR__ . '/../../assets/phpunitAnnotations/projectNotApplicable/tests';
        $expected             = [];
        $actual               = $this->checker->main($pathToTestsDirectory);
        self::assertSame($expected, $actual);
    }

    /**
     * We need to be able to test that a file without a small annotation is picked up, however CS Fixer is adding the
     * small tag to any test that doesn't have a size annotation.
     *
     * I tried to put the entire class in the heredoc, but then the CheckAnnotations tool picked it up as a false
     * positive. Putting the class line into a var and the rest of the class in the here doc seems to work.
     */
    private function getSmallTestWithNoAnnotation(): string
    {
        $class = 'final class SomethingTest extends TestCase';

        return <<<PHP
<?php

declare(strict_types=1);

namespace EdmondsCommerce\\PHPQA\\Tests\\assets\\phpunitAnnotations\\projectMissingSmall\tests\\Small;

use PHPUnit\\Framework\\TestCase;

/**
 * @internal
 * @coversNothing
 */
{$class}
{
    /**
     * @smaalll
     * @test
     */
    public function itDoesSomething(): void
    {
    }
}
PHP;
    }
}
