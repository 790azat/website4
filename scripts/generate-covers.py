#!/usr/bin/env python3
"""Generates article cover images with the Gemini API.

For each English article in resources/data/articles/{slug}.md it builds the
prompt from resources/data/cover-prompt.txt (the "{headline}" placeholder is
replaced by the article title), asks Gemini for a 16:9 image and saves it as
public/images/articles/{slug}.webp, the path the site already reads covers from.

By default only articles without a cover are processed. Pass slugs (or full
article titles/URLs) to regenerate specific covers.

    GEMINI_API_KEY=... python3 scripts/generate-covers.py            # missing only
    GEMINI_API_KEY=... python3 scripts/generate-covers.py some-slug  # regenerate one

Needs Pillow (pip install pillow) to convert the result to WebP.
"""

import argparse
import base64
import io
import json
import os
import re
import sys
import time
import urllib.error
import urllib.request
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
ARTICLES = ROOT / "resources/data/articles"
COVERS = ROOT / "public/images/articles"
PROMPT_FILE = ROOT / "resources/data/cover-prompt.txt"
EXTENSIONS = ("webp", "jpg", "jpeg", "png")
SIZE = (1376, 768)  # matches the existing covers (16:9)
API = "https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent"


def article_title(path: Path) -> str:
    text = path.read_text(encoding="utf-8")
    match = re.search(r"^title:\s*(.+)$", text, re.MULTILINE)
    if not match:
        raise ValueError(f"{path.name}: no title in front matter")
    value = match.group(1).strip()
    return json.loads(value) if value.startswith('"') else value


def has_cover(slug: str) -> bool:
    return any((COVERS / f"{slug}.{ext}").exists() for ext in EXTENSIONS)


def resolve(wanted: list[str], articles: dict[str, str]) -> list[str]:
    """Accepts slugs, article URLs or exact titles."""
    by_title = {title.strip().lower(): slug for slug, title in articles.items()}
    slugs = []
    for item in wanted:
        item = item.strip()
        if not item:
            continue
        slug = item.rstrip("/").rsplit("/", 1)[-1].removesuffix(".md")
        slug = slug if slug in articles else by_title.get(item.lower())
        if not slug:
            sys.exit(f"Unknown article: {item!r}")
        slugs.append(slug)
    return slugs


def request(model: str, key: str, body: dict) -> dict:
    req = urllib.request.Request(
        API.format(model=model),
        data=json.dumps(body).encode(),
        headers={"Content-Type": "application/json", "x-goog-api-key": key},
    )
    with urllib.request.urlopen(req, timeout=180) as resp:
        return json.load(resp)


# Newer and older API versions spell the aspect-ratio option differently;
# try each, and as a last resort ask without one and crop afterwards.
CONFIGS = [
    {"responseModalities": ["IMAGE"], "imageConfig": {"aspectRatio": "16:9"}},
    {"responseModalities": ["IMAGE"], "responseFormat": {"image": {"aspectRatio": "16:9"}}},
    {"responseModalities": ["TEXT", "IMAGE"]},
]


def generate(prompt: str, model: str, key: str) -> Image.Image:
    last_error = None
    for index, config in enumerate(CONFIGS):
        for attempt in range(4):
            try:
                data = request(model, key, {
                    "contents": [{"parts": [{"text": prompt}]}],
                    "generationConfig": config,
                })
            except urllib.error.HTTPError as err:
                detail = err.read().decode(errors="replace")[:500]
                last_error = f"HTTP {err.code}: {detail}"
                if err.code == 400:
                    break  # config not accepted, try the next spelling
                if err.code in (429, 500, 502, 503, 504):
                    time.sleep(10 * (attempt + 1))
                    continue
                raise SystemExit(last_error)  # bad key, no billing, unknown model
            except (urllib.error.URLError, TimeoutError) as err:
                last_error = str(err)
                time.sleep(10 * (attempt + 1))
                continue
            for candidate in data.get("candidates", []):
                for part in candidate.get("content", {}).get("parts", []):
                    inline = part.get("inlineData") or part.get("inline_data")
                    if inline and inline.get("data"):
                        if index:  # remember the spelling that worked
                            CONFIGS.insert(0, CONFIGS.pop(index))
                        return Image.open(io.BytesIO(base64.b64decode(inline["data"])))
            last_error = "no image in response: " + json.dumps(data)[:500]
            time.sleep(5)
    raise RuntimeError(last_error)


def save(image: Image.Image, slug: str) -> Path:
    image = image.convert("RGB")
    # Center-crop to 16:9, then scale to the size the other covers use.
    w, h = image.size
    target = SIZE[0] / SIZE[1]
    if w / h > target:
        new_w = round(h * target)
        image = image.crop(((w - new_w) // 2, 0, (w - new_w) // 2 + new_w, h))
    elif w / h < target:
        new_h = round(w / target)
        image = image.crop((0, (h - new_h) // 2, w, (h - new_h) // 2 + new_h))
    image = image.resize(SIZE, Image.LANCZOS)
    for ext in EXTENSIONS:  # drop an older cover in another format
        (COVERS / f"{slug}.{ext}").unlink(missing_ok=True)
    path = COVERS / f"{slug}.webp"
    image.save(path, "WEBP", quality=82, method=6)
    return path


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("articles", nargs="*", help="slugs, URLs or titles; separate with commas or new lines")
    parser.add_argument("--model", default=os.environ.get("GEMINI_IMAGE_MODEL") or "gemini-3.1-flash-image")
    parser.add_argument("--limit", type=int, default=0, help="stop after this many images (0 = no limit)")
    args = parser.parse_args()

    key = os.environ.get("GEMINI_API_KEY", "").strip()
    if not key:
        sys.exit("GEMINI_API_KEY is not set")

    articles = {p.stem: article_title(p) for p in sorted(ARTICLES.glob("*.md"))}
    wanted = [s for a in args.articles for s in re.split(r"[,\n]", a)]
    slugs = resolve(wanted, articles) if any(w.strip() for w in wanted) else [
        slug for slug in articles if not has_cover(slug)
    ]
    if args.limit:
        slugs = slugs[: args.limit]
    if not slugs:
        print("Every article already has a cover. Nothing to do.")
        return

    template = PROMPT_FILE.read_text(encoding="utf-8").strip()
    COVERS.mkdir(parents=True, exist_ok=True)
    failed = []
    for i, slug in enumerate(slugs, 1):
        prompt = template.replace("{headline}", articles[slug])
        print(f"[{i}/{len(slugs)}] {articles[slug]}", flush=True)
        try:
            path = save(generate(prompt, args.model, key), slug)
            print(f"    saved {path.relative_to(ROOT)}", flush=True)
        except Exception as err:  # keep going; report at the end
            print(f"    FAILED: {err}", flush=True)
            failed.append(slug)

    print(f"\nDone: {len(slugs) - len(failed)} generated, {len(failed)} failed.")
    if failed:
        print("Failed: " + ", ".join(failed))
        if len(failed) == len(slugs):
            sys.exit(1)


if __name__ == "__main__":
    main()
