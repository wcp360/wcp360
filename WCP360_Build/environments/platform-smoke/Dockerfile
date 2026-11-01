# Native distribution packages only. This image does not install the Symfony Core.
ARG BASE_IMAGE=debian:13-slim
FROM golang:1.27.3-trixie AS planner
WORKDIR /src
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./
RUN CGO_ENABLED=0 go build -trimpath -o /out/wcp-plan ./cmd/wcp-plan

FROM ${BASE_IMAGE} AS platform-smoke
RUN printf '#!/bin/sh\nexit 101\n' > /usr/sbin/policy-rc.d \
    && chmod 0755 /usr/sbin/policy-rc.d \
    && apt-get -o Acquire::Retries=2 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 update \
    && DEBIAN_FRONTEND=noninteractive apt-get -o Acquire::Retries=2 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 install -y --no-install-recommends \
        ca-certificates curl nginx php-cli php-fpm php-curl php-intl php-mbstring php-pgsql php-sqlite3 php-xml php-zip \
    && php_series="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')" \
    && test -x "/usr/sbin/php-fpm${php_series}" \
    && ln -s "/usr/sbin/php-fpm${php_series}" /usr/local/sbin/php-fpm \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /opt/wcp360
COPY WCP360_Core/src/Shared/Platform/TargetPlatform.php WCP360_Core/src/Shared/Platform/TargetPlatform.php
COPY WCP360_Core/config/platforms/targets.json WCP360_Core/config/platforms/targets.json
COPY WCP360_Core/installer/preflight/candidate.php WCP360_Core/installer/preflight/platform.php WCP360_Core/installer/preflight/
COPY WCP360_Build/verification/candidate-linux-smoke.sh WCP360_Build/verification/candidate-platform-smoke.sh WCP360_Build/verification/
COPY --from=planner /out/wcp-plan /usr/local/libexec/wcp360/wcp-plan
ENV WCP_PLANNER=/usr/local/libexec/wcp360/wcp-plan
CMD ["bash", "WCP360_Build/verification/candidate-platform-smoke.sh"]
