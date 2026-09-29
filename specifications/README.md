# Canonical Specifications

Approved owner artifacts are stored here by domain.

```text
specifications/
├── documentation/
├── design-system/
├── modules/
├── security/
├── database/
├── api/
├── ipc/
├── tests/
├── devops/
├── observability/
├── performance/
└── engineering/
```

Rules:

1. Only final or explicitly versioned owner artifacts may be used as implementation authority.
2. Historical/candidate/superseded files must be clearly marked and must not silently replace active baselines.
3. SOURCE CODE must consume approved artifacts from this repository rather than reconstructing contracts from chat history.
4. Documentation must not be rescanned wholesale unless MASTER explicitly requests it.
