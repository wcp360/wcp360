// Package cli implements a finite, inert command surface.
// It accepts no configuration, commands, paths, addresses or credentials.
package cli

import "io"

const (
	usage   = "Usage: wcpd [--help | --version | --check]\nSC-M01 inert foundation: no listener, database or privileged operation.\n"
	version = "wcpd: SC-M01 inert foundation (CODE-BASELINE NONE)\n"
	status  = "{\"component\":\"wcpd\",\"mode\":\"inert-foundation\",\"control_transport\":\"disabled\",\"privileged_operations\":\"disabled\"}\n"
)

// Run writes a finite response and returns an exit code. Zero indicates local
// command success, not daemon readiness or authorization to dispatch.
// Invalid input is never echoed because it may contain credentials.
func Run(args []string, stdout, stderr io.Writer) int {
	if len(args) > 1 {
		return reject(stderr)
	}
	message := usage
	if len(args) == 1 {
		switch args[0] {
		case "--help", "-h":
		case "--version":
			message = version
		case "--check":
			message = status
		default:
			return reject(stderr)
		}
	}
	if n, err := io.WriteString(stdout, message); err != nil || n != len(message) {
		return 1
	}
	return 0
}

func reject(stderr io.Writer) int {
	const message = "wcpd: unsupported arguments; use --help\n"
	if n, err := io.WriteString(stderr, message); err != nil || n != len(message) {
		return 1
	}
	return 2
}
