//go:build linux

package main

import (
	"context"
	"flag"
	"fmt"
	p "github.com/yyogas/wcp360/candidate/provisioning"
	"os"
	"os/signal"
	"strconv"
	"syscall"
)

func main() {
	if len(os.Args) < 2 {
		os.Exit(2)
	}
	mode := os.Args[1]
	fs := flag.NewFlagSet(mode, flag.ExitOnError)
	agentUID := fs.Uint("agent-uid", 0, "dedicated unprivileged agent UID")
	workerUID := fs.Uint("worker-uid", 0, "dedicated unprivileged worker UID")
	ipcGID := fs.Uint("ipc-gid", 0, "dedicated worker-agent IPC group")
	fs.Parse(os.Args[2:])
	if fs.NArg() != 0 || *agentUID < 1000 || *workerUID < 1000 || *ipcGID < 1000 || *ipcGID > 2147483647 || *agentUID == *workerUID || *agentUID > 2147483647 || *workerUID > 2147483647 || p.Guard() != nil {
		fmt.Fprintln(os.Stderr, "isolated candidate identities/execution profile required")
		os.Exit(78)
	}
	ctx, stop := signal.NotifyContext(context.Background(), syscall.SIGTERM, syscall.SIGINT)
	defer stop()
	var e error
	switch mode {
	case "broker":
		if os.Geteuid() != 0 {
			os.Exit(78)
		}
		if e = p.PrepareSocketDirs(int(*agentUID), int(*ipcGID)); e != nil {
			fmt.Fprintln(os.Stderr, "socket boundary rejected")
			os.Exit(78)
		}
		broker := new(p.Broker)
		if e = broker.Prepare(); e == nil {
			defer broker.Close()
			e = p.Serve(ctx, p.BrokerSocket, uint32(*agentUID), 0, int(*agentUID), broker.Handle)
		}
	case "agent":
		if strconv.Itoa(os.Geteuid()) != strconv.FormatUint(uint64(*agentUID), 10) {
			os.Exit(78)
		}
		e = p.Serve(ctx, p.AgentSocket, uint32(*workerUID), int(*agentUID), int(*ipcGID), p.Forward)
	default:
		os.Exit(2)
	}
	if e != nil {
		fmt.Fprintln(os.Stderr, "candidate service stopped:", e)
		os.Exit(1)
	}
}
