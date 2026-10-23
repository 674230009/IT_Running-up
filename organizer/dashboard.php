<?php
require_once __DIR__ . '/../includes/auth.php';
$user = auth_require_role(['organizer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$total_runners = 0;
$total_paid = 0;
$total_pending = 0;
$total_revenue = 0;
$total_events = 0;
$open_events = 0;
$latest = [];
$upcoming_events = [];

if ($conn) {
    try {
        $total_runners = (int) $conn->query("SELECT COUNT(*) FROM competitors")->fetchColumn();
        $total_paid = (int) $conn->query("SELECT COUNT(*) FROM competitors WHERE payment_status = 'paid'")->fetchColumn();
        $total_pending = max(0, $total_runners - $total_paid);
        $total_revenue = (int) $conn->query("SELECT IFNULL(SUM(amount), 0) FROM competitors WHERE payment_status = 'paid'")->fetchColumn();
        $total_events = (int) $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $open_events = (int) $conn->query("SELECT COUNT(*) FROM events WHERE status = 'open'")->fetchColumn();
        $latest = $conn->query("SELECT * FROM competitors ORDER BY register_date DESC LIMIT 6")->fetchAll();
        $latest = normalize_competitors($latest);
        $upcoming_events = $conn->query("SELECT * FROM events WHERE status <> 'closed' ORDER BY event_date ASC LIMIT 4")->fetchAll();
    } catch (Exception $e) {
        // ใช้ค่าเริ่มต้นหากฐานข้อมูลยังไม่พร้อม
    }
}

$console_title = 'แดชบอร์ดผู้จัดงาน';
$console_subtitle = 'ติดตามยอดสมัคร งานวิ่ง และการออกหมายเลข BIB';
$console_active = 'dashboard';
include __DIR__ . '/../includes/console_header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">ผู้สมัครทั้งหมด</span>
      <span class="stat-icon">👥</span>
    </div>
    <div class="stat-value"><?= number_format($total_runners) ?> <small>คน</small></div>
  </div>

  <div class="stat-card teal">
    <div class="stat-top">
      <span class="stat-label">ชำระเงินแล้ว</span>
      <span class="stat-icon">✅</span>
    </div>
    <div class="stat-value"><?= number_format($total_paid) ?> <small>คน</small></div>
  </div>

  <div class="stat-card amber">
    <div class="stat-top">
      <span class="stat-label">รอตรวจสอบการชำระ</span>
      <span class="stat-icon">⏳</span>
    </div>
    <div class="stat-value"><?= number_format($total_pending) ?> <small>คน</small></div>
  </div>

  <div class="stat-card ink">
    <div class="stat-top">
      <span class="stat-label">ยอดชำระสะสม</span>
      <span class="stat-icon">💰</span>
    </div>
    <div class="stat-value">฿<?= number_format($total_revenue) ?></div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>⚡ งานด่วนของผู้จัด</h2>
  </div>
  <div class="quick-actions">
    <a href="../admin/runners_list.php" class="quick-action">
      <span class="qa-icon">✅</span>
      <span>อนุมัติการชำระเงิน<small>ตรวจสลิปและออกหมายเลข BIB</small></span>
    </a>
    <a href="../admin/events_manage.php" class="quick-action">
      <span class="qa-icon">🏁</span>
      <span>จัดการงานวิ่ง<small>แก้ไขข้อมูลและสถานะรับสมัคร</small></span>
    </a>
    <a href="../admin/event_add.php" class="quick-action">
      <span class="qa-icon">➕</span>
      <span>เพิ่มงานวิ่งใหม่<small>สร้างงานและกำหนดระยะทาง</small></span>
    </a>
    <a href="../index.php" target="_blank" class="quick-action">
      <span class="qa-icon">🌐</span>
      <span>เปิดหน้าเว็บหลัก<small>ตรวจสอบมุมมองนักวิ่ง</small></span>
    </a>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>🗓️ งานวิ่งที่ดูแล</h2>
    <a href="../admin/events_manage.php">จัดการทั้งหมด →</a>
  </div>
  <?php if (!empty($upcoming_events)): ?>
    <div class="event-grid">
      <?php foreach ($upcoming_events as $ev): ?>
        <div class="event-tile">
          <div class="event-tile-body">
            <div style="display: flex; justify-content: space-between; gap: 10px; align-items: flex-start;">
              <h3><?= htmlspecialchars($ev['title']) ?></h3>
              <?php if ($ev['status'] === 'open'): ?>
                <span class="badge-open">เปิดรับ</span>
              <?php else: ?>
                <span class="badge-upcoming">เร็วๆ นี้</span>
              <?php endif; ?>
            </div>
            <div class="event-tile-meta">📅 <?php echo date('d/m/Y', strtotime($ev['event_date'])); ?></div>
            <div class="event-tile-meta">📍 <?= htmlspecialchars($ev['province']) ?></div>
            <div class="event-tile-meta">🏃 <?= htmlspecialchars($ev['distances']) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state"><span class="emoji">🗓️</span><p>ยังไม่มีงานวิ่งในระบบ</p></div>
  <?php endif; ?>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>📋 ผู้สมัครล่าสุด</h2>
    <a href="../admin/runners_list.php">ดูรายชื่อทั้งหมด →</a>
  </div>

  <div class="table-wrap">
    <table class="custom-table">
      <thead>
        <tr>
          <th>รหัส</th>
          <th>ชื่อ - นามสกุล</th>
          <th>ระยะทาง</th>
          <th>ยอดเงิน</th>
          <th>สถานะ</th>
          <th>BIB</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($latest)): ?>
          <?php foreach ($latest as $r): ?>
            <tr>
              <td><strong style="font-family: monospace;"><?= htmlspecialchars($r['competitor_id']) ?></strong></td>
              <td><?= htmlspecialchars($r['competitor_prefix'] . $r['competitor_firstname'] . ' ' . $r['competitor_lastname']) ?></td>
              <td><span class="dist-tag"><?= htmlspecialchars($r['distance_type'] ?? '5 km') ?></span></td>
              <td>฿<?= number_format((int) ($r['amount'] ?? 0)) ?></td>
              <td>
                <?php if (($r['payment_status'] ?? '') === 'paid'): ?>
                  <span class="badge-paid">ชำระแล้ว</span>
                <?php else: ?>
                  <span class="badge-pending">รอชำระ</span>
                <?php endif; ?>
              </td>
              <td><strong style="color: var(--accent-ink); font-family: monospace;"><?= htmlspecialchars($r['bib_number'] ?? '-') ?></strong></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">ยังไม่มีข้อมูลผู้สมัครในระบบ</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/console_footer.php'; ?>
