# V4.0.21.14 --- Mail Message Trace / Queue Diagnostics

## Trace

sender/recipient metadata, queue/message ID, timestamps, routing stage,
accepted/deferred/bounced/delivered, remote SMTP response, TLS state,
SPF/DKIM/DMARC results when available.

## Privacy

Le contenu/body n'est pas inclus par défaut. Header exposure est
permissioned et redacted. Tenant scope est strict.

## Queue

Search, inspect metadata, retry, freeze/unfreeze/delete only according
to mail backend capability and admin permission. Destructive queue
actions require step-up and audit.
