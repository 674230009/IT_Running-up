<?php
$base_url = '';
$active_nav = 'calendar';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$event = null;
$distances = [];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $event = $stmt->fetch();

    if ($event) {
        try {
            $distances = event_distances($conn, $id);
        } catch (Exception $e) {
            $distances = [];
        }
        if (!$distances) {
            $distances = [['distance_label' => 'ระยะทางตามงาน', 'price' => (int) $event['price_min']]];
        }
    }
}

$related = [];
if ($event) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE id <> :id AND status <> 'closed' ORDER BY event_date ASC LIMIT 3");
    $stmt->execute([':id' => $id]);
    $related = $stmt->fetchAll();
}

$page_title = ($event ? $event['title'] : 'ไม่พบงานวิ่ง') . ' | IT RUN 2026';
include __DIR__ . '/includes/header.php';
?>

<div class="main-wrapper" style="max-width: 1020px;">
  <div style="margin-bottom: 18px;">
    <a href="calendar.php" class="btn-outline" style="padding: 7px 16px; font-size: 0.85rem;">← กลับปฏิทินงานวิ่ง</a>
  </div>

  <?php if ($event): ?>
    <section class="page-hero" style="padding: 0; overflow: hidden;">
      <div style="display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); align-items: stretch; min-height: 300px;">
        <div style="position: relative; min-height: 260px;">
          <img src="<?= e(event_banner($event['banner'] ?? '')) ?>" alt="<?= e($event['title']) ?>" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;">
          <div style="position: absolute; inset: 0; background: linear-gradient(90deg, rgba(40,12,0,0.05) 0%, rgba(95,28,0,0.58) 100%);"></div>
        </div>
        <div style="padding: 38px 36px; display: flex; flex-direction: column; justify-content: center;">
          <?php if ($event['status'] === 'open'): ?>
            <span class="badge-open" style="align-self: flex-start; margin-bottom: 12px;">เปิดรับสมัคร</span>
          <?php elseif ($event['status'] === 'closed'): ?>
            <span class="badge-closed" style="align-self: flex-start; margin-bottom: 12px;">ปิดรับสมัคร</span>
          <?php else: ?>
            <span class="badge-upcoming" style="align-self: flex-start; margin-bottom: 12px;">เร็วๆ นี้</span>
          <?php endif; ?>
          <h1 class="page-title-lg" style="font-size: 1.85rem; margin-bottom: 14px;"><?= e($event['title']) ?></h1>
          <div style="display: flex; flex-direction: column; gap: 8px; color: #fff2e6; font-size: 0.92rem;">
            <div>📅 <?= e(thai_date($event['event_date'])) ?></div>
            <div>📍 <?= e($event['location']) ?> จ.<?= e($event['province']) ?></div>
            <div>🏃 <?= e($event['distances']) ?></div>
            <div>💰 ฿<?= number_format((int) $event['price_min']) ?> – ฿<?= number_format((int) $event['price_max']) ?></div>
          </div>
          <div class="hero-actions" style="margin-top: 26px;">
            <?php if ($event['status'] === 'open'): ?>
              <a href="pages/register.php?event_id=<?= (int) $event['id'] ?>" class="btn-primary">🏃 สมัครงานนี้</a>
            <?php else: ?>
              <span class="btn-outline" style="pointer-events: none; opacity: 0.7;"><?= $event['status'] === 'closed' ? 'ปิดรับสมัครแล้ว' : 'ยังไม่เปิดรับสมัคร' ?></span>
            <?php endif; ?>
            <a href="pages/details.php" class="btn-light" style="background: rgba(255,255,255,0.12); color: #fff; border: 1px solid rgba(255,255,255,0.22); box-shadow: none;">รายละเอียดงาน</a>
          </div>
        </div>
      </div>
    </section>

    <?php if (!empty($event['description'])): ?>
      <div class="panel" style="margin-bottom: 22px;">
        <div class="panel-head"><h2>📖 รายละเอียดงาน</h2></div>
        <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.8;"><?= nl2br(e($event['description'])) ?></p>
      </div>
    <?php endif; ?>

    <div class="panel" style="margin-bottom: 22px;">
      <div class="panel-head">
        <h2>🏁 ระยะทางและค่าสมัคร</h2>
        <span class="pill-accent"><?= count($distances) ?> ระยะทาง</span>
      </div>
      <div class="table-wrap">
        <table class="custom-table">
          <thead>
            <tr>
              <th>ระยะทาง</th>
              <th>ค่าสมัคร</th>
              <th style="text-align: right;">สมัคร</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($distances as $d): ?>
              <tr>
                <td><strong><?= e($d['distance_label']) ?></strong></td>
                <td><span style="font-weight: 900; color: var(--brand); font-size: 1.05rem;">฿<?= number_format((int) $d['price']) ?></span></td>
                <td style="text-align: right;">
                  <?php if ($event['status'] === 'open'): ?>
                    <a href="pages/register.php?event_id=<?= (int) $event['id'] ?>" class="btn-outline" style="padding: 5px 14px; font-size: 0.82rem;">เลือก</a>
                  <?php else: ?>
                    <span style="color: var(--text-muted); font-size: 0.85rem;"><?= $event['status'] === 'closed' ? 'ปิดรับ' : 'ยังไม่เปิด' ?></span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if ($related): ?>
      <div class="section-head">
        <div>
          <h2>งานวิ่งอื่นที่น่าสนใจ</h2>
          <p>เลือกดูงานที่ใกล้เคียงกัน</p>
        </div>
        <a href="calendar.php" class="btn-outline" style="padding: 8px 18px; font-size: 0.88rem;">ดูทั้งหมด →</a>
      </div>

      <div class="event-grid">
        <?php foreach ($related as $r): ?>
          <a href="event.php?id=<?= (int) $r['id'] ?>" class="event-tile">
            <div class="event-tile-media">
              <img src="<?= e(event_banner($r['banner'] ?? '')) ?>" alt="<?= e($r['title']) ?>">
            </div>
            <div class="event-tile-body">
              <h3><?= e($r['title']) ?></h3>
              <div class="event-tile-meta">📅 <?= e(thai_date($r['event_date'])) ?></div>
              <div class="event-tile-meta">📍 <?= e($r['province']) ?></div>
              <div class="event-tile-footer">
                <span class="btn-outline" style="width: 100%; padding: 8px 14px; font-size: 0.85rem;">ดูรายละเอียด →</span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <div class="panel empty-state">
      <span class="emoji">🚧</span>
      <h3>ไม่พบงานวิ่งนี้</h3>
      <p>งานวิ่งที่คุณกำลังค้นหาอาจถูกลบหรือไม่มีอยู่ในระบบ</p>
      <a href="calendar.php" class="btn-primary" style="margin-top: 16px;">กลับปฏิทินงานวิ่ง</a>
    </div>
  <?php endif; ?>
</div>

<style>
  @media (max-width: 760px) {
    section.page-hero > div[style*="grid-template-columns"] {
      grid-template-columns: 1fr !important;
    }
    section.page-hero > div > div:first-child {
      min-height: 200px !important;
    }
  }
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
