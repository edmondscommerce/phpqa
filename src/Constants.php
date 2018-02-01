<?php declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;


class Constants
{
    /**
     * The key in $_SERVER that we check for in our PHPUnit tests
     */
    const QA_QUICK_TESTS_KEY = 'qaQuickTests';

    /**
     * The value for the key in $_SERVER that we check for in our PHPUnit tests. If equal to this value, we can skip
     * tests
     */
    const QA_QUICK_TESTS_ENABLED = 1;

}
