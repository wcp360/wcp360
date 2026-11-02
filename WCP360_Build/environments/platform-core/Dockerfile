# Disposable development verification only; this is not a host installer or release image.
# PHP_VERSION and PHP_SHA256 are supplied from Core's runtime-providers.json.
ARG BASE_IMAGE=debian:13-slim
FROM golang:1.27.3-trixie AS planner
WORKDIR /src
COPY WCP360_Core/agents/wcpd/candidate/planner/ ./
RUN CGO_ENABLED=0 go build -trimpath -o /out/wcp-plan ./cmd/wcp-plan

FROM ${BASE_IMAGE} AS platform-core
ARG PHP_VERSION
ARG PHP_SHA256
ENV WCP_PHP_PREFIX=/opt/wcp360/runtime/php/8.4
ENV PATH="/opt/wcp360/runtime/php/8.4/bin:/opt/wcp360/runtime/php/8.4/sbin:/usr/local/go/bin:${PATH}"

# Build against the selected distribution's own libraries. A PHP binary copied from
# another distribution would not verify this target's libc/OpenSSL/ICU combination.
RUN printf '#!/bin/sh\nexit 101\n' > /usr/sbin/policy-rc.d \
    && chmod 0755 /usr/sbin/policy-rc.d \
    && apt-get -o Acquire::Retries=2 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 update \
    && DEBIAN_FRONTEND=noninteractive apt-get -o Acquire::Retries=2 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 install -y --no-install-recommends \
        autoconf bison build-essential ca-certificates curl dpkg-dev file git nginx pkg-config re2c tar unzip util-linux xz-utils \
        libargon2-dev libcurl4-openssl-dev libicu-dev libonig-dev libpq-dev libreadline-dev libsodium-dev libsqlite3-dev libssl-dev libxml2-dev libzip-dev zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN set -eu; \
    case "${PHP_VERSION:-}" in \
        8.4.[0-9]|8.4.[0-9][0-9]|8.4.[0-9][0-9][0-9]) ;; \
        *) echo 'A PHP 8.4 patch version from runtime-providers.json is required.' >&2; exit 1 ;; \
    esac; \
    printf '%s\n' "${PHP_SHA256:-}" | grep -Eq '^[0-9a-f]{64}$' \
        || { echo 'The reviewed PHP source SHA-256 is required.' >&2; exit 1; }; \
    mkdir -p /usr/src/wcp360-php "$WCP_PHP_PREFIX/etc/conf.d"; \
    curl --fail --show-error --silent --location --proto '=https' --proto-redir '=https' --retry 2 --connect-timeout 20 --max-time 180 \
        "https://www.php.net/distributions/php-${PHP_VERSION}.tar.xz" -o /usr/src/wcp360-php.tar.xz; \
    printf '%s  %s\n' "$PHP_SHA256" /usr/src/wcp360-php.tar.xz | sha256sum -c -; \
    tar -xJf /usr/src/wcp360-php.tar.xz --strip-components=1 -C /usr/src/wcp360-php; \
    cd /usr/src/wcp360-php; \
    export CFLAGS='-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'; \
    export CPPFLAGS="$CFLAGS"; \
    export LDFLAGS='-Wl,-O1 -pie'; \
    ./configure \
        --prefix="$WCP_PHP_PREFIX" \
        --sysconfdir="$WCP_PHP_PREFIX/etc" \
        --with-config-file-path="$WCP_PHP_PREFIX/etc" \
        --with-config-file-scan-dir="$WCP_PHP_PREFIX/etc/conf.d" \
        --build="$(dpkg-architecture --query DEB_BUILD_GNU_TYPE)" \
        --with-libdir="lib/$(dpkg-architecture --query DEB_BUILD_MULTIARCH)" \
        --enable-option-checking=fatal \
        --with-pic \
        --enable-fpm --with-fpm-user=www-data --with-fpm-group=www-data \
        --enable-intl --enable-mbstring --enable-opcache --enable-pcntl \
        --with-curl --with-iconv --with-openssl --with-password-argon2 \
        --with-pdo-pgsql --with-pdo-sqlite --with-sqlite3 \
        --with-readline --with-sodium --with-zip --with-zlib \
        --disable-cgi --disable-phpdbg --without-pear; \
    make -j2; \
    make install; \
    cp php.ini-production "$WCP_PHP_PREFIX/etc/php.ini"; \
    cp "$WCP_PHP_PREFIX/etc/php-fpm.conf.default" "$WCP_PHP_PREFIX/etc/php-fpm.conf"; \
    cp "$WCP_PHP_PREFIX/etc/php-fpm.d/www.conf.default" "$WCP_PHP_PREFIX/etc/php-fpm.d/www.conf"; \
    printf 'zend_extension=opcache\n' > "$WCP_PHP_PREFIX/etc/conf.d/opcache.ini"; \
    printf 'date.timezone=UTC\nexpose_php=Off\nmemory_limit=512M\n' > "$WCP_PHP_PREFIX/etc/conf.d/verification.ini"; \
    test "$(php -r 'echo PHP_VERSION;')" = "$PHP_VERSION"; \
    php -r 'foreach (["ctype","iconv","PDO","sodium","intl","mbstring","openssl","dom","xml","SimpleXML","xmlreader","xmlwriter","tokenizer","pdo_pgsql","pdo_sqlite","curl","zip","Zend OPcache","posix","pcntl"] as $extension) { if (!extension_loaded($extension)) { fwrite(STDERR, "Missing PHP extension: $extension\n"); exit(1); } }'; \
    php-fpm --version; \
    rm -rf /usr/src/wcp360-php /usr/src/wcp360-php.tar.xz

