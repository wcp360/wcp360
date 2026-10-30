# Candidate standalone test image; not a WCP360 product runtime decision.
ARG PHP_IMAGE
ARG BROWSER_IMAGE
FROM ${BROWSER_IMAGE} AS browsers
FROM ${PHP_IMAGE}
ARG NODE_VERSION
ARG NODE_SHA256
ARG COMPOSER_VERSION
ARG COMPOSER_SHA256
ARG DEBIAN_SNAPSHOT
ENV DEBIAN_FRONTEND=noninteractive PLAYWRIGHT_BROWSERS_PATH=/ms-playwright \
    PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 COMPOSER_ROOT_VERSION=0.3.0-alpha
RUN rm -f /etc/apt/sources.list /etc/apt/sources.list.d/* && \
    printf 'deb [check-valid-until=no] https://snapshot.debian.org/archive/debian/%s/ bookworm main\ndeb [check-valid-until=no] https://snapshot.debian.org/archive/debian-security/%s/ bookworm-security main\n' "$DEBIAN_SNAPSHOT" "$DEBIAN_SNAPSHOT" > /etc/apt/sources.list && \
    apt-get update && apt-get install -y --no-install-recommends python3 libonig-dev libzip-dev unzip xz-utils && \
    docker-php-ext-install -j2 mbstring zip && \
    curl -fsSLo /tmp/node.tar.xz "https://nodejs.org/dist/${NODE_VERSION}/node-${NODE_VERSION}-linux-x64.tar.xz" && \
    echo "${NODE_SHA256}  /tmp/node.tar.xz" | sha256sum -c - && \
    tar -xJf /tmp/node.tar.xz -C /usr/local --strip-components=1 && rm /tmp/node.tar.xz && \
    curl -fsSLo /usr/local/bin/composer "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar" && \
    echo "${COMPOSER_SHA256}  /usr/local/bin/composer" | sha256sum -c - && chmod 755 /usr/local/bin/composer
COPY tests/ci/package*.json /opt/browser/
RUN cd /opt/browser && npm ci --ignore-scripts --no-audit --no-fund && \
    node node_modules/playwright/cli.js install-deps chromium && \
    rm -rf /var/lib/apt/lists/* /root/.npm /root/.cache
COPY --from=browsers /ms-playwright /ms-playwright
COPY . /app
RUN cd /app && composer install --no-plugins --no-scripts --prefer-dist --no-progress && \
    cp -a /app /runtime && rm -rf /runtime/vendor && \
    cd /runtime && composer install --no-dev --no-plugins --no-scripts --prefer-dist --no-progress && \
    rm -rf /root/.cache /root/.composer && \
    groupadd --gid 10001 fmtest && useradd --uid 10001 --gid 10001 --create-home fmtest && \
    mkdir /evidence && chmod 1777 /evidence && \
    dpkg-query -W > /opt/dpkg-inventory.txt
ENV HOME=/tmp/fm-home FM_PLAYWRIGHT_MODULE=/opt/browser/node_modules/playwright
USER 10001:10001
WORKDIR /app
CMD ["python3", "tests/ci/run.py"]
