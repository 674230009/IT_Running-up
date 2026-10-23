<?php
require_once __DIR__ . '/../includes/auth.php';
$user = auth_require_role(['admin', 'organizer']);
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

if (isset($_GET['approve']) && $conn) {
    $cid = $_GET['approve'];
    $check = $conn->prepare("SELECT distance_type, bib_number FROM competitors WHERE competitor_id = :cid");
    $check->execute([':cid' => $cid]);
    $row = $check->fetch();

    $prefix = $row ? bib_prefix($row['distance_type']) : 'R';
    $newBib = $prefix . '-' . rand(100, 999);
    $up = $conn->prepare("UPDATE competitors SET payment_status = 'paid', bib_number = IFNULL(bib_number, :bib) WHERE competitor_id = :cid");
    $up->execute([':bib' => $newBib, ':cid' => $cid]);
    header('Location: runners_list.php?msg=approved');
    exit();
}

if (isset($_GET['delete']) && $conn) {
    $cid = $_GET['delete'];
    $del = $conn->prepare("DELETE FROM competitors WHERE competitor_id = :cid");
    $del->execute([':cid' => $cid]);
    header('Location: runners_list.php?msg=deleted');
    exit();
}

$search = trim($_GET['search'] ?? '');
$event_filter = $_GET['event_id'] ?? 'all';

$sql = "
    SELECT c.*, e.title AS event_title
    FROM competitors c
    LEFT JOIN events e ON c.event_id = e.id
    WHERE 1=1
";
$params = [];

if ($event_filter !== 'all') {
    $sql .= " AND c.event_id = :eid";
    $params[':eid'] = $event_filter;
}

if ($search !== '') {
    $sql .= " AND (c.competitor_id LIKE :q OR c.competitor_firstname LIKE :q OR c.competitor_lastname LIKE :q OR c.competitor_phone LIKE :q)";
    $params[':q'] = "%$search%";
}

$sql .= " ORDER BY c.register_date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$runners = $stmt->fetchAll();
$runners = normalize_competitors($runners);

$events = $conn->query("SELECT id, title FROM events ORDER BY event_date ASC")->fetchAll();
$message = $_GET['msg'] ?? '';

$console_title = 'รายชื่อนักวิ่ง & BIB';
$console_subtitle = 'ตรวจสอบการชำระเงินและออกหมายเลขประจำตัวนักวิ่ง';
$console_active = 'runners';
include __DIR__ . '/../includes/console_header.php';
?>

<?php if ($message === 'approved'): ?>
  <div class="alert-success">✓ อนุมัติการชำระเงินและออกหมายเลข BIB เรียบร้อยแล้ว</div>
<?php elseif ($message === 'deleted'): ?>
  <div class="alert-success">✓ ลบข้อมูลผู้สมัครเรียบร้อยแล้ว</div>
<?php endif; ?>

<div class="panel">
  <form action="runners_list.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
    <div class="form-group" style="flex: 1; min-width: 220px; margin-bottom: 0;">
      <label class="form-label">ค้นหา</label>
      <input type="text" name="search" class="form-input" placeholder="รหัส, ชื่อ หรือเบอร์โทร..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="form-group" style="min-width: 200px; margin-bottom: 0;">
      <label class="form-label">งานวิ่ง</label>
      <select name="event_id" class="form-select">
        <option value="all">ทุกงาน</option>
        <?php foreach ($events as $ev): ?>
          <option value="<?= (int) $ev['id'] ?>" <?= (string) $event_filter === (string) $ev['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($ev['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn-primary" style="padding: 11px 22px;">ค้นหา</button>
    <?php if ($search !== '' || $event_filter !== 'all'): ?>
      <a href="runners_list.php" class="btn-outline">ล้างตัวกรอง</a>
    <?php endif; ?>
  </form>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>👥 ผู้สมัครทั้งหมด <?= count($runners) ?> คน</h2>
  </div>

  <div class="table-wrap">
    <table class="custom-table">
      <thead>
        <tr>
          <th>รหัส</th>
          <th>ชื่อ - นามสกุล</th>
          <th>คณะ / หน่วยงาน</th>
          <th>ระยะทาง</th>
          <th>เสื้อ</th>
          <th>ยอดเงิน</th>
          <th>สถานะ</th>
          <th>BIB</th>
          <th style="text-align: right;">จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($runners)): ?>
          <?php foreach ($runners as $r): ?>
            <tr>
              <td><strong style="font-family: monospace;"><?= htmlspecialchars($r['competitor_id']) ?></strong></td>
              <td>
                <div style="font-weight: 700;"><?= htmlspecialchars($r['competitor_prefix'] . $r['competitor_firstname'] . ' ' . $r['competitor_lastname']) ?></div>
                <small style="color: var(--text-muted);"><?= htmlspecialchars($r['competitor_phone'] ?? '') ?></small>
              </td>
              <td style="color: var(--text-secondary); max-width: 180px; font-size: 0.85rem;">
                <?= htmlspecialchars($r['faculty'] ?? '-') ?>
              </td>
              <td><span class="dist-tag"><?= htmlspecialchars($r['distance_type'] ?? '5 km') ?></span></td>
              <td><strong><?= htmlspecialchars($r['shirt_size'] ?? '-') ?></strong></td>
              <td>฿<?= number_format((int) ($r['amount'] ?? 500)) ?></td>
              <td>
                <?php if (($r['payment_status'] ?? 'pending') === 'paid'): ?>
                  <span class="badge-paid">ชำระแล้ว</span>
                <?php else: ?>
                  <span class="badge-pending">รอชำระ</span>
                <?php endif; ?>
              </td>
              <td>
                <span style="font-weight: 800; color: var(--accent-ink); font-family: monospace; font-size: 1.02rem;">
                  <?= htmlspecialchars($r['bib_number'] ?? '-') ?>
                </span>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <?php if (($r['payment_status'] ?? '') !== 'paid'): ?>
                  <a href="runners_list.php?approve=<?= urlencode($r['competitor_id']) ?>" onclick="return confirm('ยืนยันอนุมัติยอดเงินและออก BIB ให้นักวิ่งท่านนี้?')"
                     class="btn-primary" style="padding: 5px 12px; font-size: 0.78rem; background: linear-gradient(135deg, #2dd4bf, #0d9488); box-shadow: 0 4px 12px rgba(13,148,136,.3);">
                    ✓ อนุมัติ & BIB
                  </a>
                <?php else: ?>
                  <a href="../pages/confirm.php?id=<?= urlencode($r['competitor_id']) ?>" target="_blank" class="btn-outline" style="padding: 5px 12px; font-size: 0.78rem;">
                    ดู E-BIB
                  </a>
                <?php endif; ?>
                <a href="runners_list.php?delete=<?= urlencode($r['competitor_id']) ?>" onclick="return confirm('ยืนยันลบข้อมูลผู้สมัครนี้?')"
                   class="btn-outline" style="color: #ef4444; border-color: #fecaca; padding: 5px 10px; font-size: 0.78rem; margin-left: 3px;">
                  ลบ
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">ไม่พบข้อมูลนักวิ่งที่ตรงกับเงื่อนไข</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/console_footer.php'; ?>