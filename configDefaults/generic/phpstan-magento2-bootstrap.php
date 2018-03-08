<?php

use Symfony\Component\Console\Helper\Table;

/* Find bootstrap path */
$rootPath = realpath(dirname(__FILE__));
while (!file_exists($rootPath . '/app/bootstrap.php') || $rootPath == '/') {
    $rootPath = realpath(dirname($rootPath));
}

/* Include Magento bootstrap file */
require_once $rootPath . '/app/bootstrap.php';

/* Create git hook class autoloader */
$_git_hook_loaded_class = [];
function phpstan_magento2_autoloader($class) {
    global $_git_hook_loaded_class;
    if(isset($_git_hook_loaded_class[$class])) {
        return $_git_hook_loaded_class[$class];
    }

    try {
        /* Get Magento ObjectManager */
        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
        $objectManager  = $bootstrap->getObjectManager();

        $objectManager->get($class);
        $_git_hook_loaded_class[$class] = true;
    } catch(\Exception $e) {
        $_git_hook_loaded_class[$class] = false;
    }

    return $_git_hook_loaded_class[$class];
}
spl_autoload_register('phpstan_magento2_autoloader');

/**
 * Need to avoid older versions of modules triggering errors
 */
try {
    Table::getStyleDefinition('symfony-style-guide');
} catch (InvalidArgumentException $e) {
    $default = Table::getStyleDefinition('default');
    Table::setStyleDefinition('symfony-style-guide', $default);
}
