# PHPQA
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

PHPQA is a quality assurance pipeline written in BASH that can be run both on the desktop as part of your development process and then also as part of a continuous integration (CI) pipeline.

It runs tools in a logical order and will fail as quickly as possible.

PHPQA has only been tested on Linux.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/884a284be5cd4dd3a49c199119385f58)](https://www.codacy.com/app/edmondscommerce/phpqa?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/phpqa&amp;utm_campaign=Badge_Grade) 
[![Build Status](https://travis-ci.org/edmondscommerce/phpqa.svg?branch=master)](https://travis-ci.org/edmondscommerce/phpqa)
[![Code Coverage](https://scrutinizer-ci.com/g/edmondscommerce/phpqa/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/edmondscommerce/phpqa/?branch=master)

## Installing

We need to install phpstan shim dev master first, and then PHPQA. This is the case until we get a stable release with important bug fixes

```bash
composer require phpstan/phpstan-shim:dev-master@dev --dev
composer require edmondscommerce/phpqa:dev-master@dev --dev
```

Your project's `composer.json` needs to specify a bin folder. If it's not already present, add this:

```
    ...
    "config": {
        "bin-dir": "bin"
    }
    ...
``` 

## Running

To run the full pipline, simply execute in your BASH terminal: 
```bash
./bin/qa 
```

### Usage:

```
$ ./bin/qa -h

Usage:
bin/qa [-t tool to run ] [ -p path to scan ]

Defaults to using all tools and scanning whole project based on platform

 - use -h to see this help

 - use -p to specify a specific path to scan

 - use -t to specify a single tool:
     psr|psr4         psr4 validation
     com|composer     composer validation
     st|stricttypes   strict types validation
     lint|phplint     phplint
     stan|phpstan     phpstan
     unit|phpunit     phpunit
     infect|infection infection
     md|messdetector  php mess detector
     ma|markdown      markdown validation
     bf|phpbf         php beautifier and fixer
     cs|phpcs         php code sniffer
     l|loc            lines of code and other stats


```

By default, PHPQA will run against the entire project root.

### Single Tool:

If you want to run a single tool, use the `-t` option. See the usage above to get the shortcuts for the tools

```bash
./bin/qa -t stan
```

### Specified Path:

Some of the tools allow us to restrict the scan to a specified path. To specify the path, use the `-p` option.

```bash 
./bin/qa -p ./src/specified/path.php
```

### Combined

You can combine both options

```bash 
./bin/qa -t stan -p ./src/specified/path.php
```
 
## Configuration

Please see the [Configuration docs](./docs/configuration.md)

## The Pipeline

Please see the [Pipeline docs](./docs/pipeline.md)

## The QA Tools

For full details and configuration instructions for all fo the tools, please see the [PHPQA Tools](./docs/phpqa-tools.md)

## Platform Detection

PHPQA comes with a set of generic tools and configs, but also has some inbuilt profiles.

Information on how this works can be found on the [platform detection page](docs/platform-detection.md)

Specific platforms' docs are at:

- [Magento 2](./docs/magento2.md)
- [Laravel and Lumen](./docs/laravellumen.md)

## Using for Continuous Integration (CI)

Please see the [CI docs](./docs/ci.md)
