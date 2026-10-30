# V4.0.21.18 --- Database Ownership / Grants / Least Privilege

Database principal ownership is tenant-bound. A principal cannot receive
grants to another tenant's database.

Grant policy uses a typed capability set normalized across providers;
backend differences remain adapter capabilities. `ALL` is
expanded/validated according to policy rather than becoming an unchecked
future privilege wildcard.

Administrative/root DB credentials are never exposed to WCP-USER and are
not used for application connection strings. Account suspension may lock
access according to service policy without deleting DB data.
