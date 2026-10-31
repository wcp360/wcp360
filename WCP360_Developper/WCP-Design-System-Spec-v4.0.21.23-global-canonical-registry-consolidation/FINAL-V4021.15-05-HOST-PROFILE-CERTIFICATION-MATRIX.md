# V4.0.21.15 --- Host Profile Certification Matrix Contract

Un Host Profile certifié doit fixer : OS/distribution/version/arch,
kernel minimum, cgroups/filesystem/quota, init/service manager, firewall
provider, web stack, PHP support matrix, DB/cache, DNS mode/backend,
mail mode/backend, backup adapters, malware/WAF, container capability
and CloudLinux compatibility.

Chaque combinaison a `SUPPORTED`, `EXPERIMENTAL`, `UNSUPPORTED`, dates
de support, migration path et acceptance suite. Le wizard ne propose que
les combinaisons compatibles avec le profile détecté.
