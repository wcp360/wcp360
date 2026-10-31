# Development and verification only; this is not a qualified release image.
FROM golang:1.27.3-trixie AS planner
WORKDIR /src
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./
RUN go test ./... && go vet ./... && CGO_ENABLED=0 go build -trimpath -o /out/wcp-plan ./cmd/wcp-plan

FROM golang:1.27.3-trixie AS node
WORKDIR /candidate
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./planner/
COPY WCP360_Core/agents/wcpd/candidate/provisioning/ ./provisioning/
WORKDIR /candidate/provisioning
RUN go test -race ./... && go vet ./... && CGO_ENABLED=0 go build -trimpath -o /out/wcp-candidate-node ./cmd/wcp-candidate-node

FROM php:8.4.27-fpm-trixie AS candidate
RUN apt-get update && apt-get install -y --no-install-recommends libcurl4-openssl-dev libpq-dev libicu-dev libzip-dev unzip curl nginx postgresql-client ca-certificates \
    && docker-php-ext-install curl intl pdo_pgsql zip opcache \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2.11.1 /usr/bin/composer /usr/local/bin/composer
WORKDIR /opt/wcp360/WCP360_Core
COPY WCP360_Core/composer.json WCP360_Core/composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts
COPY WCP360_Core/ ./
COPY WCP360_SDK/ /opt/wcp360/WCP360_SDK/
COPY WCP360_Plugins/wcp360-reference-report/ /opt/wcp360/WCP360_Plugins/wcp360-reference-report/
COPY WCP360_Build/verification/candidate-linux-smoke.sh /opt/wcp360/WCP360_Build/verification/candidate-linux-smoke.sh
COPY WCP360_Build/verification/candidate-provisioning-smoke.sh WCP360_Build/verification/candidate-provisioning-fixture.php /opt/wcp360/WCP360_Build/verification/
COPY --from=node /out/wcp-candidate-node /usr/local/libexec/wcp360/wcp-candidate-node
COPY --from=planner /out/wcp-plan /usr/local/libexec/wcp360/wcp-plan
ENV APP_ENV=dev APP_DEBUG=0 WCP_CANDIDATE=1 WCP_CONTEXT=user WCP_PUBLIC_HOST=user.localhost WCP_PLANNER=/usr/local/libexec/wcp360/wcp-plan
RUN APP_SECRET=build-only-do-not-use-at-runtime DATABASE_URL=sqlite:///:memory: php bin/console importmap:install \
    && APP_SECRET=build-only-do-not-use-at-runtime DATABASE_URL=sqlite:///:memory: php bin/console asset-map:compile \
    && mkdir -p var/cache var/log \
    && chmod 1777 var var/cache var/log
EXPOSE 8080
USER 10001:10001
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
