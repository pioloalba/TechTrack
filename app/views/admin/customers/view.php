<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<div class="card">
  <strong><?= html_escape($customer['name'] ?? 'Customer') ?></strong>
  <div style="margin-top:8px;">Email: <?= html_escape($customer['email'] ?? '') ?></div>
</div>
<div class="card" style="margin-top:12px;">
  <strong>Orders</strong>
  <table class="table" style="margin-top:8px;">
    <thead><tr><th>#</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach(($orders??[]) as $o): ?>
      <tr>
        <td>#<?= html_escape($o['id']??'') ?></td>
        <td><?= html_escape($o['created_at']??'') ?></td>
        <td>₱<?= number_format((float)($o['total']??0),2) ?></td>
        <td><span class="badge amber"><?= html_escape($o['status']??'pending') ?></span></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</div>
</div>
</body>
</html>