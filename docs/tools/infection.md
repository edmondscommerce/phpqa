# PHPQA Infection

[Infection](https://infection.github.io/) is a Mutation Testing Framework, that runs PHPUnit tests and then makes small 
modifications to the code and sees if these cause the unit tests to fail.

In PHPQA we run this after the normal PHPUnit run and pass in the coverage generated with PHPUnit. This means that it will only run this tool if you have the `phpUnitCoverage` environment variable set to 1.

## Configuration

You may need to tell infection where the configuration directory for PHPUnit is. To do this, override the
[./configDefaults/generic/infection.json](./configDefaults/generic/infection.json) file and add the following to it

```json
"phpUnit": {
    "configDir": "path/to/directory/with/phpunit.xml"
}
```

Here are the environment variables that you might decide to override:

- **Use Infection** `useInfection`: Set this to 0 to disable Infection
- **Minimum MSI Percentage** `mutationScoreIndicator`: The minimum [MSI](https://infection.github.io/guide/#Mutation-Score-Indicator-MSI) required for PHPQA to pass
- **Minimum Covered MSI Percentage** `coveredCodeMSI`: The minimum [covered MSI](https://infection.github.io/guide/#Covered-Code-Mutation-Score-Indicator) level required for PHPQA to pass

#### Minimum Mutation Score Indicators

Infection has been configured to require both a minimum MSI and covered MSI to be achieved for the test to pass.

Be default these are set to 60% for MSI, and 90% for covered MSI. These values can be overwritten by using environment 
variables. To do this simply export the following before running qa

 * `mutationScoreIndicator` to set the MSI level
 * `coveredCodeMSI` to set the covered MSI level

See the following page for more information on MSIs being used in CI [https://infection.github.io/guide/using-with-ci.html](https://infection.github.io/guide/using-with-ci.html)

##### Setting Minimum Score Indicators

The easiest way to override the default minimum score indicators permanently for your project is to include these in a `qaConfig.inc.bash` file in your projects `qaConfig` folder.

You can see that this is being done in the phpqa project itself in it's own [qaConfig](./qaConfig) folder.

#### Disabling Infection


If you would like to disable infection, simply export the enviroment variable `useInfection` with the value `0`

```bash
export useInfection=0
./bin/qa
```
