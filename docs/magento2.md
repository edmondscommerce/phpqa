#PHPQA Magento 2

PHPQA has been customised in order to facilitate QA and CI testing of a Magento 2 project or module.

## Progress

| Pipeline Item    | Progress | Notes |
| ---------------- | -------- | ----- |
| phpStrictTypes   | N/A      |       |
| phpLint          | 80%      | Could do with some tidying up of the ignores |
| phpstan          | 100%     |       |
| phpunit          | 70%      | Runs successfully, but takes ~3 minutes. Not using qaQuickTests parameter. |
| messDetector     | 100%     |       |
| markdownLinks    | 100%     |       |
| uncommitedChanges| 100%     |       |
| beautifierFixer  | N/A      | Not modifying core/3rd party code |
| codeSniffer      | 100%      |  |

## Notes

Need to add this to M2's `composer.json`

```
        "meqp": {
            "type": "vcs",
            "url": "https://github.com/magento/marketplace-eqp"
        },
```
## Overridden tools:

- beautifierFixer: This is set up to not automatically modify third party or core code.  
- codeSniffer: Uses the MEQP2 coding standards
- phpStrictTypes: Is skipped because Magento 2 doesn't use strict types
- setConfig: Sources the generic one, then uses Magento 2's PHPMD, PHPUnit and MEQP configs
- setPaths: Sets include paths to `app/code` and `app/design`

## composer.json amendments

PHPCS runs with the MEQP2 coding standards. For this you need to add this to M2's `composer.json`

```
        "meqp": {
            "type": "vcs",
            "url": "https://github.com/magento/marketplace-eqp"
        },
```

By default, Magento doesn't include a bin folder in composer.json. Remember to add it:

```
    ...
    "config": {
        "bin-dir": "bin"
    }
    ...
``` 
