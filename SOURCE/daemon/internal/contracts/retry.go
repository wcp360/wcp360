package contracts

// ReadFailure contains transport observations, never caller-provided policy.
// ResponseExposed means any response was delivered to the API consumer.
// Retries counts retries already attempted, excluding the initial attempt.
type ReadFailure struct {
	Retries         int
	TransportFailed bool
	HTTPStatus      int
	ResponseExposed bool
	ErrorCode       ErrorCode
}

// CanRetryRead applies only API v1.0 RR: at most two retries before any response
// is exposed, following transport failure or 429/503. RK and RN are deliberately
// excluded: durable idempotency and reconciliation require persistence.
// It returns a scheduling decision, performs no I/O, and supplies no backoff.
func (c Catalog) CanRetryRead(id APIID, failure ReadFailure) bool {
	a, ok := c.API(id)
	if !ok || a.Retry != "RR" || failure.Retries < 0 || failure.Retries >= 2 || failure.ResponseExposed {
		return false
	}
	// Unknown errors, all authorization/validation errors and uncertain
	// outcomes are never retryable. Only canonical transient codes qualify.
	if failure.ErrorCode != "" {
		if !c.KnownError(failure.ErrorCode) || failure.ErrorCode != "RATE_LIMITED" && failure.ErrorCode != "SERVICE_UNAVAILABLE" {
			return false
		}
	}
	if failure.HTTPStatus == 429 || failure.HTTPStatus == 503 {
		return true
	}
	return failure.HTTPStatus == 0 && failure.TransportFailed && failure.ErrorCode == ""
}
