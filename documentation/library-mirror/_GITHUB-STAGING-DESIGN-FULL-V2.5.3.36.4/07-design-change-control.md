# Design Change Control After Freeze

## Allowed without unfreezing
- copy clarification;
- bug fix that restores documented behavior;
- accessibility fix that does not materially change information architecture;
- responsive fix preserving capabilities;
- provider/OS-specific detail inside an existing schema;
- new error mapping for an existing workflow.

## Requires design amendment
- new top-level module;
- privilege-model change;
- new destructive-action semantics;
- shell/navigation redesign;
- new persistent state not representable by existing state families;
- new cross-family dependency;
- change that weakens a safety invariant;
- change that alters WCP-USER / WCP-ADMIN boundary.

## Versioning
Use a new design amendment version and reference affected frozen artifacts explicitly.