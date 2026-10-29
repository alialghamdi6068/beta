<?php
declare(strict_types=1);

/**
 * Beta — Hosting & Subdomain Diagnostics
 * Safe, lightweight, single-file PHP utility. No database and no external services.
 * Never exposes environment variables, filesystem paths, or server configuration.
 */
$started = microtime(true);

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$test = isset($_GET['test']) && is_string($_GET['test']) ? $_GET['test'] : '';
if ($test === '404') {
    http_response_code(404);
} elseif ($test === '500') {
    // Simulated HTTP 500 response only; does not trigger an actual server failure.
    http_response_code(500);
} else {
    http_response_code(200);
}

$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443')
);
$host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : 'Unknown host';
$method = isset($_SERVER['REQUEST_METHOD']) ? (string)$_SERVER['REQUEST_METHOD'] : 'Unknown';
$phpVersion = PHP_VERSION;
$elapsedMs = round((microtime(true) - $started) * 1000, 2);
$statusCode = http_response_code();
$securityHeaders = [
    'Content-Security-Policy' => "default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; img-src 'self' data:; base-uri 'self'; form-action 'self'; frame-ancestors 'none'",
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
];
foreach ($securityHeaders as $name => $value) {
    header($name . ': ' . $value);
}
header('Cache-Control: no-store, max-age=0');
header('X-Beta-Diagnostic: active');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="dark">
<title>Beta — Hosting Diagnostics</title>
<style>
:root{--bg:#080b13;--panel:#111827;--panel2:#151e30;--line:#263247;--text:#eef3ff;--muted:#9ba9c1;--blue:#8bb4ff;--green:#53e0b1;--yellow:#ffd479;--red:#ff7c91}
*{box-sizing:border-box}body{margin:0;background:radial-gradient(ellipse at 15% 0%,#18294b 0,transparent 42%),var(--bg);color:var(--text);font:15px/1.6 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
.wrap{width:min(1120px,calc(100% - 32px));margin:auto}.nav{display:flex;justify-content:space-between;align-items:center;padding:24px 0;border-bottom:1px solid #ffffff12}.brand{display:flex;gap:12px;align-items:center;font-weight:800;letter-spacing:.3px}.logo{width:38px;height:38px;display:grid;place-items:center;border-radius:12px;background:linear-gradient(135deg,#8bb4ff,#9e8bff);color:#07101e;font-size:20px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 11px;color:var(--muted);font-size:12px}
.hero{padding:62px 0 34px;max-width:760px}.eyebrow{color:var(--green);text-transform:uppercase;font-size:12px;letter-spacing:2px;font-weight:800}.hero h1{font-size:clamp(38px,7vw,66px);line-height:1.04;letter-spacing:-2px;margin:14px 0}.hero p{color:var(--muted);font-size:17px;max-width:650px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:26px 0}.card{background:linear-gradient(145deg,#141e30,#0f1522);border:1px solid var(--line);border-radius:18px;padding:20px;min-width:0}.card h2,.card h3{margin:0 0 8px;font-size:16px}.muted{color:var(--muted)}.value{font-size:24px;font-weight:750;overflow-wrap:anywhere}.dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--green);margin-right:8px}.section{margin:32px 0}.section-title{font-size:22px;letter-spacing:-.5px;margin:0 0 6px}.rows{display:grid;grid-template-columns:1fr 1fr;gap:12px}.rowitem{display:flex;align-items:center;justify-content:space-between;gap:12px;background:#0d1320;border:1px solid var(--line);border-radius:12px;padding:13px 15px}.state{font-size:12px;font-weight:750;border-radius:8px;padding:3px 8px;white-space:nowrap}.pass{color:var(--green);background:#12352f}.warn{color:var(--yellow);background:#392d16}.fail{color:var(--red);background:#3a1c28}.actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:16px}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid #354663;border-radius:11px;padding:10px 14px;background:#17243a;color:var(--text);text-decoration:none;font-weight:700;cursor:pointer}.btn:hover{border-color:var(--blue);background:#1b2b46}.btn.primary{background:var(--blue);color:#081225;border-color:transparent}.note{border:1px solid #66522c;background:#251f15;color:#f3d99d;padding:14px 16px;border-radius:12px}.foot{border-top:1px solid #ffffff12;color:var(--muted);padding:24px 0 36px;margin-top:42px;font-size:13px}.mono{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;overflow-wrap:anywhere}
@media(max-width:760px){.grid{grid-template-columns:1fr}.rows{grid-template-columns:1fr}.hero{padding-top:42px}.nav{padding:18px 0}.hero h1{letter-spacing:-1.4px}}
</style>
</head>
<body>
<div class="wrap">
<header class="nav"><div class="brand"><span class="logo">β</span><span>Beta <span class="muted">/ Diagnostics</span></span></div><span class="pill">HOSTING TEST ENVIRONMENT</span></header>
<main>
<section class="hero">
<div class="eyebrow">Lightweight · No database · No external APIs</div>
<h1>Know your hosting.<br><span style="color:var(--blue)">Check the basics.</span></h1>
<p>A simple diagnostic page for reviewing HTTPS, browser-visible security headers, runtime availability and HTTP error handling on a test subdomain.</p>
<div class="actions"><a class="btn primary" href="#security">Run browser checks <span aria-hidden="true">↘</span></a><a class="btn" href="?test=404">Simulate 404</a><a class="btn" href="?test=500">Simulate 500</a><a class="btn" href="?">Reset test</a></div>
</section>
<?php if ($test === '404' || $test === '500'): ?>
<div class="note">You are viewing a deliberate <strong><?= h((string)$statusCode) ?></strong> response. This changes only the HTTP status returned by this page; it does not break the server. Use “Reset test” to return to status 200.</div>
<?php endif; ?>
<section class="grid" aria-label="Runtime overview">
<div class="card"><h3><span class="dot"></span>HTTPS connection</h3><div class="value"><?= $isHttps ? 'Enabled' : 'Not detected' ?></div><div class="muted"><?= $isHttps ? 'The request reached PHP over HTTPS.' : 'Open the site using its HTTPS URL to verify TLS.' ?></div></div>
<div class="card"><h3><span class="dot"></span>HTTP response</h3><div class="value"><?= h((string)$statusCode) ?></div><div class="muted"><?= $statusCode === 200 ? 'Normal response' : 'Intentional test response' ?></div></div>
<div class="card"><h3><span class="dot"></span>PHP runtime</h3><div class="value"><?= h($phpVersion) ?></div><div class="muted">PHP executed this diagnostic page.</div></div>
<div class="card"><h3>Requested host</h3><div class="value" style="font-size:18px"><?= h($host) ?></div><div class="muted">Host header received by the application.</div></div>
<div class="card"><h3>Request method</h3><div class="value"><?= h($method) ?></div><div class="muted">Method used for this page request.</div></div>
<div class="card"><h3>Page startup</h3><div class="value"><?= h((string)$elapsedMs) ?> ms</div><div class="muted">Approximate PHP startup time; not a full network benchmark.</div></div>
</section>
<section class="section" id="security">
<h2 class="section-title">Security headers</h2><p class="muted">Server response headers set by Beta. These are not a full security audit; hosting proxies may also add or override headers.</p>
<div class="rows">
<?php foreach ($securityHeaders as $name => $value): ?>
<div class="rowitem"><div><strong><?= h($name) ?></strong><div class="muted mono"><?= h($value) ?></div></div><span class="state pass">SET</span></div>
<?php endforeach; ?>
<div class="rowitem"><div><strong>HTTPS</strong><div class="muted">TLS is configured outside this PHP file.</div></div><span class="state <?= $isHttps ? 'pass' : 'warn' ?>"><?= $isHttps ? 'DETECTED' : 'CHECK TLS' ?></span></div>
<div class="rowitem"><div><strong>External requests</strong><div class="muted">This page does not call third-party services.</div></div><span class="state pass">NONE</span></div>
</div>
</section>
<section class="section">
<h2 class="section-title">Browser-side checks</h2><p class="muted">These checks run from your browser and report only what this page can observe.</p>
<div class="rows" id="browserChecks">
<div class="rowitem"><strong>Secure context</strong><span class="state warn">Checking…</span></div>
<div class="rowitem"><strong>JavaScript</strong><span class="state pass">Running</span></div>
<div class="rowitem"><strong>Same-origin fetch</strong><span class="state warn">Checking…</span></div>
<div class="rowitem"><strong>Page visibility</strong><span class="state pass">Available</span></div>
</div>
</section>
<section class="section">
<h2 class="section-title">HTTP error testing</h2><p class="muted">Use these links to confirm that the application returns expected status codes. The 500 option is simulated and does not trigger a PHP crash.</p>
<div class="actions"><a class="btn" href="?test=404">Open 404 test</a><a class="btn" href="?test=500">Open simulated 500</a><a class="btn" href="?">Return to normal</a></div>
</section>
<section class="section"><div class="note"><strong>Important:</strong> Beta is a basic diagnostic page, not a penetration-testing suite. It does not test DDoS resilience, attempt authentication bypasses, scan other hosts, or generate heavy traffic. Test only systems you own or have explicit permission to assess.</div></section>
</main>
<footer class="foot">Beta Diagnostics · Lightweight test page · No database · No analytics · No external API calls</footer>
</div>
<script>
(() => {
 const target = document.getElementById('browserChecks');
 if (!target) return;
 const item = (name, detail, status, kind='pass') => {
   const row = document.createElement('div'); row.className = 'rowitem';
   const left = document.createElement('div');
   const title = document.createElement('strong'); title.textContent = name;
   const sub = document.createElement('div'); sub.className = 'muted'; sub.textContent = detail;
   left.append(title, sub);
   const badge = document.createElement('span'); badge.className = 'state ' + kind; badge.textContent = status;
   row.append(left, badge); return row;
 };
 const secure = window.isSecureContext && location.protocol === 'https:';
 const checks = [
   item('Secure context', secure ? 'Browser reports HTTPS secure context.' : 'HTTPS was not confirmed for this page.', secure ? 'PASS' : 'CHECK TLS', secure ? 'pass' : 'warn'),
   item('JavaScript', 'The browser executed the diagnostic script.', 'RUNNING', 'pass'),
   item('Same-origin fetch', 'Checking whether this page can fetch itself.', 'CHECKING', 'warn'),
   item('Page visibility', document.visibilityState, 'AVAILABLE', 'pass')
 ];
 target.replaceChildren(...checks);
 fetch(location.pathname + location.search, {method:'GET', cache:'no-store', credentials:'same-origin'})
   .then(response => {
      const row = item('Same-origin fetch', 'Fetch completed with HTTP ' + response.status + '.', response.ok ? 'PASS' : 'HTTP ' + response.status, response.ok ? 'pass' : 'warn');
      const current = target.children[2]; if (current) current.replaceWith(row);
   })
   .catch(() => {
      const current = target.children[2];
      if (current) current.replaceWith(item('Same-origin fetch', 'The request failed or was blocked. Review browser console and hosting rules.', 'FAILED', 'warn'));
   });
})();
</script>
</body>
</html>
