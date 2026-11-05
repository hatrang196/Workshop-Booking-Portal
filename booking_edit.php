<?php
require __DIR__ . '/lib/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET', 'POST'], true)) {
    http_response_code(405);
    exit('Method not allowed');
}

// ---- 1. Request gate: POST phải có CSRF hợp lệ trước mọi xử lý khác ----
if ($method === 'POST') {
    if (!csrf_valid($_POST)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
    $ref = trim((string)($_POST['ref'] ?? ''));
} else {
    $ref = trim((string)($_GET['ref'] ?? ''));
}

// ---- 2. Tìm booking theo ref (không dùng index do client gửi) ----
$index   = find_booking_index($_SESSION['bookings'], $ref);
$booking = $index === null ? null : $_SESSION['bookings'][$index];
if ($booking === null) {
    http_response_code(404);                 // E08: ref lạ -> không tạo/sửa gì
}

// ---- 3. POST: validate -> cập nhật đúng 1 phần tử -> PRG ----
if ($booking !== null && $method === 'POST') {
    $workshopId = (string)($booking['workshop_id'] ?? '');   // lấy từ Session, KHÔNG từ client
    $input = $_POST;
    $input['workshop_id'] = $workshopId;                     // bỏ qua workshop_id/price client gửi

    $result = validate_booking($input, $workshops);          // tái sử dụng (E03)

    if ($result['errors']) {                                 // E06: giữ dữ liệu, Session booking không đổi
        $_SESSION['edit_errors'] = $result['errors'];
        $_SESSION['edit_old'] = $result['data'] + [
            'ref' => $ref,
            'quantity_raw' => (string)($_POST['quantity'] ?? ''),
        ];
        redirect_to('booking_edit.php?ref=' . urlencode($ref));
    }

    $d   = $result['data'];
    $fee = $workshops[$workshopId]['fee'];                   // E05: fee từ catalog server
    $_SESSION['bookings'][$index]['full_name'] = $d['full_name'];
    $_SESSION['bookings'][$index]['email']     = $d['email'];
    $_SESSION['bookings'][$index]['quantity']  = $d['quantity'];
    $_SESSION['bookings'][$index]['amount']    = $fee * $d['quantity'];   // tính lại ở server

    unset($_SESSION['edit_errors'], $_SESSION['edit_old']);
    $_SESSION['flash'] = 'Booking updated successfully.';
    redirect_to('dashboard.php');                            // E07: 303
}

// ---- 4. GET: chuẩn bị dữ liệu điền form ----
$errors = [];
$old    = [];
if ($booking !== null) {
    if (($_SESSION['edit_old']['ref'] ?? '') === $ref) {
        $errors = $_SESSION['edit_errors'] ?? [];
        $old    = $_SESSION['edit_old'];
    }
    unset($_SESSION['edit_errors'], $_SESSION['edit_old']);
    $workshop = $workshops[$booking['workshop_id'] ?? ''] ?? null;
    $unitFee  = $workshop['fee'] ?? 0;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit booking</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e($theme) ?>">
<main class="wrap">
<header>
  <h1>Edit Booking</h1>
  <nav><a href="index.php">Catalog</a><a href="dashboard.php">My bookings</a></nav>
</header>

<?php if ($booking === null): ?>
  <div class="panel">
    <p><strong>Booking not found.</strong> Nothing was changed.</p>
    <a class="btn btn-light" href="dashboard.php">Back to My Bookings</a>
  </div>
<?php else: ?>
  <form method="post" action="booking_edit.php" class="panel form-stack" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="ref" value="<?= e($booking['ref']) ?>">

    <label>Booking Reference
      <input value="<?= e($booking['ref']) ?>" readonly>
    </label>
    <label>Workshop
      <input value="<?= e($booking['workshop']) ?>" readonly>
    </label>
    <small class="error"><?= e($errors['workshop_id'] ?? '') ?></small>

    <label>Full Name
      <input name="full_name" value="<?= e($old['full_name'] ?? $booking['full_name']) ?>">
    </label>
    <small class="error"><?= e($errors['full_name'] ?? '') ?></small>

    <label>Email
      <input name="email" value="<?= e($old['email'] ?? $booking['email']) ?>">
    </label>
    <small class="error"><?= e($errors['email'] ?? '') ?></small>

    <label>Tickets (1–4)
      <input name="quantity" type="number" min="1" max="4"
             value="<?= e($old['quantity_raw'] ?? $booking['quantity']) ?>">
    </label>
    <small class="error"><?= e($errors['quantity'] ?? '') ?></small>

    <hr>
    <strong>Current total: <?= number_format($booking['amount']) ?> VND</strong>
    <p class="muted">Unit fee: <?= number_format($unitFee) ?> VND - Total will be recalculated after saving.</p>

    <div class="actions">
      <button>Save Changes</button>
      <a class="btn btn-light" href="dashboard.php">Cancel</a>
    </div>
  </form>
  <p class="muted">Invalid data is not saved to the Session.</p>
<?php endif; ?>
</main>
</body>
</html>
