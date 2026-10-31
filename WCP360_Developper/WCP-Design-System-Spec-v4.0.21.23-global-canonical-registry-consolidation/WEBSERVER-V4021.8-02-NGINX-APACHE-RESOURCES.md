# V4.0.21.8 --- Nginx / Apache / Reverse Proxy Resource Dictionaries

## `webserver_profile`

`id`, `code`, `engine_mode`, `listener_policy`, `http2_policy`,
`http3_policy`, `compression_policy`, `proxy_policy`, `static_policy`,
`security_headers_profile`, `timeout_profile`, `body_limit_profile`,
`status`, `version`.

`engine_mode`: `NGINX_ONLY`, `APACHE_ONLY`, `NGINX_PROXY_APACHE`, with
availability controlled by certified host profile.

## `webserver_render`

`id`, `site_id`, `engine`, `template_version`, `desired_hash`,
`rendered_artifact_ref`, `validation_result`, `created_at`.

## `webserver_observation`

`site_id`, `engine`, `active_hash`, `service_state`, `observed_at`,
`drift_state`.

## Rules

Generated configuration is immutable per revision. Apply uses stage →
syntax/config validation → atomic activation → reload → health
verification → rollback on failure. User input is data, never an
executable configuration fragment unless accepted by a specific governed
directive schema.
