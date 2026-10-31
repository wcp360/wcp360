# Extends the locally built candidate only for browser verification.
FROM wcp360-candidate
USER root
RUN apt-get update && apt-get install -y --no-install-recommends chromium chromium-driver \
    && rm -rf /var/lib/apt/lists/*
# Chromium runs as non-root. This disposable container uses its outer isolation;
# no-sandbox is scoped to the test browser, never to hosted applications.
ENV PANTHER_CHROME_BINARY=/usr/bin/chromium PANTHER_NO_SANDBOX=1 PANTHER_CHROME_ARGUMENTS=--disable-dev-shm-usage
USER 10001:10001
