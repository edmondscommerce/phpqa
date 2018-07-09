<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\assets\phpunitAnnotations\projectAllGood\tests\Medium;

use PHPUnit\Framework\TestCase;

class SomethingTest extends TestCase
{
    /**
     * @small
     * @test
     */
    public function itDoesSomething()
    {
    }

    /**
     * This method does not have the size annotation but it is not a test, so this should not cause any problem
     */
    public function methodThatIsNotATest()
    {

    }
}
