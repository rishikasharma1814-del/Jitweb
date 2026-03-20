# Complete Technical SEO Audit — JIT Website (Pure HTML)

**Date:** February 18, 2025  
**Scope:** Home, blog listing, blog detail pages, meta tags, technical SEO, indexing, content

---

## 1. LIST OF ALL SEO ISSUES FOUND

### Critical (blocking indexing/ranking)

| # | Issue | Location | Impact |
|---|--------|----------|--------|
| 1 | **No robots.txt** | Root | Crawlers get no guidance; sitemap not declared |
| 2 | **No sitemap.xml** | Root | Google may not discover all blog URLs |
| 3 | **No canonical tags** | All pages | Risk of duplicate signals if URLs are accessible multiple ways |
| 4 | **Blog listing wrong title** | blog.html | Title is "Blog Details" (duplicate of blog-details.html); not descriptive for /blog |
| 5 | **Duplicate page titles** | blog.html, blog-details.html | Both use "Blog Details - Jewar International Technologies" |
| 6 | **Generic/duplicate meta description** | index, blog, blog-details, many pages | "Jewar International Technologies Pvt Ltd" repeated; not page-specific |
| 7 | **Broken internal links** | blog.html | Links to blog-16.html and blog-28.html (files do not exist) |
| 8 | **No structured data (schema)** | All pages | No Article/BlogPosting or Organization; no rich results |
| 9 | **No Open Graph / Twitter Card meta** | All pages | Poor social sharing and possible lower relevance signals |

### High (hurting rankings)

| # | Issue | Location | Impact |
|---|--------|----------|--------|
| 10 | **Blog detail pages: no H1** | Blog posts (e.g. why-your-business-needs-a-mobile-app-in-2025.html) | Main title in `<h3 class="blog__detail-title">`; should be single H1 per page |
| 11 | **Multiple H1s on homepage** | index.html | "Your Vision, Our Code" and "Part of BNI" / "2023" as H1; only one H1 recommended |
| 12 | **Empty image alt attributes** | blog.html, index (blog section) | Many `<img alt="">`; bad for accessibility and image SEO |
| 13 | **Blog detail pages: no visible related posts** | Blog posts | Related section commented out; weak internal linking from posts |
| 14 | **Favicon sizes typo** | Multiple | `sizes="16*16"` should be `sizes="16x16"` |
| 15 | **Duplicate / thin titles** | project1–9, service-details, portfolio-4 typo | "Portfolio Details" x9; "Service Details" x5; portfolio-4: "AxtrJewar...a" |

### Medium (optimization)

| # | Issue | Location | Impact |
|---|--------|----------|--------|
| 16 | **No meta keywords** | All | Optional; low impact but some templates use for consistency |
| 17 | **Render-blocking CSS** | All pages | Google Fonts + 7 CSS files in `<head>` without defer/async; can delay FCP |
| 18 | **Render-blocking JS** | Bottom but heavy | Many scripts (jQuery, GSAP, Swiper, etc.); consider defer for non-critical |
| 19 | **Blog URLs** | Blog files | URLs are SEO-friendly (kebab-case, descriptive); keep pattern |
| 20 | **Similar titles/content** | protect-company-data vs protect-company-data-from-hackers | Two similar articles; need clear differentiation or canonical to one |
| 21 | **Footer typo** | blog.html | "Alrights" → "All rights" |
| 22 | **Blog meta author/date** | Some posts | Inconsistent (e.g. "Writen by" typo, date in same line as author) |

### Low / content

| # | Issue | Location | Impact |
|---|--------|----------|--------|
| 23 | **AI-style content** | Flutter post (keyword stuffing in first paragraph) | First paragraph is comma-separated keywords; looks automated |
| 24 | **Thin or templated intros** | Several blog posts | Generic "In 2025…" openings; can feel repetitive across site |
| 25 | **No breadcrumb markup** | All | Breadcrumbs could use schema for rich results |

---

## 2. WHAT IS MISSING ON BLOG PAGES

