# PHPQA Coding Standards

We use the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) package to handle coding standards, including automatically fixing where possible.

## Setting the Coding Standard

The standard is defined with the environment variable `phpcsCodingStandardsNameOrPath`

PHPCS includes some coding standards by default. See [The official docs](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage#printing-a-list-of-installed-coding-standards)

```bash
$ ./bin/phpcs -i
The installed coding standards are Zend, PEAR, PSR2, PSR1, Squiz and MySource
```

The coding standard is defaulted to PSR2. If you want to use one of the built in standards, then you can specify this by name.

### Custom or Third Party Coding Standard

There are many coding standards available as [composer packages](https://packagist.org/?query=phpcs%20standard).

If you want to use one of these, simply install it and then specify the path to the folder in your `qaConfig.inc.bash`

```bash
cd /project/root

#install the coding standard package
composer require --dev vincentlanglet/symfony3-custom-coding-standard

#set up qaConfig
mkdir -p qaConfig
cat <<EOF >> qaConfig.inc.bash
export phpcsCodingStandardsNameOrPath="\$projectRoot/vendor/vincentlanglet/symfony3-custom-coding-standard"
EOF
```

## Ignoring Parts of a File
   
You can mark specific chunks of code to be not analyzed by PHPCS having the following comments:

```php
<?php
$xmlPackage = new XMLPackage;
// phpcs:disable
$xmlPackage['error_code'] = get_default_error_code_value();
$xmlPackage->send();
// phpcs:enable

```

See the [PHPCS Docs](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file) for more information.
