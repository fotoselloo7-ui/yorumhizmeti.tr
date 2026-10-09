#!/usr/bin/env python3
"""Build a PUBLIC, READ-ONLY GitHub Pages tour from the CI-only demo database.

Never connect this script to a live or customer database. Only invoke it against
the isolated MySQL QA fixture that the preview workflow creates.
"""
import html
import os
import pathlib
import re
import shutil
import subprocess
import sys
import urllib.parse
import urllib.request

ORIGIN = os.environ.get("PREVIEW_ORIGIN", "http://127.0.0.1:8006").rstrip("/")
OUT = pathlib.Path(os.environ.get("PREVIEW_OUTPUT", "pages-preview"))
BASE = "/yorumhizmeti.tr/"
PUBLIC = pathlib.Path("well-known/public")

def mysql_column(sql):
    cmd = ["mysql", "--host=127.0.0.1", "--port=3306", "-uroot",
           "-pqa_test_only_password", "-N", "-B", "yorumhizmeti_ci", "-e", sql]
    res = subprocess.run(cmd, capture_output=True, text=True, check=True)
    return [row.strip() for row in res.stdout.splitlines() if row.strip()]

def safe_slug(value):
    return bool(re.fullmatch(r"[a-zA-Z0-9_-]{1,128}", value))

def route_catalog():
    routes = {"", "kategoriler", "hazir-scriptler", "hazir-yazilimlar",
              "blog", "sss", "iletisim",
              "haber-sitesi-scripti", "temizlik-firmasi-scripti-web-site-yazilimi",
              "emlak-scripti-hazir-emlak-sitesi-yazilimi"}
    # Match the LIVE PHP router's active states and slug shapes. Previously the
    # exporter queried non-existent 'published' status, silently dropping blogs.
    sources = [
      ("SELECT slug FROM categories WHERE status='active' LIMIT 180", "kategori/"),
      ("SELECT slug FROM packages WHERE status='active' LIMIT 180", "paket/"),
      ("SELECT slug FROM blog_posts WHERE status='active' LIMIT 90", "blog/"),
      ("SELECT slug FROM blog_categories WHERE status='active' LIMIT 60", "blog/kategori/"),
      ("SELECT slug FROM pages WHERE status='active' LIMIT 60", "sayfa/"),
      ("SELECT slug FROM nv_legacy_script_products WHERE active=1 LIMIT 60", "hazir-scriptler/"),
    ]
    for sql, prefix in sources:
        for slug in mysql_column(sql):
            if safe_slug(slug):
                routes.add(prefix + slug)
    return routes

def mapped_url(raw, routes):
    if not raw.startswith("/") or raw.startswith("//"):
        return raw, False
    if raw.startswith("/assets/") or raw.startswith("/uploads/"):
        return BASE + raw.lstrip("/"), False
    parts = urllib.parse.urlsplit(raw)
    route = urllib.parse.unquote(parts.path).strip("/")
    if route in routes:
        loc = BASE + (route + "/" if route else "")
        # Static Pages has no PHP query filtering. Show the category landing.
        if parts.fragment:
            loc += "#" + parts.fragment
        return loc, False
    if not route and parts.fragment:
        return BASE + "#" + parts.fragment, False
    return "#preview-only", True

def export_html(text, route, all_routes):
    # Expose no synthetic account data, source SQL or HTTP credentials.
    # Captured HTML is from the guest/public storefront only.
    def attr_replace(match):
        name, quote, val = match.group(1), match.group(2), html.unescape(match.group(3))
        new, placeholder = mapped_url(val, all_routes)
        return name + "=" + quote + html.escape(new, quote=True) + quote + (
            " data-preview-only='1'" if placeholder else "")
    text = re.sub(r'\b(href|src|action)=(["\'])(/[^"\']*)\2',
                  attr_replace, text, flags=re.I)
    text = re.sub(r'<link[^>]+rel=(["\'])canonical\1[^>]*>', '', text, flags=re.I)
    text = text.replace("</head>", '<meta name="robots" content="noindex,nofollow">\n</head>', 1)
    if route == "":
        # Transparent preview provenance: don't pass software/blog artwork off
        # as verified private customer references.
        text = text.replace(
          '<div class="nv31-portfolio-grid nv51-portfolio-grid"',
          '<p class="pages-portfolio-note" style="margin:10px 0 18px;font-size:12px;color:#65708c">Portföy önizlemesi: NetVera yazılım ve yayınlanmış içerik örnekleri. Canlı müşteri referansları bu statik demoda senkronize değildir.</p>\n'
          '<div class="nv31-portfolio-grid nv51-portfolio-grid"', 1
        )

    # All demo forms, account operations and payments are intentionally disabled.
    notice = r'''
<div id="pages-preview-toast" role="status" aria-live="polite" hidden style="position:fixed;right:20px;bottom:20px;z-index:999999;max-width:340px;background:#152449;color:#fff;padding:15px 19px;border-radius:12px;box-shadow:0 15px 45px #10183b50;font:600 12px/1.6 Arial,sans-serif">
  Bu bağlantı yalnızca görsel önizlemede bulunmuyor. Gerçek sitede kullanılabilir.
</div>
<div style="position:fixed;z-index:999990;right:12px;top:8px;background:linear-gradient(110deg,#2e5be2,#7940de);color:white;border-radius:999px;padding:7px 12px;font:700 10px Arial,sans-serif;box-shadow:0 7px 23px #172a6a33;pointer-events:none">TASARIM ÖNİZLEMESİ · ÖDEME KAPALI</div>
<script>
(() => {
  const toast = document.getElementById('pages-preview-toast');
  let dismiss;
  const inform = () => {toast.hidden=false;clearTimeout(dismiss);dismiss=setTimeout(()=>{toast.hidden=true},3400)};
  document.addEventListener('click', e => {
    if (e.target.closest('a[data-preview-only],a[href="#preview-only"]')) {
      e.preventDefault();inform();
    }
  }, true);
  document.addEventListener('submit', e => {e.preventDefault();inform()}, true);
})();
</script>
'''
    text = text.replace("</body>", notice + "\n</body>", 1)
    return text

