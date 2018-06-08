<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small\PHPUnit;

use EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations;
use PHPUnit\Framework\TestCase;

/**
 * Class CheckForLargeAndMediumAnnotationsTest
 *
 * @coversDefaultClass \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
 *
 * @package EdmondsCommerce\PHPQA\Tests\Large\PHPUnit
 */
class CheckAnnotationsTest extends TestCase
{
    /**
     * @var CheckAnnotations
     */
    private $checker;

    public function setup(): void
    {
        $this->checker = new CheckAnnotations();
    }

    /**
     * @test
     * @covers ::main()
     * @small
     */
    public function itThrowAnExceptionIfTestsPathIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->checker->main('/invalid/path');
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itReturnsNoErrorsIfItsAllGood(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectAllGood/tests';
        $expected             = [];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingSmallAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectMissingSmall/tests';
        $expected             = [
            'SomethingTest.php' => [
                'Failed finding @small for method: itDoesSomething',
            ],
        ];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingMediumAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectMissingMedium/tests';
        $expected             = [
            'SomethingTest.php' => [
                'Failed finding @medium for method: itDoesSomething',
            ],
        ];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itFindsMissingLargeAnnotations(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectMissingLarge/tests';
        $expected             = [
            'SomethingTest.php' => [
                'Failed finding @large for method: itDoesSomething',
            ],
        ];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckAnnotations
     * @small
     */
    public function itReturnsNoErrorsIfNotApplicableToProject(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectNotApplicable/tests';
        $expected             = [];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }
}
