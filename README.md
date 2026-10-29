# Beta — Hosting Diagnostics

A small, single-file PHP site for checking basic hosting and subdomain behavior.

## Requirements
- PHP 7.4+ (PHP 8.x recommended)
- Apache hosting with PHP support; `.htaccess` rules are Apache-specific.
- No database, Composer, Node.js, environment variables, or external APIs.

## Deploy
1. Upload `index.php` to the subdomain's document root.
2. Upload `.htaccess` if the host uses Apache. If the host uses Nginx or another server, these rules will not apply; configure equivalent rules in that server instead.
3. Enable HTTPS / issue a TLS certificate from the hosting control panel.
4. Visit the subdomain over HTTPS.

## Available checks
- HTTPS detection from the PHP request and browser secure-context state.
- Basic response headers.
- PHP runtime version, request method, response code, host header, and approximate PHP startup time.
- Same-origin browser fetch.
- Simulated 404 and 500 response codes:
  - `/?test=404`
  - `/?test=500`
  - `/` resets to the normal response.

The 500 test only sets the response status; it does not crash PHP or simulate a real infrastructure failure.

## Security notes
- This is a basic diagnostic page, not a penetration test or load test.
- Do not expose secrets or run tests against systems without authorization.
- The page intentionally does not show environment variables, filesystem paths, server configuration, or internal network details.
- The page adds common security headers; review them against the actual hosting stack and site requirements before using this on a production website.
- If the host is behind a reverse proxy, HTTPS detection may depend on the proxy/PHP configuration.

## Files
- `index.php`: complete site.
- `.htaccess`: optional Apache hardening and hidden-file rules.
