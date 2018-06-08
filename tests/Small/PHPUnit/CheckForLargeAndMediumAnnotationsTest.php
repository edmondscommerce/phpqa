<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Large\PHPUnit;

use EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations;
use PHPUnit\Framework\TestCase;

/**
 * Class CheckForLargeAndMediumAnnotationsTest
 *
 * @coversDefaultClass \EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations
 *
 * @package EdmondsCommerce\PHPQA\Tests\Large\PHPUnit
 */
class CheckForLargeAndMediumAnnotationsTest extends TestCase
{
    /**
     * @var CheckForLargeAndMediumAnnotations
     */
    private $checker;

    public function setup(): void
    {
        $this->checker = new CheckForLargeAndMediumAnnotations();
    }

    /**
     * @test
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations
     * @large
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
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations
     * @large
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
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations
     * @large
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
     * @covers \EdmondsCommerce\PHPQA\PHPUnit\CheckForLargeAndMediumAnnotations
     * @large
     */
    public function itReturnsNoErrorsIfNotApplicableToProject(): void
    {
        $pathToTestsDirectory = __DIR__.'/../../assets/phpunitAnnotations/projectNotApplicable/tests';
        $expected             = [];
        $actual               = $this->checker->main($pathToTestsDirectory);
        $this->assertSame($expected, $actual);
    }


}
