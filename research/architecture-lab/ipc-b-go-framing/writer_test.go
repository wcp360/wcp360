package ipcbframe

import (
	"bytes"
	"errors"
	"io"
	"testing"
)

type recordingWriter struct {
	n int
	err error
	calls int
}

func (w *recordingWriter) Write(p []byte) (int, error) {
	w.calls++
	return w.n, w.err
}

func TestWriteRequestRoundTrip(t *testing.T) {
	frame := fixtureFrame(t, fixture())
	var out bytes.Buffer
	if err := WriteRequestFrame(&out, frame); err != nil {
		t.Fatal(err)
	}
	if !bytes.Equal(out.Bytes(), frame) {
		t.Fatal("wire bytes changed")
	}
	if _, err := ReadRequestFrame(&out); err != nil {
		t.Fatal(err)
	}
}

func responseWriteFixture() []byte {
	return framed([]byte(`{"protocol_version":"WCP360-IPC-B/1","request_id":"request-1","correlation_id":"correlation-1","ipc_operation_id":"nginx_vhost.validate","outcome":"UNKNOWN_OUTCOME","error_code":"UNKNOWN_OUTCOME","reconciliation_required":true}`))
}

func TestWriteResponseRoundTrip(t *testing.T) {
	frame := responseWriteFixture()
	var out bytes.Buffer
	if err := WriteResponseFrame(&out, frame); err != nil {
		t.Fatal(err)
	}
	if !bytes.Equal(out.Bytes(), frame) {
		t.Fatal("wire bytes changed")
	}
	response, err := ReadResponseFrame(&out)
	if err != nil {
		t.Fatal(err)
	}
	if !response.ReconciliationRequired {
		t.Fatal("reconciliation flag lost")
	}
}

func TestWriteInvalidFramesHasNoOutput(t *testing.T) {
	for _, write := range []func(io.Writer, []byte) error{WriteRequestFrame, WriteResponseFrame} {
		for _, frame := range [][]byte{nil, {0, 0, 0, 0}, framed([]byte("{}")), framed([]byte(`{"x":1,"x":2}`))} {
			w := &recordingWriter{}
			if err := write(w, frame); err == nil || w.calls != 0 {
				t.Fatalf("invalid frame reached writer: err=%v calls=%d", err, w.calls)
			}
		}
	}
}

func TestWriteFailureDoesNotRetry(t *testing.T) {
	sentinel := errors.New("transport failure")
	for _, item := range []struct {
		name string
		frame []byte
		write func(io.Writer, []byte) error
	}{
		{"request", fixtureFrame(t, fixture()), WriteRequestFrame},
		{"response", responseWriteFixture(), WriteResponseFrame},
	} {
		t.Run(item.name, func(t *testing.T) {
			for _, tc := range []struct {
				name string
				n int
				err error
				want error
			}{
				{"zero", 0, nil, io.ErrShortWrite},
				{"partial", 2, nil, io.ErrShortWrite},
				{"negative-count", -1, nil, io.ErrShortWrite},
				{"oversized-count", len(item.frame)+1, nil, io.ErrShortWrite},
				{"partial-error", 2, sentinel, sentinel},
				{"full-count-error", len(item.frame), sentinel, sentinel},
			} {
				t.Run(tc.name, func(t *testing.T) {
					w := &recordingWriter{n: tc.n, err: tc.err}
					if err := item.write(w, item.frame); !errors.Is(err, tc.want) {
						t.Fatalf("got %v want %v", err, tc.want)
					}
					if w.calls != 1 {
						t.Fatalf("unexpected retry: calls=%d", w.calls)
					}
				})
			}
		})
	}
}
