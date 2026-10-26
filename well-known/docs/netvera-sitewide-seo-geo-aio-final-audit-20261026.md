# NetVera — Sitewide SEO / GEO / AIO release audit
Date: 2026-10-26 · Scope: storefront, service categories, packages, original NetVera software URLs, blog, public pages, and regional landing pages.

## What was checked
- Shared `SeoMetadataService`, `SeoStructuredDataService`, `SeoHealthService`, `SeoScoreService`, `SitemapService`, and global frontend `layouts/app.php`.
- Sitewide title, description, robots, canonical, Open Graph, Twitter and JSON-LD output.
- Original NetVera URL preservation, category and blog pagination/filter rules, private member/checkout routes, indexable landing pages and sitemap generation.
- Source attribution, author fields, first-party company identity, blog/landing visible Q&A, references and public software demos.
- All ten regional landing theme variants; recent responsive hero, process, footer and product-card work deliberately retained.

## Released code improvements
1. Ten-theme blog card images are constrained to a consistent 16:10 visual viewport with `object-fit: contain`. No unintended overflow, distortion or cropped original images; narrow screens keep two columns and all selected cards.
2. Visual preview / design query URLs (`onizleme`, `tema`, `tasarim`, `preview`) receive `noindex`; the canonical remains the clean public route. Public nonpreview URLs, old slugs and redirects are not rewritten.
3. The shared JSON-LD graph is suppressed whenever the page's effective robots rule is `noindex`. The same centralized indexing rule protects private pages and filtered URLs.
4. UTF-8/Turkish word counting in the existing sitewide SEO readiness score no longer uses ASCII-only `str_word_count`; real H1 detection no longer counts an arbitrary H2.
5. Admin SEO health includes checks for canonical sitemap origin, fragment-free URLs, HTTPS, graph integrity and noindex privacy. No external scanning or mutation of the live database.
6. Permanent GitHub Actions CI for sitewide SEO and all ten landing theme contracts. URL, payment and existing admin SEO models are preserved.

## Confirmed existing modules (not replaced)
- Separate editor-managed fields for service, category, package and article metadata, original blog URLs, landing page SEO, authors, sources, review dates, focus and secondary terms.
- Sitewide JSON-LD with page/entity relationships, organization identity from real site settings, canonical URLs, and content-specific Article / Service / Product schema where the original controller supplies it.
- Sitemap obeys published status, explicit `noindex`, saved canonical preferences for blog, and URL deduplication; date values must be real updates.
- Regional page drafts default to `noindex` until actively published; uniqueness checks reject mechanically duplicated city changes in the content validation workflow.

## Remaining release checks requiring the actual live domain
- Confirm correct `https://netvera.tr` base URL, HTTPS redirects, SSL validity, canonical consistency and `robots.txt` / `sitemap.xml` through public HTTP responses.
- Run Google Rich Results Test on home, one service, one blog, one software detail and an active regional page; verify schema reflects visible, truthful copy. Schema alone is not a rich-result guarantee.
- Search Console: inspect the original indexed NetVera paths and 301 mapping, submit sitemap, monitor 404/redirect chains, duplicate canonical selections, crawling and performance after cutover.
- Check admin SEO health in the production environment and confirm that no unpublished pages, query variants or private pages are indexed.
- Review every active city/service page for genuinely distinct user value, truthful remote/on-site scope and noninvented offices/ratings/clients. Generating only city-swapped copies risks doorway/scaled-content spam.
- Check real Core Web Vitals (LCP/CLS/INP), image bytes and mobile layout from a production browser; static repository tests cannot establish live performance.
- Check Google Business Profile only if the company has a real eligible location. Serving a city remotely does not establish a local branch.
- Keep SEO editorial scores as internal readiness hints, not Google ranking predictions or rigid Google character/word-count requirements.

## Reference design parity
The supplied premium references were reviewed for section order, split visual/body composition, service and product grids, process, blog and FAQ. Data-driven variations and the administrator's own hero images were prioritized. Mock reference graphics that pretend to show Google rankings, customer totals, Google Partner badges or specific city offices were deliberately NOT copied because no verified data source supports them. Pixel-perfect parity across all themes requires screenshot validation against deployed pages; it cannot be honestly inferred from CSS alone.

## Official sources (as of October 2026)
- Google AI Search: https://developers.google.com/search/docs/fundamentals/ai-optimization-guide
- Structured data: https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- Canonicals: https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls
- Sitemap: https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
- Spam policies including doorway pages: https://developers.google.com/search/docs/essentials/spam-policies
- FAQ limits: https://developers.google.com/search/blog/2023/08/howto-faq-changes

**Guarantees:** None for rankings, AI citations, indexing, enhanced display, or speed scores. The changes improve technical compliance, testability, and editorial honesty while keeping existing production data untouched.
