#!/usr/bin/env bash
# ======================================================================
<<<<<<< HEAD
# WCP 360 | V0.1.0 | scripts/install.sh
# VPS installation script — Ubuntu 22.04 / 24.04
# Usage: sudo bash scripts/install.sh
# ======================================================================
set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; NC='\033[0m'
info()  { echo -e "${GREEN}[INFO]${NC}  $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $*"; }
error() { echo -e "${RED}[ERROR]${NC} $*"; exit 1; }
step()  { echo -e "\n${CYAN}▶ $*${NC}"; }

[[ $EUID -ne 0 ]] && error "Must run as root: sudo bash $0"

VERSION="v0.1.0"
INSTALL_DIR="/opt/wcp360"
CONFIG_DIR="/etc/wcp360"
DATA_DIR="/var/lib/wcp360"
WWW_DIR="/srv/www"
LOG_DIR="/var/log/wcp360"
BINARY_PATH="$INSTALL_DIR/wcp360"
SERVICE_USER="wcp360"

info "WCP360 $VERSION Installer"
info "================================"

# ── OS check ────────────────────────────────────────────────────────────
step "Checking system"
if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    info "Detected: $PRETTY_NAME"
    [[ "$ID" != "ubuntu" ]] && warn "Optimised for Ubuntu — proceeding anyway."
fi

# ── Dependencies ────────────────────────────────────────────────────────
step "Installing dependencies"
apt-get update -qq
apt-get install -y -qq curl wget ca-certificates gnupg lsb-release git

# Install Go 1.22 if not present
if ! command -v go &>/dev/null || [[ "$(go version | awk '{print $3}')" < "go1.22" ]]; then
    info "Installing Go 1.22..."
    GO_TAR="go1.22.3.linux-amd64.tar.gz"
    wget -q "https://dl.google.com/go/$GO_TAR" -O /tmp/$GO_TAR
    tar -C /usr/local -xzf /tmp/$GO_TAR
    echo 'export PATH=$PATH:/usr/local/go/bin' >> /etc/profile.d/go.sh
    export PATH=$PATH:/usr/local/go/bin
    info "Go $(go version | awk '{print $3}') installed"
fi

# ── System user ─────────────────────────────────────────────────────────
step "Creating system user"
if ! id $SERVICE_USER &>/dev/null; then
    useradd --system --no-create-home --shell /usr/sbin/nologin $SERVICE_USER
    info "User $SERVICE_USER created"
else
    info "User $SERVICE_USER already exists"
fi

# ── Directories ─────────────────────────────────────────────────────────
step "Creating directories"
mkdir -p "$INSTALL_DIR" "$CONFIG_DIR" "$DATA_DIR" "$WWW_DIR" "$LOG_DIR"
chown -R $SERVICE_USER:$SERVICE_USER "$DATA_DIR" "$WWW_DIR" "$LOG_DIR"
chmod 750 "$DATA_DIR" "$LOG_DIR"
chmod 755 "$WWW_DIR"
info "Directories created"

# ── Build binary ────────────────────────────────────────────────────────
step "Building WCP360 binary"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
cd "$PROJECT_DIR"
go mod tidy
CGO_ENABLED=0 GOOS=linux go build -ldflags="-s -w" -o "$BINARY_PATH" ./cmd/wcp360
chmod 755 "$BINARY_PATH"
info "Binary installed → $BINARY_PATH"

# ── Config ──────────────────────────────────────────────────────────────
step "Installing config"
if [[ ! -f "$CONFIG_DIR/wcp360.yaml" ]]; then
    # Generate random JWT secret
    JWT_SECRET=$(openssl rand -hex 32 2>/dev/null || cat /dev/urandom | head -c 32 | base64 | tr -d '=+/' | head -c 32)
    cat > "$CONFIG_DIR/wcp360.yaml" << YAML
