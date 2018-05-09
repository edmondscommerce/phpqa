## Key config folders

- In the phpqa folder
    - `configDefaults/` contains subfolders specific to each platform
        - `generic/` for config files not speific to any platform
        - `magento2-project/` for config files specific to an entire Magento 2 codebase
        - `magento2-module/` for config files specific to a single Magento 2 module
- In your project's folder
    - `qaConfig/` can be created to override the config in `configDefaults`

### Config Folders

PHPQA's `configDefaults/generic` folder contains a config file for each tool run by phpqa. At the moment this is

- `phpmd/ruleset.xml`
- `phpstan.neon`
- `phpstan-magento2-bootstrap.php`
- `phpunit.xml`
- `phpunit-with-coverage.xml`

#### Config overrides

If no local config file exists in your project's `qaConfig` folder, phpqa will detect what type of platform you're on and use the config in its own `configDefaults/` folder.

As an example, when running phpstan on a Magento 2 codebase, PHPQA will check for its config in the following order:

1. Your project's root `qaConfig/phpstan.neon`
2. PHPQA's root `configDefaults/magento2-project/phpstan.neon`
3. PHPQA's root `configDefaults/generic/phpstan.neon`

To create your own override, you can:

1. Make a directory in your project root called `qaConfig`
2. Copy the configuration from [/configDefaults](/configDefaults) into your project `qaConfig` folder.
3. Customise the copied config as you see fit

For example, for PHPStan, we would do this:

```bash
#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy in the default config
platform="generic" # See vendor/edmondscommerce/phpqa/configDefaults/ for options
cp vendor/edmondscommerce/phpqa/configDefaults/${platform}/phpstan.neon qaConfig

#Edit the config
vim qaConfig/phpstan.neon
```

For PHP Mess Detector, we would do this:

```bash
#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy the phpmd folder as a whole
platform="generic" # See vendor/edmondscommerce/phpqa/configDefaults/ for options
cp vendor/edmondscommerce/phpqa/configDefaults/${platform}/phpmd/ qaConfig/ -r


```

### Global Configuration Override

If you want to make more wholesale tweaks to `qa` customisation, you can do this:

```bash

#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy in the default config
awk '/#### CONFIG /,/#### PROCESS /' bin/qa > qaConfig/qaConfig.inc.bash

#Edit the config
vim qaConfig/qaConfig.inc.bash

```

When you run `qa`, it checks for a file located in `"$projectConfigPath/qaConfig.inc.bash"` and will include it if found. 

Using this you can override as much of the standard configuration as you see fit.

