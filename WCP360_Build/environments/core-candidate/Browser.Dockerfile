# Independent real-browser verification on Debian 13 / distribution PHP 8.4.
FROM debian:trixie-slim
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates unzip php8.4-cli php8.4-curl php8.4-intl php8.4-mbstring \
    php8.4-xml php8.4-pgsql php8.4-sqlite3 php8.4-zip chromium chromium-driver \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2.11.1 /usr/bin/composer /usr/local/bin/composer
WORKDIR /opt/wcp360/WCP360_Core
COPY WCP360_Core/composer.json WCP360_Core/composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts
COPY WCP360_Core/ ./
COPY WCP360_SDK/ /opt/wcp360/WCP360_SDK/
COPY WCP360_Plugins/wcp360-reference-report/ /opt/wcp360/WCP360_Plugins/wcp360-reference-report/
COPY WCP360_Build/verification/candidate-browser-smoke.sh /opt/wcp360/candidate-browser-smoke.sh
ENV APP_ENV=dev APP_DEBUG=0 WCP_CANDIDATE=1 WCP_CONTEXT=user WCP_PUBLIC_HOST=user.localhost WCP_PLANNER=/usr/local/libexec/wcp360/wcp-plan
RUN APP_SECRET=build-only-browser-secret DATABASE_URL=sqlite:///:memory: php bin/console importmap:install \
    && APP_SECRET=build-only-browser-secret DATABASE_URL=sqlite:///:memory: php bin/console asset-map:compile \
    && mkdir -p var/cache var/log \
    && chmod 1777 var var/cache var/log \
    && install -d -o 10001 -g 10001 -m 0700 /tmp/wcp-browser /tmp/wcp-browser/config /tmp/wcp-browser/cache
# Test-only browser settings inside the disposable, non-root container.
ENV HOME=/tmp/wcp-browser XDG_CONFIG_HOME=/tmp/wcp-browser/config XDG_CACHE_HOME=/tmp/wcp-browser/cache
ENV PANTHER_CHROME_BINARY=/usr/bin/chromium PANTHER_NO_SANDBOX=1 PANTHER_CHROME_ARGUMENTS="--disable-dev-shm-usage --enable-logging --log-file=/tmp/wcp-browser/chromium.log"
USER 10001:10001
