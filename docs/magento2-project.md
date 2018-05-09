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