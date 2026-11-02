"""Repair UTF-8 text that was accidentally decoded as Windows-1252.

Only files whose transformed text scores better are rewritten. The script is
idempotent: running it again leaves already-correct files untouched.
"""

from __future__ import annotations

import argparse
from pathlib import Path


MOJIBAKE_MARKERS = ("Ã", "Â", "â", "ð", "ï¿½", "ï¼", "â‚¬")
TEXT_EXTENSIONS = {".html", ".css", ".js", ".md"}


def score(text: str) -> int:
    return sum(text.count(marker) for marker in MOJIBAKE_MARKERS)


def repair(text: str) -> str:
    candidate = text
    for _ in range(3):
        try:
            repaired = candidate.encode("cp1252").decode("utf-8")
        except (UnicodeEncodeError, UnicodeDecodeError):
            break
        if score(repaired) >= score(candidate):
            break
        candidate = repaired
    return candidate


def repair_chunks(text: str) -> str:
    """Repair per line so valid Unicode elsewhere does not block a repair."""
    chunks = text.splitlines(keepends=True)
    return "".join(repair(chunk) for chunk in chunks)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("root", type=Path)
    parser.add_argument("--check", action="store_true")
    args = parser.parse_args()

    changed: list[Path] = []
    for path in sorted(args.root.rglob("*")):
        if not path.is_file() or path.suffix.lower() not in TEXT_EXTENSIONS:
            continue
        source = path.read_text(encoding="utf-8")
        repaired = repair_chunks(source)
        if score(repaired) < score(source):
            changed.append(path)
            if not args.check:
                path.write_text(repaired, encoding="utf-8", newline="")

    verb = "would repair" if args.check else "repaired"
    print(f"{verb} {len(changed)} file(s)")
    for path in changed[:20]:
        print(path)
    if len(changed) > 20:
        print(f"... and {len(changed) - 20} more")
    return 1 if args.check and changed else 0


if __name__ == "__main__":
    raise SystemExit(main())
