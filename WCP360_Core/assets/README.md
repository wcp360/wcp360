# Core frontend sources

The executable WCP360 Core uses Symfony/Twig, AssetMapper, Turbo and focused Stimulus controllers.

| Source | Responsibility |
| :--- | :--- |
| [controllers](controllers/) | Form submission state, appearance, responsive navigation and page search |
| [styles](styles/) | Shared WCP360 prototype styles and Core-specific component adapters |
| [images](images/) | First-party image assets |
| [app.js](app.js) | Turbo/Stimulus entry point and explicit controller registration |
| [Twig templates](../templates/) | Server-rendered pages, authorized navigation, forms and shared components |

The visual reference is the canonical shell and entry pages in WCP360_Prototype/. The Core carries snapshots of reference-ui.css, auth.css and readable-type.css; styles/app.css adapts the implemented Core forms and data to those tokens. Update these snapshots deliberately with the corresponding prototype changes and verify light/dark, responsive layout, focus and form journeys.

All runtime links use Symfony routes. The Core renders its actual authorized database state. Prototype simulation scripts and browser-side account/resource stores are not runtime dependencies. Only the non-sensitive appearance preference is stored in localStorage; authentication, MFA and permissions remain server-owned.

Icons are first-party inline SVG. Styles and scripts are served locally under the existing Content Security Policy; no external font, icon CDN or new JavaScript package is required.

Rebuild generated assets with php bin/console asset-map:compile after changes. Then refresh the appropriate candidate context cache and reload the browser. Generated public/assets/, dependencies and runtime state stay outside versioned source.

Relevant checks are Twig/container lint, the application suite, and real-browser navigation, appearance, responsive, Turbo/Stimulus and authentication journeys. See [browser tests](../tests/Browser/README.md).
