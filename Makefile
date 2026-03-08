# ======================================================================
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