- **Unique `<title>`** per post (some are good; blog listing is wrong).
- **Unique meta description** (155–160 chars) with primary keyword and CTA.
- **Single H1** matching the article title (currently main title is in H3).
- **Canonical URL** (absolute) to the same blog post URL.
- **Open Graph:** og:title, og:description, og:image, og:url, og:type=article.
- **Twitter Card:** twitter:card, twitter:title, twitter:description, twitter:image.
- **Article schema** (BlogPosting): headline, image, datePublished, dateModified, author.
- **Related posts** section with 3–4 internal links (currently commented out).
- **Internal links** in body to other blog posts and service/landing pages.
- **Image alt text** for every image (descriptive, not empty).
- **Breadcrumb** (e.g. Home > Blog > Article title) + BreadcrumbList schema optional.

---

## 3. WHY BLOGS ARE NOT RANKING

1. **Discovery:** No sitemap and no robots.txt pointing to it → crawlers may not find all posts.
2. **Weak on-page signals:** Duplicate/generic titles and meta descriptions (blog listing = "Blog Details") → low relevance and CTR in SERPs.
3. **No rich results:** Missing Article schema → no rich snippets and less prominence.
4. **Poor social/preview signals:** No OG/Twitter → shares look generic; possible indirect quality signal.
5. **Duplicate title risk:** blog.html and blog-details.html share the same title → confusion and dilution.
6. **Broken links:** blog-16.html and blog-28.html 404 → bad UX and crawl waste.
7. **Heading hierarchy:** Main article title not in H1 → weaker topical signal.
8. **Thin internal linking:** Blog listing links to posts, but posts don’t link back to each other or to key service pages.
9. **Content quality:** Some posts have keyword-stuffed or generic intros (AI-like) → E-E-A and uniqueness at risk.
10. **Indexing:** Without sitemap and with duplicate titles, Google may under-index or under-prioritize blog URLs.

---

## 4. EXACT CODE FIXES & IMPROVED HTML EXAMPLES

### 4.1 Homepage: meta description (index.html)

**Current:**
```html
<meta name="description" content="Jewar International Technologies Pvt Ltd">
```

**Replace with:**
```html
<meta name="description" content="Jewar International Technologies – Mobile app development, web development & UI/UX in Noida. Custom software, Flutter & cross-platform apps. 70+ projects, 5+ years. Get a quote.">
```

### 4.2 Blog listing page (blog.html) – title and meta

**Current:**
```html
<meta name="description" content="Jewar International Technologies Pvt Ltd" />
<title>Blog Details - Jewar International Technologies</title>
```

**Replace with:**
```html
<meta name="description" content="Blog & insights on mobile app development, Flutter, AI, cybersecurity, and digital strategy. Tips for startups and businesses by Jewar International Technologies." />
<title>Blog | Mobile App & Tech Insights | Jewar International Technologies</title>
```

### 4.3 Blog detail template – head section (per-post)

Add after `<meta name="viewport" ...>` (use your real domain and image URL):

```html
<!-- Canonical -->
<link rel="canonical" href="https://jewarinternational.com/why-your-business-needs-a-mobile-app-in-2025.html" />

<!-- Open Graph -->
<meta property="og:type" content="article" />
<meta property="og:url" content="https://jewarinternational.com/why-your-business-needs-a-mobile-app-in-2025.html" />
<meta property="og:title" content="Why Your Business Needs a Mobile App in 2025 | Benefits, Growth & Digital Strategy" />
<meta property="og:description" content="Discover why your business needs a mobile app in 2025. Learn how apps boost growth, customer engagement, branding, and sales. Complete guide with benefits, strategy, and trends." />
<meta property="og:image" content="https://jewarinternational.com/assets/imgs/blog/why-your-business-needs-a-mobile-app-in-2025-jewar-technologies.png" />
<meta property="og:site_name" content="Jewar International Technologies" />
<meta property="og:locale" content="en_US" />
<meta property="article:published_time" content="2025-07-07" />
<meta property="article:author" content="Jewar International Technologies" />

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="Why Your Business Needs a Mobile App in 2025 | Benefits, Growth & Digital Strategy" />
<meta name="twitter:description" content="Discover why your business needs a mobile app in 2025. Learn how apps boost growth, customer engagement, branding, and sales." />
<meta name="twitter:image" content="https://jewarinternational.com/assets/imgs/blog/why-your-business-needs-a-mobile-app-in-2025-jewar-technologies.png" />
```

