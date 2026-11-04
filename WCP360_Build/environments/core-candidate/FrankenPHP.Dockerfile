# Disposable verification image, never a production release.
FROM golang:1.27.3-trixie AS planner
WORKDIR /src
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./
RUN go test ./... && go vet ./... && CGO_ENABLED=0 go build -trimpath -o /out/wcp-plan ./cmd/wcp-plan

# Official PHP 8.4 ZTS / Debian 13 image, immutable multi-platform digest resolved 2026-10-31.
FROM dunglas/frankenphp:1-php8.4-trixie@sha256:d2635417abe4a68f4a7372c4447d5d27f21dc784b1acdb6275ea419e3b7bc4a2
USER root
RUN apt-get update && apt-get install -y --no-install-recommends nginx curl util-linux libcap2-bin unzip \
    && if [ -n "$(getcap /usr/local/bin/frankenphp)" ]; then setcap -r /usr/local/bin/frankenphp; fi \
    && rm -rf /var/lib/apt/lists/*
# Load the actual Core components against the existing lock, without app bootstrap.
# No dependency update, ignored platform requirement, plugin or Composer script.
RUN install-php-extensions intl pdo_pgsql
COPY --from=composer:2.11.1 /usr/bin/composer /usr/local/bin/composer
WORKDIR /opt/wcp360/WCP360_Core
COPY WCP360_Core/composer.json WCP360_Core/composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts --no-plugins \
    && composer check-platform-reqs --no-dev
COPY WCP360_Core/src/Shared/Http/BoundarySubscriber.php WCP360_Core/src/Shared/Http/RequestReference.php ./src/Shared/Http/
COPY WCP360_Core/src/Shared/Security/RuntimeContext.php ./src/Shared/Security/
COPY WCP360_Core/src/Shared/Correlation/RequestId.php ./src/Shared/Correlation/
COPY --from=planner /out/wcp-plan /usr/local/libexec/wcp360/wcp-plan
COPY WCP360_Build/verification/candidate-frankenphp-smoke.sh /opt/wcp360/candidate-frankenphp-smoke.sh
COPY WCP360_Build/verification/fixtures/worker-smoke.php /opt/wcp360/worker-smoke.php
COPY WCP360_Build/verification/fixtures/core-boundary-worker.php WCP360_Build/verification/fixtures/verify-core-worker.php /opt/wcp360/
ENTRYPOINT ["bash", "/opt/wcp360/candidate-frankenphp-smoke.sh"]
