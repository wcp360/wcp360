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

func main() { os.Exit(run()) }

func run() int {
	if len(os.Args) < 2 {
		return 2
	}
	mode := os.Args[1]
	if mode == "arm-vm" {
		if len(os.Args) != 2 || p.ArmVM() != nil {
			fmt.Fprintln(os.Stderr, "disposable VM boot authorization rejected")
			return 78
		}
		return 0
	}
	fs := flag.NewFlagSet(mode, flag.ExitOnError)
	agentUID := fs.Uint("agent-uid", 0, "dedicated unprivileged agent UID")
	workerUID := fs.Uint("worker-uid", 0, "dedicated unprivileged worker UID")
	ipcGID := fs.Uint("ipc-gid", 0, "dedicated worker-agent IPC group")
	fs.Parse(os.Args[2:])
	if fs.NArg() != 0 || *agentUID < 1000 || *workerUID < 1000 || *ipcGID < 1000 || *ipcGID > 2147483647 || *agentUID == *workerUID || *agentUID > 2147483647 || *workerUID > 2147483647 || p.Guard() != nil {
		fmt.Fprintln(os.Stderr, "isolated candidate identities/execution profile required")
		return 78
	}
	ctx, stop := signal.NotifyContext(context.Background(), syscall.SIGTERM, syscall.SIGINT)
	defer stop()
	var e error
	switch mode {
	case "broker":
		if os.Geteuid() != 0 {
			return 78
		}
		lock, lockErr := p.AcquireBrokerLock()
		if lockErr != nil {
			fmt.Fprintln(os.Stderr, lockErr)
			return 78
		}
		defer lock.Close()
		if e = p.PrepareSocketDirs(int(*agentUID), int(*ipcGID)); e != nil {
			fmt.Fprintln(os.Stderr, "socket boundary rejected")
			return 78
		}
		broker := new(p.Broker)
		defer broker.Close()
		if e = broker.Prepare(); e == nil {
			e = broker.Recover()
		}
		if e == nil {
			serviceCtx, cancel := context.WithCancel(ctx)
			defer cancel()
			failure := make(chan error, 1)
			go func() {
				select {
				case err := <-broker.Failures():
					failure <- err
					cancel()
				case <-ctx.Done():
				}
			}()
			e = p.Serve(serviceCtx, p.BrokerSocket, uint32(*agentUID), 0, int(*agentUID), broker.Handle)
			if ctx.Err() != nil {
				e = nil
			} else if e == nil {
				select {
				case e = <-failure:
				default:
				}
			}
		}
	case "agent":
		if strconv.Itoa(os.Geteuid()) != strconv.FormatUint(uint64(*agentUID), 10) {
			return 78
		}
		e = p.Serve(ctx, p.AgentSocket, uint32(*workerUID), int(*agentUID), int(*ipcGID), p.Forward)
	default:
		return 2
	}
	if e != nil {
		fmt.Fprintln(os.Stderr, "candidate service stopped:", e)
		return 1
	}
	return 0
}