### 4.4 Blog detail – H1 fix

**Current (e.g. why-your-business-needs-a-mobile-app-in-2025.html):**
```html
<h3 class="blog__detail-title animation__word_come">
  Why Your Business Needs a Mobile App in 2025
</h3>
```

**Replace with (single H1 per page):**
```html
<h1 class="blog__detail-title animation__word_come">
  Why Your Business Needs a Mobile App in 2025
</h1>
```

Then use `<h2>` for main sections and `<h3>` for subsections (no other H1 on the page).

### 4.5 Homepage – single H1

Keep the hero as the only H1:

**Current:** Two H1s: "Your Vision, Our Code" and "Part of BNI" / "2023".

**Fix:** Change the BNI block to H2:

```html
<h2 class="mb-3 fw-bold text-white" style="font-size: 5rem;">Part of BNI</h2>
<h3 class="fw-light mb-2 text-white" style="font-size: 2rem;">Since</h3>
<h2 class="fw-bold text-white" style="font-size: 4rem;">2023</h2>
```

### 4.6 Image alt (blog listing)

**Current:** `<img ... alt="">`

**Replace with descriptive alt per card, e.g.:**
```html
<img class="image-box__item" src="assets/imgs/blog/protect-company-data-from-hackers-business-security-guide.png" alt="Protect company data from hackers - business security guide illustration">
```

### 4.7 Broken links on blog listing (blog.html)

- **blog-16.html** → Replace with the correct post. From your list, "Designing Apps That Respect Cultural and Human Rights Values" does not have a matching HTML file; either create that post or link to an existing one, e.g. `designing-apps-that-respect-cultural-and-human-rights-values.html` if you add it, or remove the card until the page exists.
- **blog-28.html** → Same: "The Road Ahead – What to Expect in Mobile App Technology" has no file. Replace link with an existing post (e.g. `future-of-mobile-apps-ai-powered-innovation.html`) or remove the card.

Until you have real URLs, remove the two cards that link to blog-16.html and blog-28.html to avoid 404s.

### 4.8 Favicon

**Current:** `sizes="16*16"`  
**Replace with:** `sizes="16x16"`

---

## 5. STRUCTURED DATA EXAMPLE FOR BLOG (ARTICLE SCHEMA)

Add in `<head>` or before `</body>` (one script per blog post; adjust URL, dates, image, name):

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "Why Your Business Needs a Mobile App in 2025",
  "description": "Discover why your business needs a mobile app in 2025. Learn how apps boost growth, customer engagement, branding, and sales. Complete guide with benefits, strategy, and trends.",
  "image": "https://jewarinternational.com/assets/imgs/blog/why-your-business-needs-a-mobile-app-in-2025-jewar-technologies.png",
  "url": "https://jewarinternational.com/why-your-business-needs-a-mobile-app-in-2025.html",
  "datePublished": "2025-07-07",
  "dateModified": "2025-07-07",
  "author": {
    "@type": "Organization",
    "name": "Jewar International Technologies",
    "url": "https://jewarinternational.com"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Jewar International Technologies",
    "logo": {
      "@type": "ImageObject",
      "url": "https://jewarinternational.com/assets/imgs/logo/LogoNew1.png"
    }
  },
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://jewarinternational.com/why-your-business-needs-a-mobile-app-in-2025.html"
  }
}
</script>
```

Validate at: https://validator.schema.org/ and https://search.google.com/test/rich-results

---

## 6. PROPER BLOG PAGE SEO TEMPLATE (HEAD + KEY BODY PARTS)

Use this as the template for every new blog post (replace placeholders).

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ARTICLE_TITLE (Primary Keyword) | Brand Modifier | Jewar International Technologies</title>
  <meta name="description" content="155-160 char description with main keyword and benefit. No quote." />

  <link rel="canonical" href="https://jewarinternational.com/BLOG-SLUG.html" />

  <!-- Open Graph -->
  <meta property="og:type" content="article" />
  <meta property="og:url" content="https://jewarinternational.com/BLOG-SLUG.html" />
  <meta property="og:title" content="ARTICLE_TITLE" />
  <meta property="og:description" content="SAME_AS_META_DESCRIPTION" />
  <meta property="og:image" content="https://jewarinternational.com/IMAGE_PATH" />
  <meta property="og:site_name" content="Jewar International Technologies" />
  <meta property="article:published_time" content="YYYY-MM-DD" />
  <meta property="article:author" content="Jewar International Technologies" />

  <!-- Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="ARTICLE_TITLE" />
  <meta name="twitter:description" content="SHORT_DESCRIPTION" />
  <meta name="twitter:image" content="https://jewarinternational.com/IMAGE_PATH" />

  <!-- Article schema (see section 5) -->
  <script type="application/ld+json">...</script>

  <!-- rest of head: favicon, fonts, CSS -->
</head>
<body>
  <!-- ... header ... -->
  <main>
    <article>
      <header>
        <h1>ARTICLE_TITLE (only H1 on page)</h1>
        <p>By Jewar International Technologies · DD Month YYYY</p>
      </header>
      <img src="..." alt="Descriptive alt for featured image" />
      <div class="blog__detail-content">
        <!-- Use H2 for main sections, H3 for subsections -->
        <h2>First Section</h2>
        <p>...</p>
        <h2>Second Section</h2>
        <p>... internal links to other posts and /service, /contact ...</p>
      </div>
      <section aria-label="Related articles">
        <h2>Related Articles</h2>
        <!-- 3–4 links to other blog posts -->
      </section>
    </article>
  </main>
  <!-- ... footer ... -->
</body>
</html>
```

