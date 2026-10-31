# WCP360 Insights Connector

This directory is an optional public extension artifact. It is not part of the WCP360 core archive and is disabled by default.

Customer ROOT decides whether to install it, which evidence categories are collected, the schedule and retention, and whether a diagnostic bug report is submitted. The connector cannot execute remote commands, expose a shell, read hosted files, messages, database rows or export secrets.

Disabling or uninstalling the extension stops collection and transmission, revokes the pairing relationship and withdraws its routes. WCP-ADMIN, WCP-USER, the node agent and hosting workloads continue without it.

The prototype uses browser storage to simulate the signed, mutually authenticated exchange with WCP360 INTERNAL. Production must use mTLS, a pinned vendor endpoint, rotating installation credentials, replay protection and an auditable consent receipt.
