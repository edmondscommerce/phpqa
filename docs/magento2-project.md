## Progress


| Pipeline Item    | Progress | Notes |
| ---------------- | -------- | ----- |
| phpStrictTypes   | N/A      |       |
| phpLint          | 80%      | Could do with some tidying up of the ignores |
| phpstan          | 100%     |       |
| phpunit          | 70%      | Runs successfully, but takes ~3 minutes |
| messDetector     | 100%     |       |
| markdownLinks    | 100%     |       |
| uncommitedChanges| 100%     |       |
| beautifierFixer  | N/A      | Not modifying core/3rd party code |
| codeSniffer      | 50%      | Resolved dependency problems, now runs but need to work out the excludes |

## Notes

Need to add this to M2's `composer.json`

```
        "meqp": {
            "type": "vcs",
            "url": "https://github.com/magento/marketplace-eqp"
        },
```