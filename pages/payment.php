<?php
$base_url = '../';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: ../index.php");
    exit();
}

// ค้นหาข้อมูลผู้สมัคร
$stmt = $conn->prepare("SELECT * FROM competitors WHERE competitor_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$runner = $stmt->fetch();
$runner = normalize_competitor($runner);

if (!$runner) {
    die("❌ ไม่พบข้อมูลการสมัครนี้ <a href='../index.php'>กลับหน้าหลัก</a>");
}

$page_title = "ชำระเงินค่าสมัคร IT RUN 2026 | IT RUN";
$active_nav = 'register';
include __DIR__ . '/../includes/header.php';
?>

<div class="main-wrapper" style="max-width: 650px;">
  <section class="page-hero" style="padding: 34px 36px; margin-bottom: 22px;">
    <span class="page-eyebrow">💳 Payment</span>
    <h1 class="page-title-lg" style="font-size: 1.95rem;">ชำระเงินค่าสมัคร</h1>
    <p class="page-lead">สแกน PromptPay QR ผ่าน Mobile Banking เพื่อยืนยันสิทธิ์และรับ E-BIB</p>
  </section>

  <!-- Step Progress Indicator แบบ 3D Pill -->
  <div style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-bottom: 25px; font-size: 0.88rem; font-weight: 700;">
    <span style="color: #10b981; display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid #a7f3d0;">
      <span style="background: #10b981; color: #fff; width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">✓</span>
      1. กรอกข้อมูล
    </span>
    <span style="color: var(--border-color); font-size: 1.1rem;">→</span>
    <span style="color: var(--brand); display: inline-flex; align-items: center; gap: 6px; background: var(--brand-soft); padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid rgba(249, 115, 22,0.25); box-shadow: 0 4px 10px var(--brand-glow);">
      <span style="background: var(--brand); color: #fff; width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">2</span>
      2. ชำระเงิน
    </span>
    <span style="color: var(--border-color); font-size: 1.1rem;">→</span>
    <span style="color: var(--text-muted); display: inline-flex; align-items: center; gap: 6px; background: var(--brand-soft); padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid var(--border-color);">
      <span style="background: #f3e0d3; color: #8a6250; width: 18px; height: 18px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">3</span>
      3. รับ E-BIB
    </span>
  </div>

  <div class="card-box" style="text-align: center; border-top: 5px solid var(--brand); padding: 35px 25px;">
    <h2 style="font-size: 1.5rem; font-weight: 900; color: var(--text-main); margin-bottom: 6px;">
      สแกน QR Code เพื่อชำระเงิน
    </h2>
    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 25px;">
      สแกนผ่าน Mobile Banking ได้ทุกธนาคารเพื่อยืนยันสิทธิ์
    </p>

    <!-- ข้อมูลสรุปนักวิ่งในกล่อง 3D -->
    <div style="background: linear-gradient(180deg, #fffaf6 0%, #fff1e7 100%); border: 1.5px dashed #f0c9b2; border-radius: var(--radius-lg); padding: 22px; margin-bottom: 25px; text-align: left; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
        <div>
          <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">รหัสการสมัครของคุณ</span>
          <div style="font-size: 1.35rem; font-weight: 900; color: var(--brand); font-family: monospace; letter-spacing: 0.5px;">
            <?= htmlspecialchars($runner['competitor_id']) ?>
          </div>
        </div>
        <span class="badge-upcoming" style="background: #fffbeb; color: #b45309; border-color: #fde68a;">
          ⏳ รอการชำระเงิน
        </span>
      </div>

      <div style="margin-top: 14px; font-size: 0.95rem; color: var(--text-main); line-height: 1.7;">
        <div>ชื่อผู้สมัคร: <strong><?= htmlspecialchars($runner['competitor_prefix'] . $runner['competitor_firstname'] . ' ' . $runner['competitor_lastname']) ?></strong></div>
        <div>ระยะทาง: <span class="dist-tag"><?= htmlspecialchars($runner['distance_type']) ?></span></div>
        <div>ขนาดเสื้อ: <strong><?= htmlspecialchars($runner['shirt_size']) ?></strong> | คณะ: <?= htmlspecialchars($runner['faculty']) ?></div>
      </div>

      <div style="margin-top: 18px; border-top: 1px solid #f3e0d3; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <span style="font-size: 0.88rem; color: var(--text-secondary); font-weight: 600;">ยอดเงินที่ต้องโอนสุทธิ</span>
          <div style="font-size: 0.78rem; color: var(--text-muted);">(ไม่มีค่าธรรมเนียมเพิ่มเติม)</div>
        </div>
        <div style="font-size: 2.4rem; font-weight: 900; color: var(--brand); line-height: 1;">
          ฿<?= number_format($runner['amount']) ?>
        </div>
      </div>
    </div>

    <!-- บัตร QR Code พร้อมแสงเงา 3D -->
    <div style="display: inline-block; background: #fff; border: 2px solid rgba(249, 115, 22,0.25); border-radius: var(--radius-lg); padding: 20px; box-shadow: 0 12px 30px var(--brand-glow); margin-bottom: 20px;">
      <img src="../assets/images/ดาวน์โหลด.jpg" alt="PromptPay QR Code IT RUN 2026" style="width: 210px; height: 210px; object-fit: contain; display: block; border-radius: 8px; margin: 0 auto;" onerror="this.onerror=null; this.src='https://api.qrserver.com/v1/create-qr-code/?size=210x210&data=PROMPTPAY_IT_RUN_<?= htmlspecialchars($runner['competitor_id']) ?>_<?= (int) $runner['amount'] ?>';">
      
      <div style="margin-top: 12px; font-size: 0.88rem; font-weight: 700; color: var(--text-main);">
        กองทุนพัฒนากีฬา IT RUN 2026
      </div>
      <div style="font-size: 0.8rem; color: var(--text-secondary);">
        ธนาคารกรุงไทย • PromptPay QR
      </div>
    </div>

    <p style="font-size: 0.88rem; color: var(--text-secondary); margin-bottom: 25px; line-height: 1.6;">
      เมื่อทำการสแกนและโอนเงินเสร็จสิ้นแล้ว<br>
      กรุณากดปุ่ม <strong>"ยืนยันการโอนเงิน"</strong> ด้านล่างเพื่อรับหมายเลข BIB ทันที
    </p>

    <!-- ปุ่ม Action -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <a href="confirm.php?id=<?= urlencode($runner['competitor_id']) ?>&action=paid" class="btn-primary" style="justify-content: center; font-size: 1.15rem; padding: 15px; box-shadow: 0 10px 25px var(--brand-glow);">
        ✅ โอนเงินเรียบร้อยแล้ว ยืนยันการชำระเงิน
      </a>
      <a href="status.php?keyword=<?= urlencode($runner['competitor_id']) ?>" class="btn-outline" style="justify-content: center;">
        💾 บันทึกรหัสการสมัครไว้ชำระภายหลัง
      </a>
      <a href="../index.php" style="color: var(--text-secondary); font-size: 0.88rem; text-decoration: none; margin-top: 5px;">
        ย้อนกลับไปยังหน้าหลัก
      </a>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
