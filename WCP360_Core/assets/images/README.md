# WCP360 official interface logos

The two transparent PNG files in [branding](branding/) were supplied and approved by the project owner on 2026-11-01. They replace the candidate text mark and demonstration favicon in the Core.

| Surface | Official asset | Source filename | SHA-256 |
| :--- | :--- | :--- | :--- |
| USER / customer workspace | [wcp360-user.png](branding/wcp360-user.png) | Logo WCP 360 user.png | 7eee3f091da2aab5c2a77accf098cd17f72d7b8746f714fbb58b4a80f20b37d5 |
| ADMIN / ROOT administration | [wcp360-admin-root.png](branding/wcp360-admin-root.png) | Logo WCP 360 admin root.png | c92f7a6bb9386edfd9c378565b616995465a37fa5e954c311e03504f17a1e654 |

Both originals are **2172 x 724 pixels**, with transparency. Their bytes, colors, proportions and artwork are preserved. CSS keeps the logos transparent and unframed in both themes; it does not recolor or crop the artwork.

[Central Twig configuration](../../config/packages/twig.yaml) selects branding from the server runtime context, including before login and during MFA/reconfirmation. Query parameters and theme selection do not choose the surface. Branding conveys identity; it grants no role or system privilege. ADMIN and ROOT share the supplied orange artwork.

The [shared image component](../../templates/components/navigation/brand-logo.html.twig) is used by authentication and workspace layouts. Symfony AssetMapper publishes the images as first-party assets. The browser tab uses the same supplied surface image; dedicated square favicon artwork has not been supplied.

Customer uploads, generated reports, remote plugin content and private vendor assets do not belong here. Additional brand variants need project-owner approval.

- [Frontend source map](../README.md)
- [Twig template map](../../templates/README.md)
- [Browser/accessibility verification](../../tests/Browser/README.md)
- [Dependency/build ownership](../../DEPENDENCIES.md)
