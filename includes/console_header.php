<?php
// includes/console_header.php : โครงหน้าจอหลังบ้านร่วมกันสำหรับผู้จัดงานและแอดมิน
require_once __DIR__ . '/auth.php';

$console_user = auth_require_login();
$console_role = $console_user['role'];

$console_title = $console_title ?? 'แดชบอร์ด';
$console_subtitle = $console_subtitle ?? '';
$console_active = $console_active ?? 'dashboard';

$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$in_organizer = strpos($script, '/organizer/') !== false;

$console_base = $in_organizer ? '../admin/' : '';
$console_root = $in_organizer ? '../../' : '../';
$console_dashboard = auth_dashboard_url($console_user);
$console_name = $console_user['name'];
$console_initial = mb_substr($console_name, 0, 1, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($console_title) ?> | IT RUN 2026</title>
  <link rel="stylesheet" href="<?= $console_root ?>assets/css/style.css?v=<?= time() ?>">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>

<div class="console-shell">
  <aside class="console-sidebar">
    <a href="<?= $console_dashboard ?>" class="console-brand">
      <img src="<?= $console_root ?>assets/images/it logo พื้นขาว.png" alt="IT RUN Logo" onerror="this.style.display='none'">
      <div>
        <strong>IT RUN 2026</strong>
        <span>Management Console</span>
      </div>
    </a>

    <span class="console-role <?= $console_role === 'admin' ? 'admin' : '' ?>">
      <?= $console_role === 'admin' ? '🛡️ Administrator' : '📋 Organizer' ?>
    </span>

    <nav class="console-nav">
      <span class="nav-sep">ภาพรวม</span>
      <a href="<?= $console_dashboard ?>" class="<?= $console_active === 'dashboard' ? 'active' : '' ?>">
        <span class="icon">📊</span> แดชบอร์ด
      </a>

      <span class="nav-sep">จัดการงานวิ่ง</span>
      <a href="<?= $console_base ?>events_manage.php" class="<?= $console_active === 'events' ? 'active' : '' ?>">
        <span class="icon">🏁</span> งานวิ่งทั้งหมด
      </a>
      <a href="<?= $console_base ?>event_add.php" class="<?= $console_active === 'event_add' ? 'active' : '' ?>">
        <span class="icon">➕</span> เพิ่มงานวิ่ง
      </a>
      <a href="<?= $console_base ?>runners_list.php" class="<?= $console_active === 'runners' ? 'active' : '' ?>">
        <span class="icon">👥</span> รายชื่อนักวิ่ง & BIB
      </a>

      <?php if ($console_role === 'admin'): ?>
        <span class="nav-sep">ดูแลระบบ</span>
        <a href="<?= $console_base ?>staff.php" class="<?= $console_active === 'staff' ? 'active' : '' ?>">
          <span class="icon">🛡️</span> ผู้ใช้งาน & สิทธิ์
        </a>
      <?php endif; ?>

      <span class="nav-sep">ทางลัด</span>
      <a href="<?= $console_root ?>portal.php">
        <span class="icon">👥</span> หน้าบทบาทผู้ใช้
      </a>
      <a href="<?= $console_root ?>index.php" target="_blank">
        <span class="icon">🌐</span> เปิดหน้าเว็บหลัก
      </a>
      <a href="<?= $console_base ?>logout.php" style="color: #fca5a5;">
        <span class="icon">🚪</span> ออกจากระบบ
      </a>
    </nav>
  </aside>

  <div class="console-main">
    <header class="console-topbar">
      <div class="console-title">
        <h1><?= htmlspecialchars($console_title) ?></h1>
        <?php if ($console_subtitle): ?>
          <p><?= htmlspecialchars($console_subtitle) ?></p>
        <?php endif; ?>
      </div>

      <div class="console-user">
        <div class="avatar-badge"><?= htmlspecialchars($console_initial) ?></div>
        <div>
          <div class="user-name" style="font-weight: 700; color: var(--text-main);"><?= htmlspecialchars($console_name) ?></div>
          <div style="font-size: 0.74rem; color: var(--text-muted);"><?= $console_role === 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้จัดงาน' ?></div>
        </div>
      </div>
    </header>

    <div class="console-content">
