# PHPQA Tools

QA runs a suite of standard tools, and are contained in the `includes/generic` folder. These are run from the `bin/qa` script via the `runTool` function.

The tools are run in a specific order designed to allow the process to fail as quickly as possible.

In local development, a failed tool can be retried indefinitely, in CI a failed tool fails the whole process.

## The Tool Runner

Each tool is run by calling the [`runTool`](./../includes/functions.inc.bash#L30) function.

The `runTool` function takes into account the platform that PHPQA detected via the [`detectPlatform`](./../includes/functions.inc.bash#L7) function

You can override the tool for your project if you wish by copying it into `qaConfig/tools` and then editing as you see fit. 

- It will first check for project level overrides in `/project/root/qaConfig/tools/{toolName}.inc.bash`
- Then it will first look inside `includes/{detectedPlatform}/{toolName}.inc.bash`
- If none is found, it will fall back to `includes/generic/{toolName}.inc.bash`

The platform-specific script will be run instead of the generic script. You can choose to `source` the generic tool in your script:

```
source $DIR/../includes/generic/setConfig.inc.bash
... platform-specific script contents ...
```


## The Tools in Order

Here is a full listing of the tools the order they are run. 

For each tool we describe:

- what it does
- the link to the generic tool
- tool specific configuration and other notes

### 1. PSR4 Validation
[PSR-4 validation Tool](../includes/generic/psr4Validate.inc.bash)

Checks for code whose namespaces don't comply with the PSR-4 standard

#### Ignore List
You can specify a number of files or directories to be ignored by the validator. This is a newline seperated
list of valid regex including a valid regex delimiter. For example:

```
#tests/bootstrap\.php#
#tests/phpstan-bootstrap\.php#
#tests/assets/.*?asset#
```

### 2. Composer Check For Issues

[Composer Checks Tool](../includes/generic/composerChecks.inc.bash)

- Runs a diagnose on composer to make sure it's all good
- Dumps the autoloader to ensure any recent code changes will not cause any autoloading issues

### 3. Strict Types Enforcing

[PHP7 Strict Types Tool](../includes/generic/phpStrictTypes.inc.bash)

Checks for files that do not have strict types defined and allows you to fix them

### 4. PHP Parallel Lint

[PHP Lint Tool](../includes/generic/phpLint.inc.bash)

Very fast PHP linting process. Checks for syntax errors in your PHP files

See the [PHP Parallel Lint project page](https://github.com/JakubOnderka/PHP-Parallel-Lint) for more information.

### 5. PHPStan

[PHPStan Tool](../includes/generic/phpstan.inc.bash)

Static Analysis of your PHP code. 

Please see the [PHPQA PHPStan docs](./tools/phpstan.md) for full details on how to work with PHPStan in your project.

See the [PHPStan project page](https://github.com/phpstan/phpstan) for more information about PHPStan in general

### 6. PHPUnit

[PHPUnit Tool](../includes/generic/phpunit.inc.bash)

This step runs your [PHPUnit](https://github.com/sebastianbergmann/phpunit) tests.

Please see the [PHPQA PHPUnit docs](./tools/phpunit.md) for full details on how to work with PHPUnit in your project.

See the [PHPUnit docs](https://phpunit.readthedocs.io/en/7.1/) for full documentation about writing PHPUnit tests and using the PHPUnit tool.

### 7. Infection

[Infection Tool](./../includes/generic/infection.inc.bash)

This step runs your PHPUnit tests but the difference is that it deliberately mutates your code in ways that should make your tests fail. If they don't then "you failed to kill the mutant!"

This tool is incredibly useful in ensuring that your tests are not only covering your code but covering it well.

Please see the [PHPQA Infection docs](./tools/infection.md) for full details on how to work with Infection in your PHPQA project

Please see the main [Infection docs](https://infection.github.io/guide/) for full details on Infection.

### 8. PHP Mess Detector (PHPMD)

[PHP Mess Detector Tool](../includes/generic/messDetector.inc.bash)
 
PHPMD Looks for messy, complex and otherwise problematic code.

Please see the [PHPQA PHPMD docs](./tools/phpmd.md) for full details on how to work with PHPMD in your PHPQA project

[PHPMD homepage](https://phpmd.org/)

### 9. Markdown Links Checker

[Markdown Links Checker Tool](../includes/generic/markdownLinks.inc.bash)

This tool will ensure that you have at least a README.md file in your project root

It will check your `README.md` file and then recursively, all `*.md` files in the `docs` directory

For each link that it finds, it will ensure that the link target is valid. It handles both internal links to project files ,such as other docs pages or specific code files, and also external links to remote web pages.

If any broken links are found, it will then report on these and fail the tool. You can fix the links and retry to the tool until it passes.

### 10. Uncommited Changes Check

[Uncommited Changes Check](./../includes/functions.inc.bash#L92)

At this point, the pipeline will check for any uncommited changes. 

The reason for this is that tools run after this step will actively update the project code files and ideally you should be able to easily roll back any changes if required.

There are two ways for this check to be bypassed:

#### 1. CI Mode
```bash
export CI=true
./bin/qa
```

If this is a CI pipeline, then it does not check for uncommitted changes.


#### 2. skipUncommittedChangesCheck Environment Variable

If you never want this check, you can export the `skipUncommittedChangesCheck` environment variable

If you want to persistently do this, then you should add it to your projects `qaConfig/qaConfig.inc.bash`

```bash
cd /project/root
mkdir -p qaConfig
echo "
export skipUncommittedChangesCheck=1
" >> qaConfig/qaConfig.inc.bash
```

### 11. Beautifier and Fixer

[PHP Code Beautifier and Fixer Tool](../includes/generic/beautifierFixer.inc.bash)

Automatically reformats PHP code according to defined coding standards

Please see the [PHPQA Coding Standards docs](./coding-standards.md) for more information on how to manage coding standards in your project.

Please see the [PHPCS Docs](https://github.com/squizlabs/PHP_CodeSniffer/wiki) for more information about PHP Code Sniffer in general

### 12. PHP Code Sniffer

[PHP CodeSniffer Tool](../includes/generic/codeSniffer.inc.bash)

Now we run the code sniffer to check for any remaining coding standards issues that have not been automatically fixed.

Please see the [PHPQA Coding Standards docs](./coding-standards.md) for more information on how to manage coding standards in your project.

Please see the [PHPCS Docs](https://github.com/squizlabs/PHP_CodeSniffer/wiki) for more information about PHP Code Sniffer in general 
