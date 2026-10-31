# Detached reference report — development candidate

This package demonstrates the local lifecycle and the candidate SDK boundary. It is not one of the 149 qualified catalog integrations, a marketplace installer or a production plugin runtime.

An administrator can install its metadata, enable, disable and uninstall it from the candidate ADMIN interface. Installation never adds code to Symfony, runs package hooks, changes Composer dependencies or starts a privileged process. Disable it before uninstalling. Disabling and uninstalling remain available when the package files are missing or invalid; revocation never depends on loading plugin code. Core functions remain available in every state.

An authenticated USER with an explicit tenant membership can fetch GET /workbench/v1/summary/{tenant} only while the package is enabled. The response contains job-state counts, no credentials, tenant IDs or file paths. The route is an experimental interface, not API Contract v1.0.

Run the standalone report with an explicitly supplied summary on stdin:

~~~sh
php WCP360_Plugins/wcp360-reference-report/bin/report.php < summary.json
~~~

The sample uses the standalone SDK decoder. Core never includes this PHP entry point. Signed artifact provenance, real package installation, process supervision, scoped service tokens, revocation and upgrade compatibility must be qualified before a production plugin runtime is accepted.
