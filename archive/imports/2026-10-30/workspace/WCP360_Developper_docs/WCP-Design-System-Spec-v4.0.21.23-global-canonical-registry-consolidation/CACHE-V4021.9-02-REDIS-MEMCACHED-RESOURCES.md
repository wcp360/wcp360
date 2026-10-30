# V4.0.21.9 --- Redis / Memcached Resource Dictionaries

## `cache_instance`

`id`, `account_id`, `backend_type`, `version`, `endpoint_ref`,
`credential_ref?`, `memory_limit`, `eviction_policy`,
`connection_limit`, `persistence_policy`, `network_policy`, `status`,
`desired_revision`, `observed_revision?`, `version_counter`.

## `cache_binding`

`id`, `cache_instance_id`, `site_id?`, `application_id?`, `access_mode`,
`status`, `version`.

## Rules

Redis and Memcached are never exposed publicly by default. Bind/listen
policy, credentials where supported, Unix socket/private network
placement, memory limits and tenant ownership are explicit. Redis
dangerous administrative commands are not exposed as generic WCP
operations.
