# WCP360 releases

**Qualified product releases: none.**

This directory is named in the plural because it will contain records for multiple independently qualified versions. It is the publication boundary, not the source of development installers or a claim that a complete Core already exists.

## What belongs here

Create a version directory only after its exact candidate has passed applicable functional, installation, compatibility, security and recovery checks plus required reviews.

```text
WCP360_Releases/
├── README.md                    # Current state and publication rules
└── vX.Y.Z/                      # Created only for an actual qualified version
    ├── README-INSTALL.md
    ├── RELEASE-NOTES.md
    ├── ARTIFACTS.json           # Exact metadata schema selected by release owners
    └── CHECKSUMS-and-signature-references
```

This is an illustrative future record layout, not a set of artifacts currently present. Large Core archives, compiled agents and installer packages are immutable GitHub Release assets or approved distribution objects referenced by the version record. Keep dependency inventory, provenance and retained qualification evidence available.

## Stable channel

No latest link is present because no stable target exists. Add a latest/stable alias or signed distribution pointer only after qualifying its target; never point the stable channel at a placeholder or failed candidate.

Core, SDK and detached plugins have explicit independent versions. A plugin release cannot replace a Core asset, and a plugin update need not force a Core release when the approved compatibility contract is satisfied.

## Development inputs

- [Installer source](../WCP360_Core/installer/README.md)
- [Earlier guarded version templates](../WCP360_Core/installer/scaffolds/README.md)
- [Build/qualification procedure](../WCP360_Build/README.md)
- [Complete delivery and release acceptance](../documentation/development/DELIVERY-AND-RELEASES.md)
- [Current baseline manifest](../documentation/manifests/WCP360-BM-0001.md)

The former WCP360_Release/v1.0.0 and v1.1.0 shell templates now live under Core installer scaffolds. Their original shell bytes/modes are preserved, and they still perform no installation. Removing the former latest alias does not remove a stable release; it prevents an unreleased scaffold from being mistaken for one.
