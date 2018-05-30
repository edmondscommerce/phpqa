# PHPQA Continuous Integration

PHPQA is very well suited to running as part of a CI pipeline.

All that is really required is to export the CI envrionment variable, though you probably also want to ensure that full tests are run

For example, have a look at the [ci.bash](./../ci.bash) script that PHPQA uses to test itself in CI.

The usage of a generic `ci.bash` script is highly encouraged. Specific CI configuration can then be handled elswhere, but that CI system should ultimately run the `ci.bash` script to perform the actual checks. This helps you avoid becoming overly coupled to a specific CI platform.

## Running on Travis

You can use PHPQA on Travis-CI.

To see an example of how to do this, you can look at the [.travis.yml](./../.travis.yml).

## Pushing Code Coverage to Scrutinizer

PHPQA generated code coverage can be pushed to Scrutinizer

To see an example of this, have a look at 
- [.travis.yml](./../.travis.yml)
- [qaConfig/ci/travis/after.bash](./../qaConfig/ci/travis/after.bash)
- [.scrutinizer.yml](./../.scrutinizer.yml)
