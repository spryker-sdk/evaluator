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

# Other commands

Generate feature packages breakdown file. Should be executed from the evaluator project only.
```shell
GITHUB_AUTH=<token: ghp_*> bin/console extract-feature-packages -vvv
```
