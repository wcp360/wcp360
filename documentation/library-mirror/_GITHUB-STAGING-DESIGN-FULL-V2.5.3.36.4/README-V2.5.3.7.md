# WCP360 V2.5.3.7 — Deep Cockpit UX Shell & Visual Refinement

## Major UX changes

### 1. Three-layer shell
- Context Rail: organization / environment / pinned / recent.
- Workspace Sidebar: only navigation relevant to the current workspace.
- Command Header: global search, commands, jobs, notifications and create.

### 2. Context is no longer mixed with permissions
The current project/server/environment is shown separately from WCP-USER/WCP-ADMIN authority.

### 3. State Ribbon
Replaces scattered KPI cards with one horizontal operational status band.

### 4. Workspace Grid
Primary operational surface + narrow contextual side stack.

### 5. Action Center
Only items requiring intervention, not another dashboard of metrics.

### 6. Operations Dock
Persistent bottom dock for Tasks / Logs / Terminal / Events.

### 7. Command Center
Cmd/Ctrl+K becomes a secondary operating interface:
Search → Understand → Preview → Confirm → Execute → Audit.

### 8. Reduced visual noise
Less borders, fewer pills, fewer colored cards, more neutral structure.

### 9. Responsive behavior
Desktop: 3-layer cockpit.
Tablet: narrow context + compact workspace navigation.
Mobile: context/sidebar collapse; command header and resource hierarchy remain.

### 10. Light/Dark parity
All new shell elements use tokens and theme inheritance.

## Design principle
WCP360 presents:
**where you are → current state → what needs attention → what you can do → what happened.**

Not every feature at once.