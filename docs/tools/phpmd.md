# PHPQA PHP Mess Detector (PHPMD)

[PHP Mess Detector Tool](../includes/generic/messDetector.inc.bash)
 
PHPMD Looks for messy, complex and otherwise problematic code.

[PHPMD homepage](https://phpmd.org/)

## Configuration

In the [default configuration](./../../configDefaults/generic/phpmd/ruleset.xml), we use all of the Mess Detector rules apart from the long variable name one.

If you want to change this configuration, simply copy the phpmd folder into your `qaConfig` folder and then configure it as required.

```bash
#Enter your project root
cd /my/project/root

#Make your config override directory
mkdir -p qaConfig

#Copy the phpmd folder as a whole
cp vendor/edmondscommerce/phpqa/configDefaults/phpmd/ qaConfig/ -r

```

## Suppressing Warnings

As we are using all of the rules, there are often times where you need to supress a particular warning as there is no real option to avoid the warning.

If you want to suppress specific errors for certain methods etc, copy/paste some generic part of the message and then grep the `vendor/phpmd` directory, for example:

To find the rule for a message that contains `to keep number of public methods under'` 
```bash
grep 'to keep number of public methods under' -r vendor/phpmd/src/main/rulesets -B 4 | grep -P -o '(?<=rule name=")[^"]+'
```

To make life easy for yourself, you might make this a function in your bashrc file, or just paste it into your terminal session.

```bash

function phpmdRule(){
    local message="$@"
    local phpMdPath="$(find . \
        -wholename '*vendor/phpmd/phpmd/src/main/resources/rulesets*' \
        -type d 
    )"
    grep "$message" -r  -B 4 | grep -P -o '(?<=rule name=")[^"]+'
}

```

And then to use it, just:

```bash
phpmdRule public methods under
```

Whatever rule is returned by phpmdRule call, supress it in PHP using dock block.


```php
<?php
    /**
     * @SuppressWarnings(PHPMD.SomeRule) 
     */
     public function someMethod() { }
```
