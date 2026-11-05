<?php
require __DIR__ . '/lib/bootstrap.php';

$ref     = trim((string)($_GET['ref'] ?? ''));
$index   = find_booking_index($_SESSION['bookings'], $ref);
$booking = $index === null ? null : $_SESSION['bookings'][$index];

if ($booking === null) {
    http_response_code(404);
} else {
    $workshop = $workshops[$booking['workshop_id'] ?? ''] ?? null;
    // đơn giá lấy từ catalog server; fallback chỉ để không lỗi với booking cũ
    $unitFee = $workshop['fee'] ?? intdiv((int)$booking['amount'], max(1, (int)$booking['quantity']));
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Booking details</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e($theme) ?>">
<main class="wrap">
<header>
  <h1>Booking Details</h1>
  <nav><a href="index.php">Catalog</a><a href="dashboard.php">My bookings</a></nav>
</header>

<?php if ($booking === null): ?>
  <div class="panel">
    <p><strong>Booking not found.</strong> The reference is missing, unknown or the session has expired.</p>
    <a class="btn btn-light" href="dashboard.php">Back to My Bookings</a>
  </div>
<?php else: ?>
  <div class="panel">
    <table class="detail">
      <tr><th>Booking Reference</th><td><span class="tag"><?= e($booking['ref']) ?></span></td></tr>
      <tr><th>Attendee</th><td><?= e($booking['full_name']) ?></td></tr>
      <tr><th>Email</th><td><?= e($booking['email']) ?></td></tr>
      <tr><th>Workshop</th><td><?= e($booking['workshop']) ?></td></tr>
      <tr><th>Quantity</th><td><?= e($booking['quantity']) ?> tickets</td></tr>
      <tr><th>Unit Fee</th><td><?= number_format($unitFee) ?> VND</td></tr>
      <tr><th>Total Amount</th><td><strong><?= number_format($booking['amount']) ?> VND</strong></td></tr>
    </table>
    <div class="actions">
      <a class="btn btn-edit" href="booking_edit.php?ref=<?= urlencode($booking['ref']) ?>">Edit this booking</a>
      <a class="btn btn-light" href="dashboard.php">Back to My Bookings</a>
    </div>
  </div>
  <p class="muted">Booking information is read from the current PHP session.</p>
<?php endif; ?>
</main>
</body>
</html>
