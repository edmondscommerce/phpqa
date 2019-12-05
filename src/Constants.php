<?php

declare(strict_types=1);

namespace EdmondsCommerce\PHPQA;

final class Constants
{
    /**
     * The key in $_SERVER that we check for in our PHPUnit tests.
     */
    public const QA_QUICK_TESTS_KEY = 'phpUnitQuickTests';

    /**
     * The value for the key in $_SERVER that we check for in our PHPUnit tests. If equal to this value, we can skip
     * tests.
     */
    public const QA_QUICK_TESTS_ENABLED = 1;
}