# WCP360 v0.1.0 — Production Config
listen_addr:    ":8080"
env:            "production"
log_level:      "info"
database_path:  "$DATA_DIR/state.db"
data_dir:       "$WWW_DIR"
domain:         "$(hostname -f 2>/dev/null || echo localhost)"
jwt_secret:     "$JWT_SECRET"
admin_username: "admin"
admin_email:    "admin@$(hostname -f 2>/dev/null || echo localhost)"
# Generate hash: htpasswd -bnBC 12 "" YOUR_PASSWORD | tr -d ':\n'
# Default: admin123 — CHANGE THIS IMMEDIATELY
admin_password_hash: "\$2a\$12\$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj3oW6J9BmZe"
YAML
    chmod 640 "$CONFIG_DIR/wcp360.yaml"
    chown root:$SERVICE_USER "$CONFIG_DIR/wcp360.yaml"
    warn "Config created at $CONFIG_DIR/wcp360.yaml — CHANGE the admin password hash!"
else
    info "Config already exists at $CONFIG_DIR/wcp360.yaml — skipping"
fi

# ── Systemd service ─────────────────────────────────────────────────────
step "Installing systemd service"
cat > /etc/systemd/system/wcp360.service << UNIT
[Unit]
Description=WCP360 Modern Web Control Panel
Documentation=https://docs.wcp360.com
After=network.target
Wants=network-online.target

[Service]
Type=simple
User=$SERVICE_USER
Group=$SERVICE_USER
ExecStart=$BINARY_PATH
WorkingDirectory=$INSTALL_DIR
Restart=on-failure
RestartSec=5s
StandardOutput=journal
StandardError=journal
SyslogIdentifier=wcp360
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=$DATA_DIR $WWW_DIR $LOG_DIR
CapabilityBoundingSet=
AmbientCapabilities=
SecureBits=keep-caps