---

## 7. STEPS TO IMPROVE TRAFFIC IN 30 DAYS

### Week 1 (Technical & discovery)

1. Add **robots.txt** and **sitemap.xml** at root; submit sitemap in Google Search Console (GSC).
2. Fix **blog.html** title and meta description; fix **blog-details.html** title (e.g. "Blog | Jewar International Technologies" vs "Sample Post Title | Blog | Jewar..." for the template).
3. Remove or fix **broken links** (blog-16, blog-28) on blog listing.
4. Add **canonical** + **OG + Twitter** meta to 3–5 priority blog posts; add **Article schema** to the same posts.
5. Fix **H1** on 3–5 key posts (main title = H1); fix **homepage** to single H1.
6. Add **descriptive alt** to all blog images on blog listing and on 3–5 posts.

### Week 2 (Content & internal linking)

7. Uncomment and implement **Related posts** on 5–10 blog posts; link to 3–4 real posts per page.
8. Add **2–3 internal links** in each of 5 posts (to other posts and to service/contact).
9. Differentiate **protect-company-data** vs **protect-company-data-from-hackers**: different angles or add canonical to the stronger URL.
10. Fix **duplicate titles**: project1–9 and service-details pages — make titles unique (e.g. "Project Name | Portfolio | Jewar...").

### Week 3 (Quality & indexing)

11. **Humanize** 2–3 posts: remove keyword stuffing (e.g. Flutter first paragraph), vary intros, add short examples or specifics.
12. Add **blog** link in footer on all pages (already present on some; ensure consistent).
13. In GSC: **URL Inspection** for top 10 blog URLs → "Request indexing" after fixes.
14. **Page speed**: Preconnect to Google Fonts is already there; consider loading non-critical CSS with media="print" onload or defer; defer non-critical JS.

### Week 4 (Monitor & iterate)

15. In GSC, check **Coverage** and **Sitemaps** for errors; fix any 404s or canonical issues.
16. Check **Performance** (queries, CTR, impressions) for blog URLs; refine titles/descriptions for low CTR.
17. Add **5–10 more** blog posts with the new SEO template (canonical, OG, schema, H1, alt, related posts).
18. Build a few **external links** (guest post, directory, PR) to the blog or homepage.

---

## 8. PAGE SPEED (RENDER-BLOCKING) – SUMMARY

