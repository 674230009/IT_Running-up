<?php
$base_url = '';
$page_title = 'ปฏิทินงานวิ่ง | IT RUN 2026';
$active_nav = 'calendar';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/helpers.php';

$province = trim($_GET['province'] ?? '');
$month = trim($_GET['month'] ?? '');
$type = trim($_GET['type'] ?? '');

$where = [];
$params = [];

if ($province !== '') {
    $where[] = "province = :province";
    $params[':province'] = $province;
}
if ($month !== '') {
    $where[] = "DATE_FORMAT(event_date, '%Y-%m') = :month";
    $params[':month'] = $month;
}
if ($type !== '') {
    $where[] = "distances LIKE :type";
    $params[':type'] = '%' . $type . '%';
}

$sql = "SELECT * FROM events";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$provinces = $conn->query("SELECT DISTINCT province FROM events WHERE province IS NOT NULL AND province <> '' ORDER BY province")->fetchAll(PDO::FETCH_COLUMN);
$months = $conn->query("SELECT DISTINCT DATE_FORMAT(event_date, '%Y-%m') AS ym FROM events ORDER BY ym")->fetchAll(PDO::FETCH_COLUMN);

$types = ['ฟันรัน', 'มินิมาราธอน', 'ฮาล์ฟมาราธอน', 'ฟูลมาราธอน'];

include __DIR__ . '/includes/header.php';
?>

<div class="main-wrapper">
  <section class="page-hero" style="padding: 38px 40px;">
    <span class="page-eyebrow">🗓️ Calendar</span>
    <h1 class="page-title-lg" style="font-size: 2.1rem;">ปฏิทินงานวิ่ง IT RUN 2026</h1>
    <p class="page-lead">ค้นหางานวิ่งตามจังหวัด เดือน และประเภทระยะทาง แล้วเลือกงานที่ใช่สำหรับคุณ</p>
  </section>

  <form class="filter-bar" method="GET" action="calendar.php">
    <div class="form-group">
      <label class="form-label">จังหวัด</label>
      <select name="province" class="form-select">
        <option value="">ทุกจังหวัด</option>
        <?php foreach ($provinces as $p): ?>
          <option value="<?= e($p) ?>" <?= $province === $p ? 'selected' : '' ?>><?= e($p) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">เดือน</label>
      <select name="month" class="form-select">
        <option value="">ทุกเดือน</option>
        <?php foreach ($months as $ym): ?>
          <option value="<?= e($ym) ?>" <?= $month === $ym ? 'selected' : '' ?>><?= e(preg_replace('/^\d+\s+/u', '', thai_date($ym . '-01'))) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">ประเภท</label>
      <select name="type" class="form-select">
        <option value="">ทุกประเภท</option>
        <?php foreach ($types as $t): ?>
          <option value="<?= e($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="display: flex; gap: 10px;">
      <button type="submit" class="btn-primary" style="flex: 1;">🔍 ค้นหา</button>
      <a href="calendar.php" class="btn-outline">ล้าง</a>
    </div>
  </form>

  <div class="section-head" style="margin-top: 10px;">
    <div>
      <h2>พบทั้งหมด <?= count($events) ?> งาน</h2>
      <p>เรียงตามวันที่จัดงานจากใกล้ที่สุด</p>
    </div>
  </div>

  <?php if ($events): ?>
    <div class="event-grid">
      <?php foreach ($events as $ev): ?>
        <a href="event.php?id=<?= (int) $ev['id'] ?>" class="event-tile">
          <div class="event-tile-media">
            <img src="<?= e(event_banner($ev['banner'] ?? '')) ?>" alt="<?= e($ev['title']) ?>">
            <?php if ($ev['status'] === 'open'): ?>
              <span class="badge-open">เปิดรับสมัคร</span>
            <?php elseif ($ev['status'] === 'closed'): ?>
              <span class="badge-closed">ปิดรับสมัคร</span>
            <?php else: ?>
              <span class="badge-upcoming">เร็วๆ นี้</span>
            <?php endif; ?>
          </div>
          <div class="event-tile-body">
            <h3><?= e($ev['title']) ?></h3>
            <div class="event-tile-meta">📅 <?= e(thai_date($ev['event_date'])) ?></div>
            <div class="event-tile-meta">📍 <?= e($ev['location']) ?> จ.<?= e($ev['province']) ?></div>
            <div class="event-tile-meta">🏃 <?= e($ev['distances']) ?></div>
            <div class="event-tile-price">฿<?= number_format((int) $ev['price_min']) ?> – ฿<?= number_format((int) $ev['price_max']) ?></div>
            <div class="event-tile-footer">
              <span class="btn-primary" style="width: 100%; padding: 9px 16px; font-size: 0.88rem;">ดูรายละเอียด →</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="panel empty-state">
      <span class="emoji">🔍</span>
      <h3>ไม่พบงานวิ่งที่ตรงกับเงื่อนไข</h3>
      <p>ลองล้างตัวกรองแล้วค้นหาใหม่อีกครั้ง</p>
      <a href="calendar.php" class="btn-outline" style="margin-top: 16px;">ล้างตัวกรอง</a>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
