# Premium connector proposal template

This is a documentation template, excluded from the 97-entry [catalogue](../CATALOG.json). It is distinct from the existing [plugin implementation authoring template](../../_template/README.md). Neither template is an installer input or a production manifest.

Copy these three documents only when a concrete proposal is requested:

```text
<new-connector-slug>/
├── README.md
├── package.proposal.json
└── docs/
    └── ACCEPTANCE.md
```

1. Replace `plugin.example-provider-pro`, label, category, summary and boundaries with the actual proposed scope.
2. Use only exact non-`engine.*` keys from the [149-entry catalogue](../../../PLUGIN_CATALOG.md) and actual owners from the [shared closed schema](../../../WCP360_Modules/schemas/package-proposal.schema.json).
3. Keep `schema`, `kind: plugin`, `tier: pro`, `status: design`, `installable: false` and `source_model: undecided` unchanged in this proposal format. Define real dependencies only when reviewed package contracts exist.
4. Write package-specific acceptance gates for ownership, secret references, provider/interface compatibility, supply-chain evidence, refusal, outage and recovery.
5. Add the complete object to the authoritative catalogue; synchronize its JSON mirror and the Markdown view. Do not count this example itself.
6. Do not create placeholder source/configuration trees, shell hooks, fake versions/digests, vendor endpoints, price fields, entitlement keys or an admitted manifest.

Passing this schema does not qualify a publisher, establish license compatibility, authorize effects or make a package installable. The seven native engine adapters and the 233 essential hosting capabilities remain free Core scope.

Read [Core licensing](../../../WCP360_Core/LICENSING.md), [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md), the [example metadata](package.proposal.json) and [acceptance record](docs/ACCEPTANCE.md). A separate service/API does not automatically establish a legally independent proprietary package. No premium license or source distribution model is selected.

## Example scope


This is an authoring example, not an admitted package. Essential Core features, current actor and tenant authorization, publisher trust and host execution remain outside this metadata. A production provider interface and licensing model must be reviewed separately.

## Intended scope and evidence

1. Replace the example scope and catalogue key with exact existing design keys and the correct Core owner boundaries before proposing a real package.
2. Select a concrete provider/interface profile and define positive, refusal, outage and recovery evidence without inventing qualified revisions or artifacts.
3. Keep credentials in scoped secret references and reject arbitrary shell commands, host paths, network destinations and container sockets.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.
