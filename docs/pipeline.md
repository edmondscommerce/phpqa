# PHPQA Pipeline

PHPQA runs tools in a sequence that is designed to fail as quickly as possible making it ideal for local development purposes as well as CI.

The steps are run in sequence. Any step that fails kills the whole process.

In local development, a failed step can be retried indefinitely until it passes. In CI a failed step marks the CI process as failed.

In general the steps are in a logical progression of importance. So for example there is no point static analysing PHP code if the code syntax is not even valid.

Each tool is run in `bin/qa` using the [runTool](./../includes/functions.inc.bash#L30) function. This function handles the process of checking for a platform tool and falling back to the generic tool.

## Hooks

There are two hooks in the pipeline which allow you to run project specific tasks before the qa process commences and after it has successfully completed.

These are simple bash scripts that you place into your projects `qaConfig` folder called `hookPre.bash` and `hookPost.bash` respectively.

It is sourced into the main qa process and so has access to all of the config variables as defined in the main script and overridden in your project config.

If you need something in this hook to fail the whole process, it is important to do the following:

```bash
# something in the hook needs to mark the process as failed...
exit 1;
```
In Bash - an exit code of greater than 0 indicates an error. You can pick any number you want, but 1 is the standard for "general error".


### Suggested Use Cases

There is no restriction on what you can do in these scripts, though they should not really be used for applying any configuration. Common use cases might be 

#### hookPre.bash

- flushing and/or priming caches
- building IDE helpers
- updating composer dependencies

#### hookPost.bash

- pushing code to CI
- rebuilding example code
- generating reports


## The Pipeline

### 1. Detection and Configuration
This step includes:

 - platform detection
 - detecting if Xdebug is available
 - [setPaths.inc.bash](./../includes/generic/setPaths.inc.bash): setting the paths to be checked and also ignored
 - [setConfig.inc.bash](./../includes/generic/setConfig.inc.bash): setting the configuration
 - checking for and applying project specific overrides (qaConfig.inc.bash)

### 2. Preparation
This step includes:

 - [prepareDirectories.inc.bash](./../includes/generic/prepareDirectories.inc.bash): ensuring required directories exist
 - checking for and running your project's `hookPre.bash` script
 
### 3. QA Tools

This step runs through the various quality assurance tools in order. 

To read about the full suite of tools, please read [PHPQA's suite of tools](./phpqa-tools.md)

### 4. Statistics and Post Hook

If all tests pass then you get a nice thumbs up and get some interesting stats about your codebase.

Finally we check for a `hookPost.bash` in your project's `qaConfig` folder and run that if found.

If there were retries of any of the tools, it is strongly suggested that you rerun the full pipeline before regarding it as passing.
