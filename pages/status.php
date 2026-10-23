<?php
$base_url = '../';
$active_nav = 'status';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$keyword = trim($_GET['keyword'] ?? '');
$runners = [];

if ($conn && $keyword !== '') {
    $stmt = $conn->prepare("
        SELECT * FROM competitors 
        WHERE competitor_phone = :k 
           OR competitor_email = :k 
           OR competitor_id = :k 
           OR competitor_firstname LIKE :k2
        ORDER BY register_date DESC
    ");
    $stmt->execute([':k' => $keyword, ':k2' => "%$keyword%"]);
    $runners = $stmt->fetchAll();
    $runners = normalize_competitors($runners);
}

$page_title = "ตรวจสอบสถานะการสมัครและหมายเลข BIB | IT RUN 2026";
include __DIR__ . '/../includes/header.php';
?>

<div class="main-wrapper" style="max-width: 860px;">
  <section class="page-hero" style="padding: 36px 38px; margin-bottom: 24px;">
    <span class="page-eyebrow">🔍 Runner Status</span>
    <h1 class="page-title-lg" style="font-size: 2.05rem;">ตรวจสอบสถานะการสมัคร & BIB</h1>
    <p class="page-lead">
      ค้นหาด้วยเบอร์โทรศัพท์ อีเมล หรือรหัสการสมัคร เพื่อดูสถานะการชำระเงินและหมายเลข BIB ของคุณ
    </p>
  </section>

  <!-- กล่องฟอร์มค้นหา 3D Card -->
  <div class="card-box" style="text-align: center; border-top: 5px solid var(--brand); padding: 30px 25px;">
    <span class="badge-open" style="background: var(--brand-soft); color: var(--brand-ink); border-color: rgba(255, 90, 31, 0.25); font-size: 0.85rem; padding: 5px 14px;">
      ค้นหาข้อมูลนักวิ่ง IT RUN 2026
    </span>

    <form action="status.php" method="GET" style="display: flex; gap: 10px; max-width: 580px; margin: 18px auto 0; flex-wrap: wrap;">
      <input type="text" name="keyword" class="form-input" style="font-size: 1.05rem; padding: 14px 20px; flex: 1; min-width: 240px; border-radius: var(--radius-md);" placeholder="กรอกเบอร์โทร / อีเมล / รหัสสมัคร..." value="<?= htmlspecialchars($keyword) ?>" required>
      <button type="submit" class="btn-primary" style="padding: 14px 30px; white-space: nowrap; flex-shrink: 0; font-size: 1.05rem; border-radius: var(--radius-md);">
        🔍 ค้นหา
      </button>
    </form>
  </div>

  <?php if ($keyword !== ''): ?>
    <?php if (!empty($runners)): ?>
      <div style="margin: 25px 0 15px 0; font-weight: 700; color: var(--text-secondary); display: flex; justify-content: space-between; align-items: center;">
        <span>พบข้อมูลการสมัครทั้งหมด <strong><?= count($runners) ?></strong> รายการ:</span>
        <a href="status.php" class="btn-outline" style="padding: 4px 14px; font-size: 0.85rem;">ล้างผลการค้นหา</a>
      </div>

      <?php foreach ($runners as $r): ?>
        <div class="card-box" style="border-left: 6px solid <?= $r['payment_status'] === 'paid' ? '#10b981' : '#f59e0b' ?>; position: relative; margin-bottom: 20px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
            <div style="flex: 1; min-width: 260px;">
              <span style="font-size: 0.85rem; color: var(--text-secondary);">
                รหัสการสมัคร: <strong style="font-family: monospace; color: var(--text-main); font-size: 1.05rem;"><?= htmlspecialchars($r['competitor_id']) ?></strong>
              </span>
              <h3 style="font-size: 1.45rem; font-weight: 900; margin: 6px 0 3px 0; color: var(--text-main);">
                <?= htmlspecialchars($r['competitor_prefix'] . $r['competitor_firstname'] . ' ' . $r['competitor_lastname']) ?>
              </h3>
              <p style="color: var(--brand); font-weight: 700; font-size: 0.95rem; margin-bottom: 5px;">
                🏃 งานวิ่ง: โครงการเดิน-วิ่งการกุศล IT RUN 2026
              </p>
              <p style="color: var(--text-secondary); font-size: 0.9rem;">
                คณะ/สังกัด: <?= htmlspecialchars($r['faculty']) ?> <?= !empty($r['major']) ? '· สาขา: ' . htmlspecialchars($r['major']) : '' ?> (ชั้นปีที่ <?= htmlspecialchars($r['study_year']) ?>)
              </p>
            </div>

            <!-- กล่อง BIB Number -->
            <div style="text-align: right; background: var(--brand-soft); border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 24px; box-shadow: var(--shadow-subtle);">
              <span style="font-size: 0.72rem; color: var(--text-secondary); display: block; font-weight: 800; letter-spacing: 0.8px; text-transform: uppercase;">
                BIB NUMBER
              </span>
              <span style="font-size: 2.2rem; font-weight: 900; color: <?= $r['bib_number'] ? '#ff5a1f' : '#a98977' ?>; font-family: monospace; line-height: 1.1;">
                <?= htmlspecialchars($r['bib_number'] ?? 'รอชำระเงิน') ?>
              </span>
            </div>
          </div>

          <hr style="border: none; border-top: 1px solid var(--border-color); margin: 18px 0;">

          <!-- สรุปข้อมูลแถวล่าง -->
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; gap: 14px; font-size: 0.92rem; flex-wrap: wrap; align-items: center;">
              <span>ระยะทาง: <span class="dist-tag"><?= htmlspecialchars($r['distance_type']) ?></span></span>
              <span>เสื้อ: <strong><?= htmlspecialchars($r['shirt_size']) ?></strong></span>
              <span>สถานะ: 
                <?php if ($r['payment_status'] === 'paid'): ?>
                  <span class="badge-open">✓ ชำระแล้ว</span>
                <?php else: ?>
                  <span class="badge-upcoming" style="background: #fffbeb; color: #b45309; border-color: #fde68a;">⏳ รอชำระเงิน</span>
                <?php endif; ?>
              </span>
            </div>

            <div>
              <?php if ($r['payment_status'] !== 'paid'): ?>
                <a href="payment.php?id=<?= urlencode($r['competitor_id']) ?>" class="btn-primary" style="font-size: 0.95rem; padding: 9px 20px;">
                  💳 ชำระเงิน ฿<?= number_format($r['amount']) ?>
                </a>
              <?php else: ?>
                <a href="confirm.php?id=<?= urlencode($r['competitor_id']) ?>" class="btn-outline" style="font-size: 0.95rem; padding: 9px 20px;">
                  📄 ดูบัตร E-BIB
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <!-- กรณีไม่พบข้อมูล -->
      <div class="card-box" style="text-align: center; padding: 50px 20px; margin-top: 25px;">
        <div style="font-size: 3rem; margin-bottom: 15px;">🔍</div>
        <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px;">
          ไม่พบข้อมูลการสมัครสำหรับ "<?= htmlspecialchars($keyword) ?>"
        </h3>
        <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 25px;">
          กรุณาตรวจสอบความถูกต้องของเบอร์โทรศัพท์ อีเมล หรือรหัสการสมัครอีกครั้ง
        </p>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
          <a href="status.php" class="btn-outline">ลองค้นหาใหม่</a>
          <a href="register.php" class="btn-primary">👉 ลงทะเบียนสมัครวิ่งใหม่</a>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

