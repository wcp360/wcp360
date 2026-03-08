# ======================================================================
# WCP 360 – Multi-stage Docker build
# Creator: HADJ RAMDANE Yacine | V0.1.0
# ======================================================================

# ── Stage 1: Build ───────────────────────────────────────────────────
FROM golang:1.22-alpine AS builder

RUN apk add --no-cache git ca-certificates tzdata

WORKDIR /src
COPY go.mod go.sum ./
RUN go mod download

COPY . .
RUN CGO_ENABLED=0 GOOS=linux go build \
    -ldflags="-s -w -extldflags=-static" \
    -o /wcp360 ./cmd/wcp360

# ── Stage 2: Minimal runtime ─────────────────────────────────────────
FROM scratch

COPY --from=builder /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/
COPY --from=builder /usr/share/zoneinfo /usr/share/zoneinfo
COPY --from=builder /wcp360 /wcp360

# Config and data directories
VOLUME ["/etc/wcp360", "/var/lib/wcp360", "/srv/www"]

EXPOSE 8080

ENTRYPOINT ["/wcp360"]
