# V4.0.21.20 --- File Manager Deep Contract

Operations include `FILE-LIST`, `FILE-READ`, `FILE-CREATE`,
`FILE-UPLOAD`, `FILE-DOWNLOAD`, `FILE-RENAME`, `FILE-MOVE`, `FILE-COPY`,
`FILE-DELETE`, `DIRECTORY-CREATE`, `PERMISSION-UPDATE`,
`ARCHIVE-CREATE`, `ARCHIVE-EXTRACT`, `FILE-SEARCH`.

Security invariants: canonical path resolution, managed-root
containment, symlink/hardlink escape defense, race-safe operations where
possible, ownership checks, upload size/type policy, archive
traversal/zip-slip prevention, archive-bomb limits, disk/inode quota
preflight and no privileged device/socket creation.

Trash/soft-delete is policy-capable. Destructive recursive operations
require impact preview/confirmation according to risk. File editor never
edits outside authorized roots.
