# V4.0.21.20 --- Application Runtime Manager

Canonical runtime families: PHP (linked to PHP registry), Node.js,
Python, Ruby and Go where certified. Provider may be native packages,
CloudLinux selectors or other certified adapter.

Operations: `RUNTIME-LIST`, `RUNTIME-ASSIGN`, `RUNTIME-SETTINGS-UPDATE`,
`APP-START`, `APP-STOP`, `APP-RESTART`, `APP-RECONCILE`.

Runtime version is capability-discovered. Assignment cannot silently
install an unsupported runtime. Processes run as tenant/system identity
with cgroup/resource ceilings and managed working directory. Arbitrary
systemd unit/service names are not accepted from tenant payloads.
