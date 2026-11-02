// wcpd is the inert SC-M01 foundation. It starts no daemon or listener.
package main

import (
	"os"

	"github.com/yyogas/wcp360/daemon/internal/cli"
)

func main() {
	os.Exit(cli.Run(os.Args[1:], os.Stdout, os.Stderr))
}