def main():
    if os.environ.get("PAGES_PREVIEW_CI_ONLY") != "1":
        sys.exit("Safety guard: PAGES_PREVIEW_CI_ONLY=1 required. Never export live customer pages.")
    if OUT.exists():
        shutil.rmtree(OUT)
    OUT.mkdir(parents=True)
    routes = route_catalog()
    failures = []
    for route in sorted(routes):
        try:
            request = urllib.request.Request(ORIGIN + "/" + urllib.parse.quote(route, safe="/"),
                                             headers={"User-Agent": "Static-Preview-Builder/1.0"})
            with urllib.request.urlopen(request, timeout=18) as resp:
                if resp.status != 200: raise RuntimeError("HTTP " + str(resp.status))
                doc = resp.read().decode("utf-8")
            if "<html" not in doc.lower(): raise RuntimeError("not HTML")
            page = OUT / route / "index.html" if route else OUT / "index.html"
            page.parent.mkdir(parents=True, exist_ok=True)
            page.write_text(export_html(doc, route, routes), encoding="utf-8")
        except Exception as exc:
            failures.append((route, str(exc)))
    for dirname in ("assets", "uploads"):
        source = PUBLIC / dirname
        if source.exists():
            shutil.copytree(source, OUT / dirname, dirs_exist_ok=True,
                            ignore=shutil.ignore_patterns("*.php", "*.sql", "*.env", "*.ini"))
    for file in OUT.rglob("*.css"):
        css = file.read_text(encoding="utf-8", errors="replace")
        css = re.sub(r'url\((["\']?)/(assets|uploads)/', lambda m:
                     "url(" + m.group(1) + BASE + m.group(2) + "/", css)
        file.write_text(css, encoding="utf-8")
    (OUT / ".nojekyll").write_text("", encoding="utf-8")
    if not (OUT / "index.html").exists():
        sys.exit("Public homepage was not exported")
    # Do not upload a partial catalog when the required static front door fails.
    for required in ("kategoriler", "hazir-scriptler", "blog"):
        if not (OUT / required / "index.html").exists():
            sys.exit("Required demo route missing: " + required)
    home_html = (OUT / "index.html").read_text(encoding="utf-8")
    # Count rendered card elements, not JS selector strings in inline scripts.
    reference_count = len(re.findall(r'<article[^>]+data-ref-card',
                                     home_html, flags=re.S))
    if reference_count != 6:
        sys.exit(f"Missing cover-backed portfolio examples: {reference_count}/6")
    if 'data-ref-group="agency"' not in home_html or 'data-ref-group="marketing"' not in home_html:
        sys.exit("Missing agency or digital filter on static homepage")
    for ref in ["insaat-firmasi-scripti", "haber-sitesi-scripti",
                "netvera-emlak-script-yazilimi-pro", "google-maps-veri-cekme-isletme-bulucu-botu"]:
        if not (OUT / "hazir-scriptler" / ref / "index.html").exists():
            sys.exit("Missing linked portfolio software page: " + ref)
    if len(list((OUT / "hazir-scriptler").glob("*/index.html"))) < 16:
        sys.exit("Incomplete authentic public software catalog (expected 16)")
    print("STATIC_PREVIEW_OK: references=6, verified_catalog=16, pages=", len(list(OUT.rglob("index.html"))),
          "failed_optional=",len(failures),"public_only=true")
    for route,err in failures[:10]:
        print("OPTIONAL_PAGE_OMITTED:",route,err[:130])

if __name__ == "__main__":
    main()
