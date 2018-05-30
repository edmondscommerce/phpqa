# PHPQA Configuration

There are multiple ways to configure PHPQA and the component tools

The general strategy with PHPQA is that, there is a extensive and relatively sensible default configuration. This means that in your project you should hopefully only need to make minor adjustments to the configuration by overriding it.

## qaConfig Folder

A really important concept with phpqa is that there should be a directory in your project root called `qaConfig`. This folder is used as much as possible to contain all of your QA and CI configuration and is where you store your project specific configuration and hooks.

## Environment Variables

PHPQA makes extensive use of bash environment variables for configuration.

There are three ways you can generally set these environment variables:

1. Export them in your BASH session

```bash
export environmentVariable="value"

./bin/qa
```

2. Set them inline when running phpqa
```bash
environmentVariable="value" bin/qa
```

3. Export them as part of your CI script
```bash
#ci.bash
export CI=true
bin/qa
```

Here are some general PHPQA environment variables you might want to set:

##### Quick tests only:
 `phpqaQuickTests`
 
 If you want to run only fast PHPQA tests

##### CI Mode:
 `CI`
 
 will not prompt for user input

##### Skip uncommitted check:
 `skipUncommittedChangesCheck`
 
 Don't check for uncommitted changes when running

## Configuration Files

The bulk of the configuration is handled with configuration files which are separated by platform.

- [configDefaults/](./../configDefaults) contains subfolders specific to each platform
    - [generic/](./../configDefaults/generic) for config files not specific to any platform
    - [magento2/](./../configDefaults/magento2) for config files specific to Magento 2
    - [laravellumen/](./../configDefaults/laravellumen) for config files specific to Laravel or Lumen project
        
Each platform folder contains the configuration files for that platform. Where if a file does not exist in the platform folder, the generic configuration file is used.

PHPQA's [configDefaults/generic](./../configDefaults/generic) folder contains a config file for each tool run by phpqa. At the moment this is

- [phpmd/ruleset.xml](./../configDefaults/generic/phpmd/ruleset.xml)
- [infection.json](./../configDefaults/generic/infection.json)
- [phpstan.neon](./../configDefaults/generic/phpstan.neon)
- [phpunit.xml](./../configDefaults/generic/phpunit.xml)
- [psr4-validate-ignore-list.txt](./../configDefaults/generic/psr4-validate-ignore-list.txt)

#### Config overrides

If no local config file exists in your project's `qaConfig` folder, phpqa will detect what type of platform you're on and use the config in its own `configDefaults/` folder.

As an example, when running phpstan on a Magento 2 codebase, PHPQA will check for its config in the following order:

1. Your project's root `qaConfig/phpstan.neon`
2. PHPQA's root `configDefaults/magento2/phpstan.neon`
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

If you want to make more wholesale tweaks to `qa` customisation you can create a file in your `qaConfig` directory called `qaConfig.inc.bash`. This will be automatically detected and included and can then override all kinds of default configuration that has occurred up to that point.

When you run `qa`, it checks for a file located in `"$projectConfigPath/qaConfig.inc.bash"` and will include it if found. 

Using this you can override as much of the standard configuration as you see fit.

**_For example, the phpqa project itself has a [qaConfig](./../qaConfig) folder which is used when phpqa is run against itself._**

