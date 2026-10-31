# Inert hosting-plan builder

Development candidate, standard-library-only Go 1.27.3. Build cmd/wcp-plan into Core var/bin, which is ignored by Git. Input is one bounded JSON object on stdin; output is a versioned Nginx/PHP-FPM plan. No network listener, database, command execution, filesystem mutation or privileged operation is implemented.

This is not the accepted wcpd daemon, the IPC-B protocol or wcp-privd. The reviewed inert daemon and domain candidates remain on their original branches. Their acceptance evidence is not transferred by this work.

Template rendering has one owner here. PHP invokes a fixed configured executable with an argv array and bounded timeout; user input cannot choose a command, path, Unix identity or service. Only UUIDv7 identifiers and lowercase ASCII .test hostnames are accepted. Linux resource locations and Unix names are generated from those validated identifiers.

Run go test ./... and go vet ./.... The build verification also feeds the emitted configuration to real Nginx and PHP-FPM inside an isolated Debian 13 container. Passing a syntax check does not prove service activation, tenant isolation or production correctness.
