// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/api/handlers/helpers.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/helpers.go
// Description: Shared HTTP helpers — JSON writers, pagination, param parsers.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package handlers

import (
	"encoding/json"
	"fmt"
	"log/slog"
	"math"
	"net/http"
	"strconv"
<<<<<<< HEAD
	"strings"

	"github.com/wcp360/wcp360/internal/database/queries"
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
)

type errorResponse struct{ Error string `json:"error"` }

type Pagination struct {
	Page       int `json:"page"`
	PerPage    int `json:"per_page"`
	Total      int `json:"total"`
	TotalPages int `json:"total_pages"`
}

func NewPagination(page, perPage, total int) Pagination {
<<<<<<< HEAD
	tp := 1
	if perPage > 0 { tp = int(math.Ceil(float64(total) / float64(perPage))) }
	if tp < 1 { tp = 1 }
	return Pagination{Page: page, PerPage: perPage, Total: total, TotalPages: tp}
=======
	totalPages := 1
	if perPage > 0 {
		totalPages = int(math.Ceil(float64(total) / float64(perPage)))
	}
	if totalPages < 1 { totalPages = 1 }
	return Pagination{Page: page, PerPage: perPage, Total: total, TotalPages: totalPages}
}

type paginatedResponse[T any] struct {
	Data       []T        `json:"data"`
	Pagination Pagination `json:"pagination"`
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}

func writeJSON(w http.ResponseWriter, status int, v any) {
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	if err := json.NewEncoder(w).Encode(v); err != nil {
		slog.Warn("writeJSON: encode error", "err", err)
	}
}

func writeError(w http.ResponseWriter, status int, message string) {
	writeJSON(w, status, errorResponse{Error: message})
}

<<<<<<< HEAD
=======
func writePaginated[T any](w http.ResponseWriter, items []T, pag Pagination) {
	if items == nil { items = []T{} }
	writeJSON(w, http.StatusOK, paginatedResponse[T]{Data: items, Pagination: pag})
}

>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
func parseIDParam(w http.ResponseWriter, r *http.Request, name string) (int64, bool) {
	raw := r.PathValue(name)
	if raw == "" {
		writeError(w, http.StatusBadRequest, fmt.Sprintf("missing path parameter: %s", name))
		return 0, false
	}
	id, err := strconv.ParseInt(raw, 10, 64)
	if err != nil || id <= 0 {
		writeError(w, http.StatusBadRequest, fmt.Sprintf("invalid %s: must be a positive integer", name))
		return 0, false
	}
	return id, true
}

func parsePaginationParams(r *http.Request) (page, perPage int) {
	page, perPage = 1, 20
	if v := r.URL.Query().Get("page"); v != "" {
		if n, err := strconv.Atoi(v); err == nil && n >= 1 { page = n }
	}
	if v := r.URL.Query().Get("per_page"); v != "" {
		if n, err := strconv.Atoi(v); err == nil && n >= 1 && n <= 100 { perPage = n }
	}
	return
}

<<<<<<< HEAD
func parseLimit(r *http.Request, def, max int) int {
	v := r.URL.Query().Get("limit")
	if v == "" { return def }
	n, err := strconv.Atoi(v)
	if err != nil || n < 1 { return def }
	if n > max { return max }
	return n
}

func parseFilterParams(r *http.Request) queries.TenantFilter {
	q := r.URL.Query()
	f := queries.TenantFilter{
		Search: strings.TrimSpace(q.Get("search")),
		Status: q.Get("status"),
		Plan:   q.Get("plan"),
	}
	if !map[string]bool{"active": true, "suspended": true, "": true}[f.Status] { f.Status = "" }
	if !map[string]bool{"starter": true, "pro": true, "business": true, "": true}[f.Plan] { f.Plan = "" }
	return f
}

=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
func decodeJSON(w http.ResponseWriter, r *http.Request, v any) bool {
	if err := json.NewDecoder(r.Body).Decode(v); err != nil {
		writeError(w, http.StatusBadRequest, "invalid JSON body: "+err.Error())
		return false
	}
	return true
}