COPY --from=composer:2.11.1 /usr/bin/composer /usr/local/bin/composer
WORKDIR /opt/wcp360/WCP360_Core
COPY WCP360_Core/composer.json WCP360_Core/composer.lock ./
COPY WCP360_Core/config/platforms/runtime-providers.json ./config/platforms/runtime-providers.json
RUN php -r '$m=json_decode(file_get_contents("config/platforms/runtime-providers.json"),true,16,JSON_THROW_ON_ERROR); if ($m["schema"]!=="wcp360.candidate-runtime-providers/1" || $m["php"]["version"]!==PHP_VERSION || $m["php"]["sha256"]!==$argv[1] || $m["php"]["source_url"]!==("https://www.php.net/distributions/php-".PHP_VERSION.".tar.xz") || $m["php"]["prefix"]!==getenv("WCP_PHP_PREFIX")) { fwrite(STDERR,"Built PHP does not match the runtime provider manifest.\n"); exit(1); }' "$PHP_SHA256"
RUN composer install --no-interaction --prefer-dist --no-progress --no-scripts
COPY WCP360_Core/ ./
COPY WCP360_SDK/ /opt/wcp360/WCP360_SDK/
COPY WCP360_Plugins/wcp360-reference-report/ /opt/wcp360/WCP360_Plugins/wcp360-reference-report/
COPY WCP360_Build/verification/candidate-platform-core.sh WCP360_Build/verification/candidate-platform-core-fixture.php WCP360_Build/verification/candidate-demo-application-smoke.php WCP360_Build/verification/candidate-demo-client.php WCP360_Build/verification/candidate-linux-smoke.sh /opt/wcp360/WCP360_Build/verification/
COPY --from=planner /out/wcp-plan /usr/local/libexec/wcp360/wcp-plan
COPY --from=planner /usr/local/go/ /usr/local/go/
ENV APP_ENV=dev APP_DEBUG=0 WCP_CANDIDATE=1 WCP_CONTEXT=user WCP_PUBLIC_HOST=user.localhost WCP_PLANNER=/usr/local/libexec/wcp360/wcp-plan \
    GOTOOLCHAIN=local GOCACHE=/opt/wcp360/WCP360_Core/var/go-cache GOMODCACHE=/opt/wcp360/WCP360_Core/var/go-mod-cache \
    COMPOSER_HOME=/opt/wcp360/WCP360_Core/var/composer COMPOSER_CACHE_DIR=/opt/wcp360/WCP360_Core/var/composer-cache
RUN APP_SECRET=build-only-do-not-use-at-runtime DATABASE_URL=sqlite:///:memory: php bin/console importmap:install \
    && APP_SECRET=build-only-do-not-use-at-runtime DATABASE_URL=sqlite:///:memory: php bin/console asset-map:compile \
    && mkdir -p var/cache var/log \
    && chmod 1777 var var/cache var/log \
    && chown -R 10001:10001 /opt/wcp360/WCP360_Core /opt/wcp360/WCP360_SDK /opt/wcp360/WCP360_Plugins/wcp360-reference-report
USER 10001:10001
CMD ["bash", "/opt/wcp360/WCP360_Build/verification/candidate-platform-core.sh"]
