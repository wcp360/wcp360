# WCP360 V2.5.3.18 — Files / File Manager Functional Depth

## Style lock
V2.5.3.14 remains the visual baseline. No shell redesign was introduced.

## Developed family
- file manager tree / listing / inspector
- file editor
- uploads / downloads / transfers
- search
- trash / soft-delete
- SFTP / SSH scoped access
- file settings
- permissions workflow

## Security and operational rules
- WCP-USER is bounded to the current application root;
- filesystem paths outside the application scope are inaccessible;
- protected files are not silently editable;
- environment secrets should use a dedicated secrets/environment surface;
- server-managed service configuration stays outside the normal file manager;
- large recursive operations run through Job Center;
- deletion is recoverable where feasible;
- permanent delete is explicit and destructive;
- SFTP access does not imply shell access;
- 0777/world-writable permissions are considered unsafe;
- protected or high-impact production file edits should show a diff and preserve a recoverable revision.

## Next family
Backups & Restore.