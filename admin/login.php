<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (auth_current_user()) {
    header('Location: ' . auth_dashboard_url());
    exit();
}

$role_hint = $_GET['role'] ?? '';
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_hint = $_POST['role'] ?? '';

    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            auth_login_user($admin);
            header('Location: ' . auth_dashboard_url(auth_current_user()));
            exit();
        }
    }

    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>เข้าสู่ระบบผู้จัดงาน / แอดมิน | IT RUN 2026</title>
  <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body class="login-shell">

<div class="login-card">
  <div class="login-brand">
    <img src="../assets/images/it logo พื้นขาว.png" alt="IT RUN Logo" onerror="this.style.display='none'">
    <h1>ระบบจัดการ IT RUN 2026</h1>
    <p>สำหรับผู้จัดงานและผู้ดูแลระบบ</p>
  </div>

  <div class="login-role-tabs">
    <a href="login.php?role=organizer" class="<?= $role_hint !== 'admin' ? 'active' : '' ?>">📋 ผู้จัดงาน</a>
    <a href="login.php?role=admin" class="<?= $role_hint === 'admin' ? 'active' : '' ?>">🛡️ แอดมิน</a>
  </div>

  <?php if (!empty($error)): ?>
    <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form action="login.php" method="POST">
    <input type="hidden" name="role" value="<?= htmlspecialchars($role_hint) ?>">

    <div class="form-group">
      <label class="form-label">ชื่อผู้ใช้งาน (Username)</label>
      <input type="text" name="username" class="form-input" required placeholder="admin" value="<?= htmlspecialchars($username) ?>" autofocus>
    </div>

    <div class="form-group" style="margin-bottom: 22px;">
      <label class="form-label">รหัสผ่าน (Password)</label>
      <input type="password" name="password" class="form-input" required placeholder="••••••">
    </div>

    <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.02rem; padding: 13px;">
      เข้าสู่ระบบ →
    </button>
  </form>

  <div style="background: var(--brand-soft); border: 1px dashed rgba(249, 115, 22, 0.35); border-radius: var(--radius-md); padding: 12px 15px; margin-top: 20px; font-size: 0.8rem; color: var(--brand-ink); line-height: 1.7;">
    <strong>บัญชีทดลอง:</strong><br>
    ผู้จัดงาน — <code>organizer / 123456</code><br>
    แอดมิน — <code>admin / 123456</code>
  </div>

  <div class="login-foot">
    <a href="../portal.php">← กลับหน้าบทบาทผู้ใช้งาน</a>
    &nbsp;•&nbsp;
    <a href="../index.php">หน้าหลัก</a>
  </div>
</div>

</body>
</html>
