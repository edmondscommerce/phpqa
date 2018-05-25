<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

/**
 * Class Psr4ValidatorTest
 *
 * @package EdmondsCommerce\PHPQA
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Psr4ValidatorTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testItFindsNoErrorsOnAValidProject()
    {
        $projectRoot = \realpath(__DIR__.'/assets/psr4/projectAllValid/');
        $validator   = new Psr4Validator(
            [],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual      = $validator->main();
        $expected    = [];
        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \Exception
     */
    public function testItCanHandleOddComposerConfigs()
    {
        $projectRoot = \realpath(__DIR__.'/assets/psr4/projectOddComposer/');
        $validator   = new Psr4Validator(
            [],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual      = $validator->main();
        $expected    = [];
        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \Exception
     */
    public function testItFindsErrorsAndThrowsAnExceptionOnAnInvalidProject()
    {
        $projectRoot = \realpath(__DIR__.'/assets/psr4/projectInValid/');
        $validator   = new Psr4Validator(
            ['%IgnoredStuff%'],
            $projectRoot,
            Helper::getComposerJsonDecoded($projectRoot.'/composer.json')
        );
        $actual      = $validator->main();
        $expected    = [
            'PSR-4 Errors:'  =>
                [
                    'In\\Valid\\' =>
                        [

                            [
                                'fileInfo'          => $projectRoot.'/src/Wrong.php',
                                'expectedNamespace' => 'In\\Valid',
                                'actualNamespace'   => 'Totally',
                            ],

                            [
                                'fileInfo'          => $projectRoot.'/src/Nested/Deep/Bad.php',
                                'expectedNamespace' => 'In\\Valid\\Nested\\Deep',
                                'actualNamespace'   => 'So',
                            ],
                        ],
                ],
            'Parse Errors:'  =>
                [
                    $projectRoot.'/tests/ParseError.php',
                ],
            'Ignored Files:' =>
                [
                    $projectRoot.'/src/IgnoredStuff/Ignored.php',
                    $projectRoot.'/src/IgnoredStuff/InvalidIgnored.php',
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
        ];
        $this->recursiveKeySort($expected);
        $this->recursiveKeySort($actual);
        $this->assertSame($expected, $actual);
    }

    private function recursiveKeySort(array &$array): bool
    {
        foreach ($array as &$value) {
            if (\is_array($value)) {
                $this->recursiveKeySort($value);
            }
        }

        return ksort($array);
    }
}
