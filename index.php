<?php
$base_url = '';
$page_title = 'IT RUN 2026 | ก้าวด้วยใจ สานสายใยไอทีสัมพันธ์';
$active_nav = 'home';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/helpers.php';

$upcoming = [];
$total_events = 0;
$total_runners = 0;
$open_events = 0;

if ($conn) {
    try {
        $upcoming = $conn->query("SELECT * FROM events WHERE status <> 'closed' ORDER BY event_date ASC LIMIT 3")->fetchAll();
        $total_events = (int) $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $open_events = (int) $conn->query("SELECT COUNT(*) FROM events WHERE status = 'open'")->fetchColumn();
        $total_runners = (int) $conn->query("SELECT COUNT(*) FROM competitors")->fetchColumn();
    } catch (Exception $e) {
        $upcoming = [];
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="main-wrapper">
  <!-- HERO -->
  <section class="page-hero">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 30px; flex-wrap: wrap;">
      <div style="flex: 1; min-width: 280px;">
        <span class="page-eyebrow">🏁 ก้าวด้วยใจ สานสายใยไอทีสัมพันธ์</span>
        <h1 class="page-title-lg">วิ่งไปด้วยกัน<br>ใน <span class="text-gradient">IT RUN 2026</span></h1>
        <p class="page-lead">
          งานวิ่งการกุศลที่รวมนักศึกษา ศิษย์เก่า และประชาชนทั่วไปไว้ในเส้นทางเดียวกัน
          พร้อมระยะ Fun Run ถึง Full Marathon และระบบสมัครออนไลน์ที่ใช้งานง่าย
        </p>
        <div class="hero-actions">
          <a href="pages/register.php" class="btn-primary" style="font-size: 1.02rem; padding: 13px 30px;">🏃 สมัครวิ่งเลย</a>
          <a href="calendar.php" class="btn-light" style="background: rgba(255,255,255,0.12); color: #fff; box-shadow: none; border: 1px solid rgba(255,255,255,0.22);">🗓️ ดูปฏิทินงานวิ่ง</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><strong><?= number_format($open_events) ?></strong><span>งานที่เปิดรับสมัคร</span></div>
          <div class="hero-stat"><strong><?= number_format($total_events) ?></strong><span>งานวิ่งในระบบ</span></div>
          <div class="hero-stat"><strong><?= number_format($total_runners) ?></strong><span>นักวิ่งลงทะเบียน</span></div>
        </div>
      </div>
      <div style="flex-shrink: 0; width: min(360px, 100%);">
        <div style="background: rgba(255,255,255,0.16); border: 1px solid rgba(255,255,255,0.32); border-radius: var(--radius-xl); padding: 22px; backdrop-filter: blur(10px); box-shadow: 0 20px 45px -25px rgba(0,0,0,0.6);">
          <span class="pill-soft" style="background: rgba(255,255,255,0.2); color: #ffffff; border-color: rgba(255,255,255,0.4);">งานหลัก</span>
          <h3 style="font-size: 1.15rem; font-weight: 900; color: #fff; margin: 12px 0 4px;">IT RUN 2026</h3>
          <p style="font-size: 0.86rem; color: #ffedd5; line-height: 1.7;">
            📅 22 พฤศจิกายน 2569<br>
            📍 วัดเขารังจันทร์ราม จ.นครสวรรค์<br>
            🏃 5 / 11 / 25 / 40 กม.
          </p>
          <div style="margin-top: 16px; height: 1px; background: rgba(255,255,255,0.26);"></div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
            <span style="font-size: 0.78rem; color: #ffedd5; font-weight: 600;">ค่าสมัครเริ่มต้น</span>
            <strong style="font-size: 1.4rem; color: #ffffff; font-weight: 900;">฿500</strong>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ROLE PORTAL -->
  <div class="section-head">
    <div>
      <h2>เลือกเส้นทางของคุณ</h2>
      <p>ระบบเดียว รองรับ 3 บทบาท ตั้งแต่สมัครวิ่งจนถึงการจัดการหลังบ้าน</p>
    </div>
    <a href="portal.php" class="btn-outline" style="padding: 8px 18px; font-size: 0.88rem;">ดูภาพรวมทุกบทบาท →</a>
  </div>

  <div class="role-grid">
    <article class="role-card">
      <div class="role-icon">🏃</div>
      <div>
        <h3>ผู้สมัคร / นักวิ่ง</h3>
        <p>สมัครออนไลน์ เลือกระยะทาง ตรวจสอบสถานะ และรับ E-BIB ได้ในไม่กี่ขั้นตอน</p>
      </div>
      <ul class="role-features">
        <li>สมัครและชำระเงินออนไลน์</li>
        <li>ตรวจสอบสถานะและหมายเลข BIB</li>
        <li>พิมพ์บัตรนักวิ่ง E-BIB</li>
      </ul>
      <div class="role-cta">
        <a href="pages/register.php" class="btn-primary" style="width: 100%;">เริ่มสมัครวิ่ง →</a>
      </div>
    </article>

    <article class="role-card teal">
      <div class="role-icon">📋</div>
      <div>
        <h3>ผู้จัดงาน</h3>
        <p>ดูแลงานวิ่งที่รับผิดชอบ ตรวจรายชื่อผู้สมัคร และอนุมัติการชำระเงิน/ออก BIB</p>
      </div>
      <ul class="role-features">
        <li>เพิ่มและจัดการงานวิ่ง</li>
        <li>ดูรายชื่อนักวิ่งแบบเรียลไทม์</li>
        <li>อนุมัติยอดเงินและออกหมายเลข BIB</li>
      </ul>
      <div class="role-cta">
        <a href="admin/login.php?role=organizer" class="btn-outline" style="width: 100%;">เข้าสู่ระบบผู้จัดงาน →</a>
      </div>
    </article>

    <article class="role-card ink">
      <div class="role-icon">🛡️</div>
      <div>
        <h3>แอดมิน</h3>
        <p>ควบคุมภาพรวมทั้งแพลตฟอร์ม จัดการสิทธิ์ผู้ใช้งาน และดูแลข้อมูลส่วนกลาง</p>
      </div>
      <ul class="role-features">
        <li>ภาพรวมสถิติทั้งระบบ</li>
        <li>จัดการบัญชีผู้จัดและแอดมิน</li>
        <li>ดูแลงานวิ่งและรายชื่อทั้งหมด</li>
      </ul>
      <div class="role-cta">
        <a href="admin/login.php?role=admin" class="btn-outline" style="width: 100%;">เข้าสู่ระบบแอดมิน →</a>
      </div>
    </article>
  </div>

  <!-- UPCOMING EVENTS -->
  <?php if ($upcoming): ?>
    <div class="section-head">
      <div>
        <h2>งานวิ่งที่น่าสนใจ</h2>
        <p>งานที่เปิดรับสมัครและกำลังจะมาถึง</p>
      </div>
      <a href="calendar.php" class="btn-outline" style="padding: 8px 18px; font-size: 0.88rem;">ดูทั้งหมด →</a>
    </div>

    <div class="event-grid">
      <?php foreach ($upcoming as $ev): ?>
        <a href="event.php?id=<?= (int) $ev['id'] ?>" class="event-tile">
          <div class="event-tile-media">
            <img src="<?= e(event_banner($ev['banner'] ?? '')) ?>" alt="<?= e($ev['title']) ?>">
            <?php if (($ev['status'] ?? '') === 'open'): ?>
              <span class="badge-open">เปิดรับสมัคร</span>
            <?php else: ?>
              <span class="badge-upcoming">เร็วๆ นี้</span>
            <?php endif; ?>
          </div>
          <div class="event-tile-body">
            <h3><?= e($ev['title']) ?></h3>
            <div class="event-tile-meta">📅 <?= e(thai_date($ev['event_date'])) ?></div>
            <div class="event-tile-meta">📍 <?= e($ev['province']) ?></div>
            <div class="event-tile-meta">🏃 <?= e($ev['distances']) ?></div>
            <div class="event-tile-price">฿<?= number_format((int) $ev['price_min']) ?> – ฿<?= number_format((int) $ev['price_max']) ?></div>
            <div class="event-tile-footer">
              <span class="btn-primary" style="width: 100%; padding: 9px 16px; font-size: 0.88rem;">ดูรายละเอียด →</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- CTA -->
  <section class="cta-panel">
    <div>
      <h2>พร้อมออกสตาร์ทไปด้วยกันหรือยัง?</h2>
      <p>สมัครวันนี้ รับสิทธิ์ Early Bird และของที่ระลึกสุดพิเศษ Limited Edition</p>
    </div>
    <a href="pages/register.php" class="btn-light">👉 สมัครวิ่งออนไลน์</a>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
