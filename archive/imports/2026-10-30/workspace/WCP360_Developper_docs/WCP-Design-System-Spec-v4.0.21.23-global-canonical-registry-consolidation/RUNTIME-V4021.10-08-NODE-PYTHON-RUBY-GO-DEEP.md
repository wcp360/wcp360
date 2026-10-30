# V4.0.21.10 --- Node.js / Python / Ruby / Go Runtime Deep Contract

## Modèle commun

`runtime_family`, `runtime_version`, `source`, `support_state`,
`installed_state`, `capabilities`, `package_manager_profiles`.

## Node.js

Version sélectionnée, entrypoint contrôlé, package manager npm/yarn/pnpm
si certifié, production install policy, process manager WCP-owned.

## Python

Version, virtualenv par app, WSGI/ASGI mode, entrypoint/module typed,
pip requirements/lock support, gunicorn/uvicorn adapter profiles.

## Ruby

Version, Bundler, Rack/Passenger or certified process adapter.

## Go

Version/build toolchain pour build ; runtime déployé sous identité
account. Pas de compilation root.

## Règle

Build toolchain et runtime sont séparés. Toutes les applications
héritent cgroup/UID/GID/isolation du compte et peuvent recevoir des
sous-limites.
