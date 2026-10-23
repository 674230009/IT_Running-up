<?php
$base_url = '../';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';

if (!$id) {
    header("Location: ../index.php");
    exit();
}

// หากมีการกดยืนยันการชำระเงิน
if ($action === 'paid') {
    // กำหนดคำนำหน้า BIB ตามระยะทาง
    $checkStmt = $conn->prepare("SELECT distance_type, bib_number FROM competitors WHERE competitor_id = :id");
    $checkStmt->execute([':id' => $id]);
    $curr = $checkStmt->fetch();

    if ($curr && empty($curr['bib_number'])) {
        $newBib = bib_prefix($curr['distance_type']) . '-' . rand(100, 999);

        $upStmt = $conn->prepare("UPDATE competitors SET payment_status = 'paid', bib_number = :bib WHERE competitor_id = :id");
        $upStmt->execute([':bib' => $newBib, ':id' => $id]);
    } else {
        $upStmt = $conn->prepare("UPDATE competitors SET payment_status = 'paid' WHERE competitor_id = :id");
        $upStmt->execute([':id' => $id]);
    }
}

// ดึงข้อมูลผู้สมัคร
$stmt = $conn->prepare("SELECT * FROM competitors WHERE competitor_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$runner = $stmt->fetch();
$runner = normalize_competitor($runner);

if (!$runner) {
    die("❌ ไม่พบข้อมูลการสมัครนี้ <a href='../index.php'>กลับหน้าหลัก</a>");
}

$page_title = "ยืนยันการสมัครและรับ E-BIB | IT RUN 2026";
$active_nav = 'register';
include __DIR__ . '/../includes/header.php';
?>

<div class="main-wrapper" style="max-width: 760px;">
  <section class="page-hero" style="padding: 38px 38px; margin-bottom: 24px;">
    <span class="page-eyebrow">🎉 Registration Complete</span>
    <h1 class="page-title-lg" style="font-size: 2.15rem;">สมัครวิ่งสำเร็จ</h1>
    <p class="page-lead">ระบบยืนยันสิทธิ์และออกหมายเลขประจำตัวนักวิ่ง (E-BIB) ให้คุณแล้ว</p>
    <div class="hero-actions">
      <a href="../calendar.php" class="btn-light">🗓️ ดูงานวิ่งอื่น</a>
      <a href="status.php?keyword=<?= urlencode($runner['competitor_id']) ?>" class="btn-primary">🔍 ตรวจสอบสถานะ</a>
    </div>
  </section>

  <div class="card-box" style="text-align: center; border-top: 6px solid #10b981; padding: 40px 30px;">
    
    <div style="font-size: 3.8rem; line-height: 1; margin-bottom: 12px; filter: drop-shadow(0 4px 10px rgba(16,185,129,0.3));">
      🎉
    </div>

    <span class="badge-open" style="background: #ecfdf5; color: #047857; border-color: #a7f3d0; font-size: 0.92rem; padding: 6px 16px;">
      ✓ ลงทะเบียนและชำระเงินสำเร็จ
    </span>

    <h2 style="font-size: 1.55rem; font-weight: 900; color: #047857; margin: 12px 0 24px 0;">
      บัตรนักวิ่ง E-BIB ของคุณ
    </h2>

    <!-- บัตรประจำตัวนักวิ่ง (Official E-BIB Card) ดีไซน์ 3D Metallic สุดพรีเมียม -->
    <div class="bib-card" style="background: linear-gradient(135deg, #3b1a08 0%, #7a2e0b 100%); border-radius: var(--radius-xl); padding: 32px 30px; color: #fff; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.15); margin-bottom: 30px; position: relative; overflow: hidden; text-align: left;">
      
      <!-- Hologram Glow Effect ด้านหลัง -->
      <div style="position: absolute; top: -50px; right: -50px; width: 220px; height: 220px; background: radial-gradient(circle, var(--brand-glow) 0%, transparent 70%); pointer-events: none;"></div>

      <!-- ส่วนหัว BIB -->
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15); padding-bottom: 16px; margin-bottom: 22px; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <img src="../assets/images/it logo พื้นขาว.png" alt="IT Logo" style="height: 38px; background: #fff; padding: 2px 6px; border-radius: 6px;" onerror="this.style.display='none'">
          <div>
            <div style="font-size: 0.72rem; letter-spacing: 2px; color: #ffd9b8; text-transform: uppercase; font-weight: 800;">
              OFFICIAL RUNNER PASS • E-BIB
            </div>
            <div style="font-size: 1.25rem; font-weight: 900; color: #ffffff; letter-spacing: -0.3px;">
              IT RUN 2026
            </div>
          </div>
        </div>

        <div>
          <span style="background: var(--brand-gradient); font-weight: 900; padding: 6px 16px; border-radius: var(--radius-full); font-size: 0.9rem; box-shadow: 0 6px 16px var(--brand-glow); letter-spacing: 0.5px;">
            <?= htmlspecialchars($runner['distance_type']) ?>
          </span>
        </div>
      </div>

      <!-- เนื้อหา BIB -->
      <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px;">
        <div>
          <div style="font-size: 0.8rem; color: #ffd9b8; font-weight: 600; text-transform: uppercase;">ชื่อ-นามสกุล นักวิ่ง</div>
          <div style="font-size: 1.65rem; font-weight: 900; color: #ffffff; letter-spacing: 0.3px; margin: 2px 0 6px 0;">
            <?= htmlspecialchars($runner['competitor_prefix'] . $runner['competitor_firstname'] . ' ' . $runner['competitor_lastname']) ?>
          </div>
          <div style="font-size: 0.92rem; color: #ffe9d6;">
            สังกัด: <strong><?= htmlspecialchars($runner['faculty']) ?></strong>
          </div>
          <div style="font-size: 0.85rem; color: #ffd9b8; margin-top: 3px;">
            ไซส์เสื้อ: <span style="color: #38bdf8; font-weight: 800;"><?= htmlspecialchars($runner['shirt_size']) ?></span>
            &nbsp;•&nbsp; รหัส: <span style="font-family: monospace; color: #ffffff;"><?= htmlspecialchars($runner['competitor_id']) ?></span>
          </div>
        </div>

        <!-- กล่องหมายเลข BIB -->
        <div style="text-align: right; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px); padding: 14px 24px; border-radius: var(--radius-lg); border: 1.5px solid rgba(56, 189, 248, 0.35); box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
          <div style="font-size: 0.72rem; color: #38bdf8; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px;">
            BIB NUMBER
          </div>
          <div style="font-size: 3rem; font-weight: 900; color: #38bdf8; font-family: monospace; line-height: 1; margin-top: 4px; text-shadow: 0 0 20px rgba(56,189,248,0.4);">
            <?= htmlspecialchars($runner['bib_number'] ?? 'PENDING') ?>
          </div>
        </div>
      </div>

      <!-- แถบล่าง BIB -->
      <div style="margin-top: 22px; pt-3; border-top: 1px dashed rgba(255,255,255,0.15); padding-top: 14px; display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: #ffd9b8; flex-wrap: wrap; gap: 8px;">
        <div>📍 วัดเขารังจันทร์ราม อ.เมือง จ.นครสวรรค์</div>
        <div>📅 22 พฤศจิกายน 2569</div>
      </div>

    </div>

    <!-- ตารางสรุปข้อมูลลงทะเบียน -->
    <div style="background: var(--brand-soft); border-radius: var(--radius-lg); padding: 22px; border: 1px solid var(--border-color); margin-bottom: 30px; text-align: left;">
      <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 12px;">
        📋 สรุปข้อมูลการสมัคร
      </h3>
      <table class="custom-table" style="margin: 0;">
        <tr>
          <td style="color: var(--text-secondary); width: 35%;">รหัสการสมัคร</td>
          <td><strong style="font-family: monospace; font-size: 1.05rem;"><?= htmlspecialchars($runner['competitor_id']) ?></strong></td>
        </tr>
        <tr>
          <td style="color: var(--text-secondary);">สถานะการชำระเงิน</td>
          <td><span class="badge-open">✓ ชำระเงินเรียบร้อยแล้ว</span></td>
        </tr>
        <tr>
          <td style="color: var(--text-secondary);">ระยะทางแข่งขัน</td>
          <td><strong><?= htmlspecialchars($runner['distance_type']) ?></strong></td>
        </tr>
        <tr>
          <td style="color: var(--text-secondary);">ยอดชำระสุทธิ</td>
          <td><strong style="color: var(--brand); font-size: 1.1rem;">฿<?= number_format($runner['amount']) ?></strong></td>
        </tr>
        <tr>
          <td style="color: var(--text-secondary);">เบอร์โทรศัพท์ติดต่อ</td>
          <td><?= htmlspecialchars($runner['competitor_phone']) ?></td>
        </tr>
      </table>
    </div>

    <!-- ปุ่ม Action -->
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
      <button onclick="window.print()" class="btn-primary" style="padding: 12px 28px; font-size: 1rem;">
        🖨️ พิมพ์ / บันทึก E-BIB
      </button>
      <a href="status.php?keyword=<?= urlencode($runner['competitor_id']) ?>" class="btn-outline" style="padding: 12px 24px; font-size: 1rem;">
        🔍 ดูสถานะในระบบ
      </a>
      <a href="../index.php" class="btn-outline" style="padding: 12px 24px; font-size: 1rem;">
        🏠 กลับหน้าหลัก
      </a>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>