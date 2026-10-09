# NetVera Cep — Admin Support PWA (October 2026)

## Delivered

- **Mobile URL**: \`https://yorumhizmeti.tr/admin/cep\` (requires a valid active admin session).
- **Admin link / QR**: Admin → NetVera Sohbet ve Teklifler. QR encodes the public app URL ONLY, never passwords, access tokens, or session state.
- **Install**: On Android, open the HTTPS URL in Chrome → Install app / Add to Home Screen. On iPhone, Safari → Share → Add to Home Screen. Both use the responsive PWA with installed app display.
- **Agent profile**: Name, professional title, profile photo. Edit via Admin → NetVera Sohbet ve Teklifler.
- **Sounds**: Chime, Soft, Digital, Off. New chat and incoming support messages can have separate presets. To comply with browser autoplay limits, a real tap on the bell icon is necessary to unlock sound / request foreground notifications.
- **Workflows**: Chat inbox and reply; support tickets and reply; recent orders with payment + fulfillment status and purchased items, read-only. Payment flags remain unchanged; sensitive order changes use the full admin panel.
- **Polling**: Up to 4–5 seconds delay while app/browser is open and visible. **No guaranteed iOS/Android push in background or when closed**: implementing actual background push needs separately provisioned VAPID keys, subscription persistence, provider retries, app permissions and a cPanel cron. Do not advertise background alerts until integrated and tested.
- **Security**: No private API credential in public JS. Every mobile API route is under the existing Admin route middleware AND validates active user through AdminAuth::admin(); all POST replies require CSRF. Every API response uses no-store. Service worker only caches explicit static assets, never customer tickets/chats/orders or authenticated HTML.

## Release smoke test
1. Deploy PHP, CSS, JS, manifest and static service worker to the actual document root; keep your existing live \`.env\` and session config.
2. HTTPS is mandatory for service worker installation and mobile notifications.
3. Verify merchant-facing site displays 3 testimonial cards; only the first colored card rotates. Add a fourth real review in Admin → Müşteri Yorumları with photo, category, service, and stars.
4. Verify Payment Modules shows bank transfer, PayTR and iyzico as one flat list; test mode/toggle/default/actions still work.
5. Configure staff profile + sounds; verify visitor chat header changes and agent/admin responses are delivered.
6. Open admin browser and PWA, tap the bell to activate sound; send a customer chat message and support ticket and verify in-app alerts within ~5 seconds. Test when app is hidden too: **background alerts are not claimed**.
7. Open /admin/cep in a fresh logged-out browser or scan QR on a phone not yet signed in: admin login is required. POST without CSRF is denied.
8. Install on iPhone Safari and Android Chrome. Device OS/PWA support can vary; run hands-on testing on both devices before selling as a production mobile app.
9. Confirm Payment callback, account auth, original URLs, SEO and order data remain untouched. No DB migration is required for these PWA UI changes.

## Authenticity
Customer testimonials must be genuine and published with permission. Do not fabricate stars, buyer roles, or purchases; the category/package selections are editorial metadata, not a cryptographic proof of completed purchase. For verified-buyer badges, an independently validated purchase match would be a separate future enhancement.
