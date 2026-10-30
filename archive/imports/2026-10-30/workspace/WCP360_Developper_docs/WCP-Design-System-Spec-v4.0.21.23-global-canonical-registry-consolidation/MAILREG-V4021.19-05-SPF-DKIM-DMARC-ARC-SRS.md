# V4.0.21.19 --- SPF / DKIM / DMARC / ARC / SRS

## Deliverability resources

SPF desired policy, DKIM key/selectors, DMARC desired policy and
observed DNS state are distinct.

Operations: `DKIM-ENABLE`, `DKIM-ROTATE`, `DKIM-DISABLE`,
`SPF-POLICY-SET`, `DMARC-POLICY-SET`, `MAIL-AUTH-VERIFY`,
`ARC-POLICY-SET`, `SRS-POLICY-SET`.

Private DKIM material never leaves privileged storage through normal
APIs. DNS publication may be automatic or guidance-only depending on DNS
capability. WCP distinguishes desired record, published record and
externally observed state.

ARC/SRS are provider-capability features, not assumed universally
available.
