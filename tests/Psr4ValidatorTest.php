<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

use PHPUnit\Framework\TestCase;

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
        $errors      = $validator->main();
        $this->assertNotSame([], $errors);
        $expected =
            [
                'In\\Valid\\' =>
                    [
                        0 =>
                            [
                                'fileInfo'          =>
                                    '/var/www/vhosts/phpqa/tests/assets/psr4/projectInValid/src/Wrong.php',
                                'expectedNamespace' => 'In\\Valid',
                                'actualNamespace'   => 'Totally',
                            ],
                        1 =>
                            [
                                'fileInfo'          =>
                                    '/var/www/vhosts/phpqa/tests/assets/psr4/projectInValid/src/Nested/Deep/Bad.php',
                                'expectedNamespace' => 'In\\Valid\\Nested\\Deep',
                                'actualNamespace'   => 'So',
                            ],
                    ],

                'Parse Errors:'  =>
                    [
                        0 => '/var/www/vhosts/phpqa/tests/assets/psr4/projectInValid/tests/ParseError.php',
                    ],
                'Ignored Files:' =>
                    [
                        0 =>
                            '/var/www/vhosts/phpqa/tests/assets/psr4/projectInValid/src/IgnoredStuff/Ignored.php',
                        1 =>
                            '/var/www/vhosts/phpqa/tests/assets/psr4/projectInValid/src/IgnoredStuff/InvalidIgnored.php'
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
',
                    ],
            ];
        $actual   = $errors['PSR-4 Errors:'];
        $this->assertSame($expected, $actual);

    }
}