- **CSS:** Multiple stylesheets in `<head>` block rendering. Options: combine/critical CSS above the fold, or load non-critical with `<link rel="preload" as="style" onload="this.rel='stylesheet'">`.
- **Fonts:** You use preconnect; consider `font-display: swap` in the Google Fonts URL (`&display=swap`).
- **JS:** Scripts at bottom are good; ensure they use `defer` or `async` where possible (e.g. analytics already async).
- **Images:** Use responsive images (`srcset`/`sizes`) and modern formats (WebP) where possible; lazy-load below-the-fold images.

---

## 9. MOBILE RESPONSIVENESS

- Viewport meta is correct: `width=device-width, initial-scale=1.0`.
- Layout uses Bootstrap grid (col-xxl, col-xl, etc.) and should be responsive.
- Test with Chrome DevTools device toolbar and Google’s Mobile-Friendly Test; fix any overflow or tap-target issues if reported.

---

## 10. KEYWORD & CONTENT OPTIMIZATION

- **Titles:** Include primary keyword near the front; keep under ~60 characters.
- **Meta description:** Include primary keyword and a clear benefit/CTA; 155–160 chars.
- **H1:** One per page; match or closely reflect the title.
- **H2/H3:** Use long-tail or question keywords where natural.
- **Body:** Use primary keyword in first 100 words; avoid stuffing; add LSI terms (e.g. "mobile app development", "Flutter", "MVP") naturally.
- **Humanization:** Replace generic "In 2025…" intros with a specific stat, question, or story; remove comma-separated keyword lines; vary sentence length and add short examples or quotes.

---

**Next files to add/update in repo:**  
`robots.txt`, `sitemap.xml`, blog listing title/meta and broken-link fixes, one blog post with full SEO head + H1 + schema as reference.

---

## 11. BLOG CONTENT SEO – FIXES APPLIED

The following content-level fixes were applied across blog posts for better SEO and readability:

| Fix | Applied to |
|-----|------------|
| **Main title as H1** | All blog posts: `blog__detail-title` changed from `<h3>` to `<h1>` so each article has a single, clear H1 for the main topic. |
| **Duplicate H1 removed** | `leveraging-ai-to-build-smarter-faster-scalable-apps.html`: extra `<h1>` inside body removed; page now has one H1 in the header. |
| **Keyword stuffing removed** | `flutter-go-to-framework-for-startups-2025.html`: first-paragraph comma-separated keyword line removed; intro starts with the real "Introduction" H2 and copy. |
| **"Writen" typo** | All blog posts: "Writen by" → "Written by". |
| **Author/date formatting** | All blog posts: "Jewar international Technologies" → "Jewar International Technologies"; added " · " between author and date for clarity. |
| **Featured image alt** | Flutter post: generic "Blog Thumbnail" → descriptive alt describing the article topic. |
| **Internal links** | Flutter post conclusion: added links to cross-platform and cost guide. "Why your business needs a mobile app" conclusion: added links to hire guide and cost guide. |

**Content SEO checklist for future posts:**  
Use one H1 (article title), H2 for main sections, H3 for subsections; no keyword-stuffed lines; natural internal links in body/conclusion; "Written by" + correct author + " · " + date; descriptive alt on featured image.

---

## 12. FULL AUDIT FIXES APPLIED (SETUP PER SEO_AUDIT_REPORT)

The following were implemented across the site so the setup matches the audit:

