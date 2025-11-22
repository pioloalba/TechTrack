<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>403 | Access Denied</title>
  <style>
	:root {
	  --bg: #f6f8fb;
	  --card: #ffffff;
	  --text: #0f172a;
	  --muted: #475569;
	  --primary: #ef4444;
	  --border: #e5e7eb;
	  --shadow: 0 10px 25px rgba(2, 6, 23, .06);
	}
	* { box-sizing: border-box; }
	html, body { height: 100%; margin: 0; }
	body {
	  font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Apple Color Emoji", "Segoe UI Emoji";
	  background: var(--bg);
	  color: var(--text);
	  display: grid;
	  place-items: center;
	  padding: 2rem;
	}
	.card {
	  width: 100%;
	  max-width: 720px;
	  background: var(--card);
	  border: 1px solid var(--border);
	  border-radius: 1.25rem;
	  box-shadow: var(--shadow);
	  padding: 2rem;
	}
	.code {
	  display: inline-flex;
	  align-items: center;
	  gap: .75rem;
	  font-weight: 700;
	  letter-spacing: .08em;
	  color: var(--primary);
	  background: rgba(239,68,68,.08);
	  border: 1px solid rgba(239,68,68,.15);
	  border-radius: 999px;
	  padding: .4rem .9rem;
	  margin-bottom: 1rem;
	  font-size: .85rem;
	  text-transform: uppercase;
	}
	h1 {
	  margin: 0 0 .4rem 0;
	  font-size: clamp(1.6rem, 2.4vw + 1rem, 2.4rem);
	  line-height: 1.2;
	}
	p { margin: 0; color: var(--muted); font-size: 1.05rem; }
	.actions { margin-top: 1.5rem; display: flex; gap: .75rem; flex-wrap: wrap; }
	.btn {
	  display: inline-flex; align-items: center; gap: .5rem;
	  padding: .75rem 1rem; border-radius: .8rem; border: 1px solid var(--border);
	  background: #fff; color: var(--text); text-decoration: none; font-weight: 600;
	}
	.btn:hover { background: #f3f4f6; }
	.btn.primary { background: #ef4444; color: white; border-color: transparent; box-shadow: 0 8px 18px rgba(239,68,68,.25); }
	.btn.primary:hover { background: #dc2626; }
	.hint { margin-top: 1.25rem; font-size: .9rem; color: var(--muted); border-top: 1px dashed var(--border); padding-top: 1rem; }
  </style>
</head>
<body>
  <main class="card" role="main" aria-labelledby="title">
	<div class="code" aria-hidden="true">403 • Access Denied</div>
	<h1 id="title"><?= isset($heading) ? html_escape($heading) : 'Access Denied' ?></h1>
	<p><?= isset($message) ? html_escape($message) : "You don't have permission to access this page." ?></p>
	<div class="actions">
		<a class="btn primary" href="<?= site_url('admin/pos') ?>">Go to POS</a>
		<a class="btn" href="<?= site_url('admin/orders') ?>">Go to Orders</a>
		<a class="btn" href="javascript:history.back()">Go Back</a>
	</div>
	<div class="hint">If you think this is a mistake, please contact an administrator.</div>
  </main>
</body>
</html>
