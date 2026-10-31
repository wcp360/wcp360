# Extends the locally built candidate only for browser verification.
FROM wcp360-candidate
USER root
RUN apt-get update && apt-get install -y --no-install-recommends chromium chromium-driver \
    && rm -rf /var/lib/apt/lists/*
RUN install -d -o 10001 -g 10001 -m 0700 /tmp/wcp-browser /tmp/wcp-browser/config /tmp/wcp-browser/cache
# Chromium runs as non-root. This disposable container uses its outer isolation;
# no-sandbox is scoped to the test browser, never to hosted applications.
ENV HOME=/tmp/wcp-browser XDG_CONFIG_HOME=/tmp/wcp-browser/config XDG_CACHE_HOME=/tmp/wcp-browser/cache
ENV PANTHER_CHROME_BINARY=/usr/bin/chromium PANTHER_NO_SANDBOX=1 PANTHER_CHROME_ARGUMENTS="--disable-dev-shm-usage --enable-logging --log-file=/tmp/wcp-browser/chromium.log"
COPY WCP360_Build/verification/candidate-browser-smoke.sh /opt/wcp360/candidate-browser-smoke.sh
USER 10001:10001