[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable wcp360
info "Systemd service installed and enabled"

# ── Firewall ─────────────────────────────────────────────────────────────
step "Configuring firewall"
if command -v ufw &>/dev/null; then
    ufw allow 8080/tcp comment "WCP360" 2>/dev/null || true
    ufw allow 80/tcp comment "HTTP" 2>/dev/null || true
    ufw allow 443/tcp comment "HTTPS" 2>/dev/null || true
    info "UFW rules added"
fi

# ── Summary ──────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}════════════════════════════════════════${NC}"
echo -e "${GREEN}  WCP360 $VERSION installed successfully!${NC}"
echo -e "${GREEN}════════════════════════════════════════${NC}"
echo ""
info "Next steps:"
echo "  1. Edit config:        nano $CONFIG_DIR/wcp360.yaml"
echo "  2. Set admin password: htpasswd -bnBC 12 '' YOUR_PASS | tr -d ':\\n'"
echo "  3. Start service:      systemctl start wcp360"
echo "  4. View logs:          journalctl -u wcp360 -f"
echo "  5. Admin UI:           http://$(hostname -I | awk '{print $1}'):8080/admin/login"
echo ""
warn "Default password is 'admin123' — change it before exposing to the internet!"
=======
# WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
# ======================================================================
# Creator: HADJ RAMDANE Yacine | V0.0.5
# File: scripts/install.sh
# Description: Interactive installer — creates FHS dirs, systemd unit,
#              generates JWT secret, hashes admin password.
#              Usage: sudo bash install.sh [--non-interactive]
# ======================================================================

set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
info()    { echo -e "${BLUE}[WCP360]${NC} $*"; }
success() { echo -e "${GREEN}[✓]${NC} $*"; }
warn()    { echo -e "${YELLOW}[!]${NC} $*"; }
error()   { echo -e "${RED}[✗]${NC} $*" >&2; }

BINARY_PATH="${BINARY_PATH:-/opt/wcp360/bin/wcp360}"
CONFIG_PATH="/etc/wcp360/wcp360.yaml"
NON_INTERACTIVE=false
[[ "${1:-}" == "--non-interactive" ]] && NON_INTERACTIVE=true

check_root() {
    [[ $EUID -eq 0 ]] || { error "Run as root: sudo bash install.sh"; exit 1; }
}

create_directories() {
    info "Creating FHS directories..."
    local dirs=("/opt/wcp360/bin" "/etc/wcp360" "/etc/wcp360/tls"
                 "/var/lib/wcp360" "/var/log/wcp360" "/srv/www")
    for d in "${dirs[@]}"; do
        mkdir -p "$d"
        success "$d"
    done
}

create_system_user() {
    id -u wcp360 &>/dev/null && { success "wcp360 user already exists"; return; }
    useradd --system --no-create-home --shell /sbin/nologin --home-dir /opt/wcp360 wcp360
    success "wcp360 system user created"
}

generate_jwt_secret() {
    if command -v openssl &>/dev/null; then
        openssl rand -hex 32
    else
        tr -dc 'a-f0-9' < /dev/urandom | head -c 64
    fi
}

hash_password() {
    local password="$1"
    if command -v htpasswd &>/dev/null; then
        htpasswd -nbB -C 12 admin "$password" | cut -d: -f2
    elif command -v python3 &>/dev/null; then
        python3 -c "import bcrypt; print(bcrypt.hashpw(b'${password}', bcrypt.gensalt(12)).decode())"
    else
        error "Cannot hash password: install apache2-utils (htpasswd) or python3-bcrypt"
        exit 1
    fi
}

get_admin_credentials() {
    if $NON_INTERACTIVE; then
        ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
        ADMIN_PASSWORD="${ADMIN_PASSWORD:-$(openssl rand -hex 12)}"
        warn "Non-interactive: admin=${ADMIN_USERNAME}, password=${ADMIN_PASSWORD}"
    else
        read -rp "Admin username [admin]: " ADMIN_USERNAME
        ADMIN_USERNAME="${ADMIN_USERNAME:-admin}"
        read -rsp "Admin password: " ADMIN_PASSWORD; echo
        [[ -n "$ADMIN_PASSWORD" ]] || { error "Password cannot be empty"; exit 1; }
    fi
    info "Hashing admin password (bcrypt cost=12)..."
    ADMIN_HASH=$(hash_password "$ADMIN_PASSWORD")
    success "Password hashed"
}

write_config() {
    local jwt_secret listen_addr data_dir
    jwt_secret=$(generate_jwt_secret)
    listen_addr="${LISTEN_ADDR:-:8080}"
    data_dir="${DATA_DIR:-/srv/www}"

    cat > "$CONFIG_PATH" << EOF
# WCP360 Configuration — generated by install.sh
# Edit: /etc/wcp360/wcp360.yaml

listen_addr: "$listen_addr"
env: "production"
log_level: "info"
database_path: "/var/lib/wcp360/state.db"
data_dir: "$data_dir"
jwt_secret: "$jwt_secret"
admin_username: "$ADMIN_USERNAME"
admin_password_hash: "$ADMIN_HASH"
EOF
    chmod 600 "$CONFIG_PATH"
    success "Config written to $CONFIG_PATH"
}

set_permissions() {
    chown -R wcp360:wcp360 /opt/wcp360 /var/lib/wcp360 /var/log/wcp360
    chown root:wcp360 /etc/wcp360
    chmod 750 /etc/wcp360
    [[ -f "$BINARY_PATH" ]] && chmod 755 "$BINARY_PATH"
    success "Permissions set"
}

install_systemd() {
    cat > /etc/systemd/system/wcp360.service << EOF
[Unit]
Description=WCP360 Web Control Panel
After=network.target
Requires=network.target

[Service]
Type=simple
User=wcp360
Group=wcp360
ExecStart=$BINARY_PATH
Restart=always
RestartSec=5
WorkingDirectory=/opt/wcp360

# Hardening
NoNewPrivileges=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=/var/lib/wcp360 /var/log/wcp360 /srv/www
PrivateTmp=true
PrivateDevices=true
CapabilityBoundingSet=
AmbientCapabilities=

[Install]
WantedBy=multi-user.target
EOF
    systemctl daemon-reload
    success "Systemd unit installed: wcp360.service"
}

main() {
    echo ""
    echo -e "${BLUE}╔══════════════════════════════════╗${NC}"
    echo -e "${BLUE}║  WCP360 Installer v0.0.5         ║${NC}"
    echo -e "${BLUE}╚══════════════════════════════════╝${NC}"
    echo ""
    check_root
    create_directories
    create_system_user
    get_admin_credentials
    write_config
    set_permissions
    install_systemd
    echo ""
    success "Installation complete!"
    info "Start: systemctl enable --now wcp360"
    info "Admin: http://YOUR_SERVER_IP:8080/admin/login"
    echo ""
}

main "$@"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