| Audit item | Fix applied |
|------------|-------------|
| **robots.txt** | Already present; points to sitemap. |
| **sitemap.xml** | Already present; includes all blogs + main pages. |
| **Canonical tags** | Added to: index, about, service, contact, blog, blog-details, category, portfolio-2, career, faq, 404; protect-company-data → canonical to protect-company-data-from-hackers-non-technical-guide; why-your-business, flutter, cost guide, how-to-hire (and template). Add to remaining blog posts using `blog-seo-template-head.html`. |
| **Blog listing title/meta** | Already fixed: unique title and meta for blog.html. |
| **Duplicate titles** | project1–9: unique titles (LeoRon World, PinQin, Aata Dady, F&G, Anekk, Crawfish, Modern Mart, Capital Curve, Council Konnect \| Portfolio \| JIT). service-details, service-1 to 4-details: unique (Overview, Mobile App, Website, UI/UX, Resource Outsourcing). portfolio-4: typo "AxtrJewar...a" → "Portfolio - Jewar International Technologies". |
| **Meta descriptions** | index, about, contact, service: page-specific descriptions. |
| **Open Graph & Twitter** | Added to index, about, contact, service, blog (full OG + Twitter Card). |
| **Organization schema** | Added to index.html (JSON-LD with name, url, logo, address, contactPoint). |
| **Favicon sizes** | Replaced `sizes="16*16"` with `sizes="16x16"` across all HTML files. |
| **Empty image alts (blog listing)** | All blog card images on blog.html given descriptive alt text. |
| **Footer blog link** | Blog link uncommented and added to index footer (Information section). |
| **protect-company-data duplicate** | Canonical on protect-company-data.html points to protect-company-data-from-hackers-non-technical-guide.html. |
| **Broken links blog-16, blog-28** | Already removed from blog listing. |
| **H1 / heading structure** | Already fixed: single H1 per blog post; homepage BNI block uses H2/H3. |
| **Font display** | Google Fonts URL on index (and site) already uses `&display=swap`. |

**Still recommended (manual / ongoing):**  
- Add canonical + OG + Article schema to remaining blog posts (use blog-seo-template-head.html).  
- Uncomment Related posts block on key blog posts and link to 3–4 real posts.  
- In GSC: submit sitemap, request indexing for priority URLs.

---

## 13. HOW LONG IT TAKES FOR BLOGS TO RANK ON GOOGLE

Realistic timelines and what to expect after implementing this audit.

### Typical timelines (from publish or from fixes)

| Phase | Timeframe | What usually happens |
|-------|-----------|------------------------|
| **Indexing** | 3–14 days | New or updated URLs get crawled and appear in the index (faster if sitemap is submitted and “Request indexing” is used in GSC). |
| **Initial rankings** | 2–8 weeks | Pages may start appearing in search for long-tail or low-competition queries. Often positions 20–100+ at first. |
| **Noticeable traffic** | 2–4 months | With consistent content, internal links, and no critical SEO issues, some posts can reach positions 10–30 and get modest traffic. |
| **Competitive rankings** | 4–12+ months | Moving to page 1 (positions 1–10) for competitive keywords usually takes 6–12+ months, depending on domain authority, backlinks, and content depth. |

### Why blogs often take months to rank

1. **New or thin domain authority** – Google tends to trust established sites more; new or low-authority sites need time and signals (links, relevance, UX).  
2. **Crawl and index delay** – Without a sitemap and clear internal links, discovery is slower; after adding them (as in this audit), indexing typically improves within 2–4 weeks.  
3. **Competition** – Terms like “mobile app development company” or “cost of mobile app 2025” are competitive; ranking takes longer than for long-tail phrases (e.g. “Flutter MVP development cost for startups”).  
4. **Content and E-E-A-T** – Unique, helpful content and clear expertise signals help over time; generic or thin content ranks poorly or not at all.  
5. **Backlinks** – Few or no quality backlinks to the blog or site slow ranking; building links (guest posts, directories, PR) usually shows effect in 2–6+ months.

### What to expect after this audit (rough guide)

- **Weeks 1–2:** Sitemap and robots in place → faster discovery; GSC may show more indexed blog URLs.  
- **Weeks 2–6:** Fixed titles, meta, canonicals, and structure → better crawling and relevance; some long-tail impressions/clicks may start.  
- **Months 2–4:** If you keep publishing and linking internally, expect gradual improvement in impressions and a few rankings (often long-tail first).  
- **Months 6–12:** With more content, internal linking, and some backlinks, stronger keywords can move toward page 1–2; timeline varies by niche and competition.

### Practical takeaways

- **There is no fixed “X days to rank”** – It depends on keyword difficulty, domain strength, content quality, and links.  
- **Indexing:** Often **1–2 weeks** after sitemap + fixes.  
- **First meaningful rankings (long-tail):** Often **1–3 months**.  
- **Page-1 potential for harder keywords:** Often **6–12+ months**, with consistent effort.  
- Use **Google Search Console** (Performance + URL Inspection) to see when pages are indexed and when impressions/clicks begin; that is the best measure of “how long it took” for your site.
