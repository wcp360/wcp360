# V4.0.21.9 --- Database / Cache / Mail CLI Mapping

Examples: `wcpctl db create --account <id> ...`
`wcpctl db user-create --database <id>`
`wcpctl db grant-set --database <id> --user <id> --profile <id>`
`wcpctl cache create --account <id> --backend redis`
`wcpctl mail mailbox-create --domain <id> ...`
`wcpctl mail-security spam-policy --domain <id> ...`
`wcpctl mail dkim-rotate --domain <id> --dry-run`
`wcpctl mail auth-verify --domain <id>`
`wcpctl webmail sso --mailbox <id>`

Forbidden public escape hatches include arbitrary SQL, arbitrary Redis
commands, arbitrary MTA/IMAP commands and raw privileged config
injection.
