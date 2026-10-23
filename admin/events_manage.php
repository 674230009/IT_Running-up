<?php
require_once __DIR__ . '/../includes/auth.php';
$user = auth_require_role(['admin', 'organizer']);
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['delete']) && $conn) {
    $delId = (int) $_GET['delete'];
    $deleted = false;
    try {
        $conn->beginTransaction();
        $conn->prepare("DELETE FROM competitors WHERE event_id = :id")->execute([':id' => $delId]);
        $conn->prepare("DELETE FROM event_distances WHERE event_id = :id")->execute([':id' => $delId]);
        $conn->prepare("DELETE FROM events WHERE id = :id")->execute([':id' => $delId]);
        $conn->commit();
        $deleted = true;
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
    }
    header('Location: events_manage.php?msg=' . ($deleted ? 'deleted' : 'error'));
    exit();
}

$events = $conn->query("SELECT * FROM events ORDER BY event_date ASC")->fetchAll();
$message = $_GET['msg'] ?? '';

$console_title = 'งานวิ่งทั้งหมด';
$console_subtitle = 'เพิ่มหรือลบงานวิ่งในปฏิทิน';
$console_active = 'events';
include __DIR__ . '/../includes/console_header.php';
?>

<?php if ($message === 'deleted'): ?>
  <div class="alert-success">✓ ลบงานวิ่งเรียบร้อยแล้ว</div>
<?php elseif ($message === 'added'): ?>
  <div class="alert-success">✓ เพิ่มงานวิ่งใหม่เรียบร้อยแล้ว</div>
<?php elseif ($message === 'error'): ?>
  <div class="alert-error">⚠️ ไม่สามารถลบงานวิ่งได้ กรุณาลองใหม่อีกครั้ง</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h2>🏁 รายการงานวิ่งในระบบ (<?= count($events) ?> งาน)</h2>
    <a href="event_add.php" class="btn-primary" style="font-size: 0.86rem; padding: 8px 18px;">➕ เพิ่มงานวิ่งใหม่</a>
  </div>

  <div class="table-wrap">
    <table class="custom-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่องานวิ่ง</th>
          <th>วันที่จัดงาน</th>
          <th>จังหวัด / สถานที่</th>
          <th>ระยะทาง</th>
          <th>ค่าสมัคร</th>
          <th>สถานะ</th>
          <th style="text-align: right;">จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($events)): ?>
          <?php foreach ($events as $ev): ?>
            <tr>
              <td><?= (int) $ev['id'] ?></td>
              <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
              <td><?= date('d/m/Y', strtotime($ev['event_date'])) ?></td>
              <td style="color: var(--text-secondary); font-size: 0.88rem;"><?= htmlspecialchars($ev['province']) ?> · <?= htmlspecialchars($ev['location']) ?></td>
              <td><span class="dist-tag"><?= htmlspecialchars($ev['distances']) ?></span></td>
              <td>฿<?= number_format((int) $ev['price_min']) ?> – ฿<?= number_format((int) $ev['price_max']) ?></td>
              <td>
                <?php if ($ev['status'] === 'open'): ?>
                  <span class="badge-open">เปิดรับสมัคร</span>
                <?php elseif ($ev['status'] === 'closed'): ?>
                  <span class="badge-closed">ปิดรับสมัคร</span>
                <?php else: ?>
                  <span class="badge-upcoming">เร็วๆ นี้</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="../event.php?id=<?= (int) $ev['id'] ?>" target="_blank" class="btn-outline" style="padding: 5px 11px; font-size: 0.78rem;">ดูหน้าเว็บ</a>
                <a href="events_manage.php?delete=<?= (int) $ev['id'] ?>" onclick="return confirm('ยืนยันลบงานวิ่งนี้? ข้อมูลผู้สมัครในงานนี้จะได้รับผลกระทบ')" class="btn-outline" style="color: #ef4444; border-color: #fecaca; padding: 5px 11px; font-size: 0.78rem;">ลบ</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">ยังไม่มีงานวิ่งในระบบ</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/console_footer.php'; ?>