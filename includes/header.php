<?php
// includes/header.php : ส่วนหัวเว็บไซต์ IT RUN 2026 (Orange + Teal)
$base = $base_url ?? '';
$page_title = $page_title ?? 'IT RUN 2026 | ก้าวด้วยใจ สานสายใยไอทีสัมพันธ์';
$active_nav = $active_nav ?? 'home';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nav_user = null;
if (!empty($_SESSION['admin_id'])) {
    $nav_user = [
        'name' => $_SESSION['admin_name'] ?? 'ผู้ใช้งาน',
        'role' => $_SESSION['admin_role'] ?? 'organizer',
    ];
}
$console_url = $base . 'admin/login.php';
if ($nav_user) {
    $console_url = $nav_user['role'] === 'admin'
        ? $base . 'admin/dashboard.php'
        : $base . 'organizer/dashboard.php';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="<?= $base ?>assets/css/style.css?v=<?= time() ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar">
  <div class="nav-container">
    <a href="<?= $base ?>index.php" class="nav-brand">
      <img src="<?= $base ?>assets/images/it logo พื้นขาว.png" alt="IT RUN Logo" style="height: 40px; width: auto; object-fit: contain;">
      <div style="display: flex; flex-direction: column; line-height: 1.15;">
        <span style="font-weight: 900; font-size: 1.25rem; color: var(--text-main); letter-spacing: -0.3px;">
          IT RUN <span style="color: var(--brand);">2026</span>
        </span>
        <span style="font-size: 0.72rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px;">
          ก้าวด้วยใจ สานสายใยไอที
        </span>
      </div>
    </a>

    <div class="nav-links">
      <a href="<?= $base ?>index.php" class="<?= $active_nav === 'home' ? 'active' : '' ?>">🏠 หน้าหลัก</a>
      <a href="<?= $base ?>calendar.php" class="<?= $active_nav === 'calendar' ? 'active' : '' ?>">🗓️ ปฏิทินงานวิ่ง</a>
      <a href="<?= $base ?>pages/details.php" class="<?= $active_nav === 'details' ? 'active' : '' ?>">📋 รายละเอียดงาน</a>
      <a href="<?= $base ?>pages/status.php" class="<?= $active_nav === 'status' ? 'active' : '' ?>">🔍 ตรวจสอบสถานะ / E-BIB</a>
      <a href="<?= $base ?>portal.php" class="<?= $active_nav === 'portal' ? 'active' : '' ?>">👥 สำหรับผู้จัด / แอดมิน</a>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
      <a href="<?= $base ?>pages/register.php" class="btn-primary btn-nav-cta" style="padding: 8px 18px; font-size: 0.92rem; border-radius: var(--radius-full);">
        🏃 สมัครวิ่ง
      </a>
      <a href="<?= $console_url ?>" class="btn-outline" style="padding: 7px 14px; font-size: 0.82rem; border-radius: var(--radius-full); color: var(--text-secondary); text-decoration: none;" title="<?= $nav_user ? 'ไปยังแดชบอร์ด' : 'เข้าสู่ระบบผู้จัดงาน / แอดมิน' ?>">
        <?= $nav_user ? '📊 ' . htmlspecialchars($nav_user['name']) : '🔐 เข้าสู่ระบบ' ?>
      </a>
    </div>
  </div>
</nav>