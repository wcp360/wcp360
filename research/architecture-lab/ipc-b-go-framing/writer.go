package ipcbframe

import (
	"io"
)

// WriteRequestFrame validates a complete research request before writing any
// bytes. The input must not be mutated concurrently. A write failure can leave
// a partial frame: the caller must discard the transport and must not blindly
// retry. This function provides no authentication, deadline or dispatch.
func WriteRequestFrame(w io.Writer, frame []byte) error {
	if _, err := DecodeRequestFrame(frame); err != nil {
		return err
	}
	return writeValidatedFrame(w, frame)
}

// WriteResponseFrame validates a complete research response before writing.
// A successful write proves neither delivery nor execution of an operation.
func WriteResponseFrame(w io.Writer, frame []byte) error {
	if _, err := DecodeResponseFrame(frame); err != nil {
		return err
	}
	return writeValidatedFrame(w, frame)
}

func writeValidatedFrame(w io.Writer, frame []byte) error {
	n, err := w.Write(frame)
	if err != nil {
		return err
	}
	if n != len(frame) {
		return io.ErrShortWrite
	}
	return nil
}
