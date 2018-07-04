<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA\Tests\Small;

use EdmondsCommerce\PHPQA\Helper;
use EdmondsCommerce\PHPQA\Psr4Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class Psr4ValidatorTest
 *
 * @package EdmondsCommerce\PHPQA
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @coversDefaultClass \EdmondsCommerce\PHPQA\Psr4Validator
 */
class Psr4ValidatorTest extends TestCase
{
    /**
     * @throws \Exception
     * @covers \EdmondsCommerce\PHPQA\Psr4Validator
     * @uses   \EdmondsCommerce\PHPQA\Helper::getComposerJsonDecoded
     * @small
     */
    public function testItFindsNoErrorsOnAValidProject(): void
    {
        $assetsPath  = __DIR__.'/../assets/psr4/projectAllValid/';
        $projectRoot = \realpath($assetsPath);
        if (false === $projectRoot) {
            self::fail('failed getting realpath to '.$assetsPath);
        }
        $validator = new Psr4Validator(
            [],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual    = $validator->main();
        $expected  = [];
        self::assertSame($expected, $actual);
    }

    /**
     * @throws \Exception
     * @covers \EdmondsCommerce\PHPQA\Psr4Validator
     * @uses   \EdmondsCommerce\PHPQA\Helper::getComposerJsonDecoded
     * @small
     */
    public function testItCanHandleOddComposerConfigs(): void
    {
        $assetsPath  = __DIR__.'/../assets/psr4/projectOddComposer/';
        $projectRoot = \realpath($assetsPath);
        if (false === $projectRoot) {
            self::fail('failed getting realpath to '.$assetsPath);
        }
        $validator = new Psr4Validator(
            [],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual    = $validator->main();
        $expected  = [];
        self::assertSame($expected, $actual);
    }

    /**
     * @throws \Exception
     * @covers \EdmondsCommerce\PHPQA\Psr4Validator
     * @uses   \EdmondsCommerce\PHPQA\Helper::getComposerJsonDecoded()
     * @small
     */
    public function testItFindsErrorsAndThrowsAnExceptionOnAnInvalidProject(): void
    {
        $assetsPath  = __DIR__.'/../assets/psr4/projectInValid/';
        $projectRoot = \realpath($assetsPath);
        if (false === $projectRoot) {
            self::fail('failed getting realpath to '.$assetsPath);
        }
        $validator = new Psr4Validator(
            ['%IgnoredStuff%'],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual    = $validator->main();
        $expected  = [
            'PSR-4 Errors:'  =>
                [
                    'In\\Valid\\' =>
                        [
                            0 =>
                                [
                                    'fileInfo'          => $projectRoot.'/src/Nested/Deep/Bad.php',
                                    'expectedNamespace' => 'In\\Valid\\Nested\\Deep',
                                    'actualNamespace'   => 'So',
                                ],
                            1 =>
                                [
                                    'fileInfo'          => $projectRoot.'/src/Wrong.php',
                                    'expectedNamespace' => 'In\\Valid',
                                    'actualNamespace'   => 'Totally',
                                ],
                        ],
                ],
            'Parse Errors:'  =>
                [
                    0 => $projectRoot.'/tests/ParseError.php',
                ],
            'Missing Paths:' =>
                [
                    'missing/path'         => 'Namespace root \'In\\Valid\\\'
contains a path \'missing/path\'\'
which doesn\'t exist
',
                    'missing/magento/path' => 'Namespace root \'In\\Valid\\\'
contains a path \'missing/magento/path\'\'
which doesn\'t exist
Magento\'s composer includes this by default, it should be removed from the psr-4 section',
                ],
            'Ignored Files:' =>
                [
                    0 => $projectRoot.'/src/IgnoredStuff/Ignored.php',
                    1 => $projectRoot.'/src/IgnoredStuff/InvalidIgnored.php',
                ],
        ];

        self::assertEquals($expected, $actual);
    }
}
