# ======================================================================
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
