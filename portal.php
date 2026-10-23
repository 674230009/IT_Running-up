<?php
$base_url = '';
$page_title = 'สำหรับผู้สมัคร ผู้จัดงาน และแอดมิน | IT RUN 2026';
$active_nav = 'portal';

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$current = auth_current_user();
$dashboard_url = $current ? auth_dashboard_url($current) : null;

$event_count = 0;
$runner_count = 0;
if ($conn) {
    try {
        $event_count = (int) $conn->query("SELECT COUNT(*) FROM events")->fetchColumn();
        $runner_count = (int) $conn->query("SELECT COUNT(*) FROM competitors")->fetchColumn();
    } catch (Exception $e) {
        $event_count = 0;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="main-wrapper" style="max-width: 1000px;">
  <section class="page-hero">
    <span class="page-eyebrow">👥 Role Portal</span>
    <h1 class="page-title-lg">เลือกบทบาทการใช้งาน</h1>
    <p class="page-lead">
      IT RUN 2026 แยกหน้าจอการใช้งานตามบทบาทอย่างชัดเจน
      เพื่อให้ทุกคนเข้าถึงฟังก์ชันที่ต้องการได้เร็วที่สุด
    </p>
    <div class="hero-stats">
      <div class="hero-stat"><strong><?= number_format($event_count) ?></strong><span>งานวิ่งในระบบ</span></div>
      <div class="hero-stat"><strong><?= number_format($runner_count) ?></strong><span>นักวิ่งลงทะเบียน</span></div>
    </div>
  </section>

  <?php if ($current): ?>
    <div class="panel" style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; border-left: 5px solid var(--brand);">
      <div>
        <span class="pill-accent">กำลังเข้าสู่ระบบในชื่อ <?= htmlspecialchars($current['name']) ?></span>
        <p style="margin-top: 8px; color: var(--text-secondary); font-size: 0.9rem;">
          คุณสามารถกลับไปยังแดชบอร์ดของบทบาท <?= $current['role'] === 'admin' ? 'แอดมิน' : 'ผู้จัดงาน' ?> ได้ทันที
        </p>
      </div>
      <a href="<?= $dashboard_url ?>" class="btn-primary">ไปยังแดชบอร์ด →</a>
    </div>
  <?php endif; ?>

  <div class="role-grid">
    <article class="role-card">
      <div class="role-icon">🏃</div>
      <div>
        <h3>ผู้สมัคร / นักวิ่ง</h3>
        <p>สำหรับผู้ที่ต้องการสมัครวิ่งหรือตรวจสอบข้อมูลการสมัครของตนเอง</p>
      </div>
      <ul class="role-features">
        <li>สมัครวิ่งออนไลน์ เลือกระยะทางและไซส์เสื้อ</li>
        <li>ชำระเงินผ่าน PromptPay QR</li>
        <li>ตรวจสอบสถานะและพิมพ์ E-BIB</li>
      </ul>
      <div class="role-cta" style="display: flex; flex-direction: column; gap: 8px;">
        <a href="pages/register.php" class="btn-primary" style="width: 100%;">สมัครวิ่ง →</a>
        <a href="pages/status.php" class="btn-outline" style="width: 100%;">ตรวจสอบสถานะ</a>
      </div>
    </article>

    <article class="role-card teal">
      <div class="role-icon">📋</div>
      <div>
        <h3>ผู้จัดงาน</h3>
        <p>สำหรับทีมผู้จัดที่ดูแลงานวิ่ง ตรวจรายชื่อ และออกหมายเลข BIB</p>
      </div>
      <ul class="role-features">
        <li>เพิ่มและแก้ไขงานวิ่ง</li>
        <li>ตรวจสอบและอนุมัติการชำระเงิน</li>
        <li>ออกหมายเลข BIB ให้ผู้สมัคร</li>
      </ul>
      <div class="role-cta">
        <a href="admin/login.php?role=organizer" class="btn-outline" style="width: 100%; border-color: rgba(13,148,136,0.35); color: var(--accent-ink);">
          เข้าสู่ระบบผู้จัดงาน →
        </a>
      </div>
    </article>

    <article class="role-card ink">
      <div class="role-icon">🛡️</div>
      <div>
        <h3>แอดมิน</h3>
        <p>สำหรับผู้ดูแลระบบ ควบคุมสิทธิ์และภาพรวมของแพลตฟอร์มทั้งหมด</p>
      </div>
      <ul class="role-features">
        <li>ภาพรวมสถิติทั้งระบบ</li>
        <li>จัดการบัญชีผู้จัดและแอดมิน</li>
        <li>ดูแลงานวิ่งและข้อมูลส่วนกลาง</li>
      </ul>
      <div class="role-cta">
        <a href="admin/login.php?role=admin" class="btn-outline" style="width: 100%; color: var(--ink); border-color: rgba(15,23,42,0.25);">
          เข้าสู่ระบบแอดมิน →
        </a>
      </div>
    </article>
  </div>

  <div class="panel" style="margin-top: 28px;">
    <div class="panel-head">
      <h2>🧭 ขั้นตอนการใช้งาน by role</h2>
    </div>
    <div class="table-wrap">
      <table class="custom-table">
        <thead>
          <tr>
            <th>บทบาท</th>
            <th>เริ่มต้นที่</th>
            <th>ทำอะไรได้บ้าง</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="pill-soft">ผู้สมัคร</span></td>
            <td>หน้าหลัก → สมัครวิ่ง</td>
            <td>สมัคร ชำระเงิน ตรวจสอบสถานะ และพิมพ์ E-BIB</td>
          </tr>
          <tr>
            <td><span class="pill-accent">ผู้จัดงาน</span></td>
            <td>เข้าสู่ระบบ → แดชบอร์ดผู้จัด</td>
            <td>จัดการงานวิ่ง รายชื่อนักวิ่ง และออก BIB</td>
          </tr>
          <tr>
            <td><span style="display:inline-flex; align-items:center; gap:6px; background:var(--brand-soft); color:var(--brand-ink); border:1px solid var(--border-color); padding:4px 12px; border-radius:999px; font-size:.78rem; font-weight:700;">แอดมิน</span></td>
            <td>เข้าสู่ระบบ → ภาพรวมระบบ</td>
            <td>จัดการสิทธิ์ผู้ใช้ ดูแลงานวิ่งและข้อมูลทั้งหมด</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
