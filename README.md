[![codecov](https://codecov.io/gh/spryker-sdk/evaluator/branch/master/graph/badge.svg?token=PkAXEay5ir)](https://codecov.io/gh/spryker-sdk/evaluator)

# evaluator

To run evaluator for the project execute the command
```bash
vendor/bin/evaluator evaluate
```

To run evaluator in particular project directory
```bash
vendor/bin/evaluator evaluate --path=some/path
```

To run evaluator with specific checkers
```bash
vendor/bin/evaluator evaluate --checks=<comma separated checkers names>
```

To run evaluator with specific output format (output by default)
```bash
vendor/bin/evaluator evaluate --format=<output, json>
```

To run evaluator with specific output redirect to project file `./report`
```bash
vendor/bin/evaluator evaluate --file
```

# Other commands

Generate feature packages breakdown file. Should be executed from the evaluator project only.
```shell
GITHUB_AUTH=<token: ghp_*> bin/console extract-feature-packages -vvv
```
