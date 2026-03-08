# ======================================================================
<<<<<<< HEAD
# WCP 360 – Modern Web Control Panel
# Creator: HADJ RAMDANE Yacine | V0.1.0
# File: Makefile
# ======================================================================

BINARY   = wcp360
BUILD_DIR = ./build
CMD_DIR   = ./cmd/wcp360

.PHONY: build run test test-cover check vet lint tidy clean install docker

## build: Compile the binary
build:
	@mkdir -p $(BUILD_DIR)
	go build -ldflags="-s -w" -o $(BUILD_DIR)/$(BINARY) $(CMD_DIR)
	@echo "Built → $(BUILD_DIR)/$(BINARY)"

## run: Run in development mode
run:
	WCP360_ENV=development go run $(CMD_DIR)

## test: Run all tests with race detector
test:
	go test -race -count=1 ./...

## test-cover: Run tests and open HTML coverage report
test-cover:
	go test -race -count=1 -coverprofile=coverage.out ./...
	go tool cover -html=coverage.out -o coverage.html
	@echo "Coverage report → coverage.html"

## check: go vet + tests (CI-safe, no golangci-lint needed)
check: vet test

## vet: Run go vet
vet:
	go vet ./...

## lint: Run golangci-lint (installs if missing)
lint:
	@which golangci-lint > /dev/null 2>&1 || \
		(echo "Installing golangci-lint..." && \
		 go install github.com/golangci/golangci-lint/cmd/golangci-lint@latest)
	golangci-lint run ./...

## tidy: Download and tidy modules (generates go.sum)
tidy:
	go mod tidy
	@echo "go.sum generated"

## clean: Remove build artifacts
clean:
	rm -rf $(BUILD_DIR) coverage.out coverage.html

## install: Install binary to /usr/local/bin
install: build
	sudo cp $(BUILD_DIR)/$(BINARY) /usr/local/bin/$(BINARY)
	@echo "Installed → /usr/local/bin/$(BINARY)"

## docker: Build a minimal Docker image
docker:
	docker build -t wcp360:v0.1.0 .

help:
	@grep -E '^## ' Makefile | sed 's/## /  /'
=======
# WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
# ======================================================================
# Creator: HADJ RAMDANE Yacine
# Contact: yacine@wcp360.com
# Version: V0.0.5
# Website: https://www.wcp360.com
# File: Makefile
# Description: Developer workflow — build, run, test, lint, docs.
# ======================================================================

VERSION  := $(shell git describe --tags --always --dirty 2>/dev/null || echo "v0.0.5-dev")
BINARY   := bin/wcp360
MAIN     := ./cmd/wcp360
LDFLAGS  := -ldflags="-s -w -X main.version=$(VERSION)"

.PHONY: all build run run-direct test test-cover lint tidy clean docs docs-build help

help:
	@grep -E '^## ' Makefile | sed 's/## /  /'

## build: Compile the binary
build:
	@mkdir -p bin
	go build $(LDFLAGS) -o $(BINARY) $(MAIN)
	@echo "✅  Built: $(BINARY)"

## run: Run with hot-reload (requires air)
run:
	@which air > /dev/null || (echo "Install air: go install github.com/air-verse/air@latest" && exit 1)
	air

## run-direct: Run without hot-reload
run-direct:
	go run $(MAIN)

## test: Run all tests with race detector
test:
	go test -race -count=1 -v ./...

## test-cover: Run tests with HTML coverage report
test-cover:
	go test -race -coverprofile=coverage.out ./...
	go tool cover -html=coverage.out -o coverage.html
	@echo "📊  Coverage: coverage.html"

## lint: Run golangci-lint
lint:
	golangci-lint run ./...

## tidy: Clean up go.mod + go.sum
tidy:
	go mod tidy

## clean: Remove compiled artifacts
clean:
	rm -rf bin/ coverage.out coverage.html

## docs: Serve documentation locally
docs:
	cd docs && mkdocs serve

## docs-build: Build static documentation
docs-build:
	cd docs && mkdocs build
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
