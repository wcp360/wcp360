# Disposable verification image, never a production release.
FROM golang:1.27.3-trixie AS planner
WORKDIR /src
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./
RUN go test ./... && go vet ./... && CGO_ENABLED=0 go build -trimpath -o /out/wcp-plan ./cmd/wcp-plan

# Official PHP 8.4 ZTS / Debian 13 image, immutable multi-platform digest resolved 2026-10-31.
FROM dunglas/frankenphp:1-php8.4-trixie@sha256:08af88488ce218936d530b80f6f55dc2afe4d0d91f13f9e6ae350a9e7752128e
USER root
RUN apt-get update && apt-get install -y --no-install-recommends nginx curl util-linux libcap2-bin \
    && if [ -n "$(getcap /usr/local/bin/frankenphp)" ]; then setcap -r /usr/local/bin/frankenphp; fi \
    && rm -rf /var/lib/apt/lists/*
COPY --from=planner /out/wcp-plan /usr/local/libexec/wcp360/wcp-plan
COPY WCP360_Build/verification/candidate-frankenphp-smoke.sh /opt/wcp360/candidate-frankenphp-smoke.sh
COPY WCP360_Build/verification/fixtures/worker-smoke.php /opt/wcp360/worker-smoke.php
ENTRYPOINT ["bash", "/opt/wcp360/candidate-frankenphp-smoke.sh"]
