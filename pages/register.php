<?php
$base_url = '../';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/helpers.php';

$event_id = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 1;

$reg_event = null;
$reg_distances = [];
if ($conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $event_id]);
        $reg_event = $stmt->fetch();
        $reg_distances = event_distances($conn, $event_id);
    } catch (Exception $e) {
        $reg_event = null;
        $reg_distances = [];
    }
}

if (!$reg_distances) {
    $reg_distances = [['distance_label' => '5 km (Fun Run)', 'price' => (int) ($reg_event['price_min'] ?? 500)]];
}
$first_distance = $reg_distances[0];

if (!$reg_event) {
    header('Location: ../calendar.php');
    exit();
}

$page_title = "สมัครวิ่ง " . $reg_event['title'] . " | IT RUN";
$active_nav = 'register';
include __DIR__ . '/../includes/header.php';
?>

<?php if ($reg_event['status'] !== 'open'): ?>
  <?php $closed_title = $reg_event['status'] === 'closed' ? 'งานนี้ปิดรับสมัครแล้ว' : 'งานนี้ยังไม่เปิดรับสมัคร'; ?>
  <div class="main-wrapper" style="max-width: 820px;">
    <section class="page-hero" style="padding: 36px 38px; margin-bottom: 24px;">
      <span class="page-eyebrow">🏃 Registration</span>
      <h1 class="page-title-lg" style="font-size: 2rem;">สมัครวิ่ง <?= e($reg_event['title'] ?? 'IT RUN 2026') ?></h1>
      <p class="page-lead">
        📅 <?= e(thai_date($reg_event['event_date'])) ?> · 📍 <?= e($reg_event['location']) ?> จ.<?= e($reg_event['province']) ?>
      </p>
      <div class="hero-actions">
        <a href="../event.php?id=<?= (int) $reg_event['id'] ?>" class="btn-light">← กลับรายละเอียดงาน</a>
      </div>
    </section>

    <div class="card-box" style="border-top: 5px solid #ef4444; padding: 40px 30px; text-align: center;">
      <div style="font-size: 3rem;">⛔</div>
      <h2 style="font-size: 1.5rem; font-weight: 900; margin: 12px 0 8px;"><?= e($closed_title) ?></h2>
      <p style="color: var(--text-secondary); margin-bottom: 20px;"><?= e($reg_event['title']) ?></p>
      <a href="../event.php?id=<?= (int) $reg_event['id'] ?>" class="btn-primary">กลับไปดูรายละเอียดงาน</a>
      <a href="../calendar.php" class="btn-outline" style="margin-left: 8px;">ดูงานวิ่งอื่น</a>
    </div>
  </div>
  <?php include __DIR__ . '/../includes/footer.php'; ?>
  <?php exit(); ?>
<?php endif; ?>

<div class="main-wrapper" style="max-width: 820px;">
  <section class="page-hero" style="padding: 36px 38px; margin-bottom: 24px;">
    <span class="page-eyebrow">🏃 Registration</span>
    <h1 class="page-title-lg" style="font-size: 2rem;">สมัครวิ่ง <?= e($reg_event['title'] ?? 'IT RUN 2026') ?></h1>
    <p class="page-lead">
      📅 <?= e(thai_date($reg_event['event_date'])) ?> · 📍 <?= e($reg_event['location']) ?> จ.<?= e($reg_event['province']) ?>
    </p>
    <div class="hero-actions">
      <a href="../event.php?id=<?= (int) $event_id ?>" class="btn-light">← กลับรายละเอียดงาน</a>
      <a href="../calendar.php" class="btn-primary">🗓️ เลือกงานวิ่งอื่น</a>
    </div>
  </section>

  <div class="card-box" style="border-top: 5px solid var(--brand); padding: 35px 30px;">
    <!-- ส่วนหัวแบบฟอร์ม -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 20px; margin-bottom: 30px;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px;">
        <div>
          <span class="badge-open">แบบฟอร์มลงทะเบียนออนไลน์</span>
          <h2 style="font-size: 1.6rem; font-weight: 900; margin-top: 10px; color: var(--text-main); line-height: 1.3;">
            กรอกข้อมูลผู้สมัคร
          </h2>
          <p style="color: var(--text-secondary); font-size: 0.95rem; margin-top: 5px;">
            เลือกระยะทาง ตรวจสอบข้อมูล และดำเนินการชำระเงินในขั้นตอนถัดไป
          </p>
        </div>
        <img src="../assets/images/it logo พื้นขาว.png" alt="IT Logo" style="height: 52px; object-fit: contain;" onerror="this.style.display='none'">
      </div>
    </div>

    <form action="save_register.php" method="POST" id="regForm">
      <input type="hidden" name="event_id" value="<?= (int) $event_id ?>">
      
      <!-- หมวดเลือกระยะทาง 3D Cards -->
      <div style="margin-bottom: 35px;">
        <label class="form-label" style="font-size: 1.1rem; font-weight: 800; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
          <span>🏃</span> เลือกระยะทางที่ต้องการแข่งขัน: <span style="color: red;">*</span>
        </label>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px;">
          <?php foreach ($reg_distances as $i => $d): ?>
            <?php $isFirst = $i === 0; $value = $d['distance_label'] . '|' . (int) $d['price']; ?>
            <label class="dist-card <?= $isFirst ? 'active-card' : '' ?>"
                   onclick="selectDistance(this, <?= (int) $d['price'] ?>, '<?= htmlspecialchars($d['distance_label'], ENT_QUOTES) ?>')"
                   style="border: 2px solid <?= $isFirst ? 'var(--brand)' : 'var(--border-color)' ?>; border-radius: var(--radius-md); padding: 18px 12px; text-align: center; cursor: pointer; background: <?= $isFirst ? 'var(--brand-soft)' : '#fff' ?>; <?= $isFirst ? 'box-shadow: 0 8px 20px var(--brand-glow);' : '' ?> transition: all 0.25s;">
              <input type="radio" name="distance_type" value="<?= e($value) ?>" <?= $isFirst ? 'checked' : '' ?> style="display: none;">
              <div style="font-weight: 900; font-size: 1.15rem; color: var(--text-main);"><?= e($d['distance_label']) ?></div>
              <div style="font-weight: 900; color: var(--brand); font-size: 1.35rem; margin-top: 8px;">฿<?= number_format((int) $d['price']) ?></div>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- หมวดข้อมูลส่วนตัวของผู้สมัคร -->
      <h3 style="font-size: 1.15rem; font-weight: 800; margin: 30px 0 15px 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
        <span>👤</span> ข้อมูลส่วนตัวของผู้สมัคร
      </h3>

      <div style="display: grid; grid-template-columns: 140px 1fr 1fr; gap: 14px; margin-bottom: 15px;">
        <div class="form-group">
          <label class="form-label">คำนำหน้า <span style="color: red;">*</span></label>
          <select name="Competitor_prefix" class="form-select" required>
            <option value="นาย">นาย</option>
            <option value="นาง">นาง</option>
            <option value="นางสาว">นางสาว</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">ชื่อจริง <span style="color: red;">*</span></label>
          <input type="text" name="Competitor_firstname" class="form-input" required placeholder="ชื่อภาษาไทย">
        </div>
        <div class="form-group">
          <label class="form-label">นามสกุล <span style="color: red;">*</span></label>
          <input type="text" name="Competitor_lastname" class="form-input" required placeholder="นามสกุลภาษาไทย">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-bottom: 20px;">
        <div class="form-group">
          <label class="form-label">เบอร์โทรศัพท์มือถือ <span style="color: red;">*</span></label>
          <input type="tel" name="Competitor_phone" class="form-input" required placeholder="เช่น 0812345678" pattern="[0-9]{9,10}">
        </div>
        <div class="form-group">
          <label class="form-label">อีเมล <span style="color: red;">*</span></label>
          <input type="email" name="Competitor_email" class="form-input" required placeholder="เช่น runner@example.com">
        </div>
      </div>

      <!-- หมวดสังกัด/การศึกษา -->
      <h3 style="font-size: 1.15rem; font-weight: 800; margin: 30px 0 15px 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
        <span>🎓</span> ข้อมูลคณะ / หน่วยงาน
      </h3>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px;">
        <div class="form-group">
          <label class="form-label">คณะ / หน่วยงาน <span style="color: red;">*</span></label>
          <input type="text" name="faculty" class="form-input" required placeholder="เช่น วิทยาการสารสนเทศ หรือ ประชาชนทั่วไป">
        </div>
        <div class="form-group">
          <label class="form-label">สาขาวิชา</label>
          <input type="text" name="major" class="form-input" placeholder="เช่น เทคโนโลยีสารสนเทศ (ถ้ามี)">
        </div>
        <div class="form-group">
          <label class="form-label">ชั้นปี (หรือระบุ 0 หากเป็นศิษย์เก่า/บุคคลทั่วไป) <span style="color: red;">*</span></label>
          <input type="number" name="study_year" min="0" max="8" value="1" class="form-input" required>
        </div>
      </div>

      <!-- หมวดขนาดเสื้อ -->
      <h3 style="font-size: 1.15rem; font-weight: 800; margin: 30px 0 15px 0; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
        <span>👕</span> ไซส์เสื้อที่ระลึก IT RUN 2026
      </h3>

      <div class="form-group" style="margin-bottom: 25px;">
        <label class="form-label">เลือกไซส์เสื้อของคุณ: <span style="color: red;">*</span></label>
        <select name="shirt_size" class="form-select" id="shirtSelect" required onchange="updateSummary()">
          <option value="S">S (รอบอก 36 นิ้ว / ยาว 26 นิ้ว)</option>
          <option value="M" selected>M (รอบอก 38 นิ้ว / ยาว 27 นิ้ว)</option>
          <option value="L">L (รอบอก 40 นิ้ว / ยาว 28 นิ้ว)</option>
          <option value="XL">XL (รอบอก 42 นิ้ว / ยาว 29 นิ้ว)</option>
          <option value="2XL">2XL (รอบอก 44 นิ้ว / ยาว 30 นิ้ว)</option>
          <option value="None">None (ไม่รับเสื้อวิ่งที่ระลึก)</option>
        </select>
      </div>

      <!-- กล่องสรุปค่าใช้จ่าย 3D Order Summary Box -->
      <div style="background: linear-gradient(135deg, #fff5f4 0%, #fee2e2 100%); border-radius: var(--radius-lg); padding: 25px; margin: 30px 0; border: 1.5px solid rgba(249, 115, 22,0.25); box-shadow: 0 10px 25px var(--brand-glow); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
          <span style="font-size: 0.88rem; color: var(--brand-ink); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">สรุปรายการสมัคร</span>
          <div style="font-size: 1.25rem; font-weight: 900; color: var(--text-main); margin-top: 4px;" id="summaryDistance">
            <?= e($first_distance['distance_label']) ?>
          </div>
          <div style="font-size: 0.88rem; color: var(--text-secondary);" id="summaryShirt">
            เสื้อขนาด: M • รวมประกันอุบัติเหตุและเหรียญที่ระลึก
          </div>
        </div>

        <div style="text-align: right;">
          <span style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 700;">ยอดเงินที่ต้องชำระ</span>
          <div style="font-size: 2.4rem; font-weight: 900; color: var(--brand); line-height: 1.1;" id="displayPrice">
            ฿<?= number_format((int) $first_distance['price']) ?>
          </div>
        </div>
      </div>

      <!-- ปุ่มส่งข้อมูล -->
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; font-size: 1.2rem; padding: 16px; border-radius: var(--radius-md); box-shadow: 0 10px 25px var(--brand-glow);">
        ดำเนินการต่อและไปยังหน้าชำระเงิน →
      </button>

      <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 15px;">
        🔒 ข้อมูลของท่านจะถูกเก็บรักษาอย่างปลอดภัยเพื่อใช้ในการออกหมายเลข BIB และประกันอุบัติเหตุเท่านั้น
      </p>
    </form>
  </div>
</div>

<script>
    let currentPrice = <?= (int) $first_distance['price'] ?>;
    let currentDistName = <?= json_encode($first_distance['distance_label'], JSON_UNESCAPED_UNICODE) ?>;

function selectDistance(element, price, distName) {
  document.querySelectorAll('.dist-card').forEach(c => {
    c.style.borderColor = 'var(--border-color)';
    c.style.background = '#fff';
    c.style.boxShadow = 'none';
  });

  element.style.borderColor = 'var(--brand)';
  element.style.background = 'var(--brand-soft)';
  element.style.boxShadow = '0 8px 20px var(--brand-glow)';
  
  const radio = element.querySelector('input[type="radio"]');
  if (radio) radio.checked = true;

  currentPrice = price;
  currentDistName = distName;
  updateSummary();
}

function updateSummary() {
  const shirtVal = document.getElementById('shirtSelect').value;
  document.getElementById('displayPrice').innerText = '฿' + Number(currentPrice).toLocaleString();
  document.getElementById('summaryDistance').innerText = currentDistName;
  document.getElementById('summaryShirt').innerText = 'เสื้อขนาด: ' + shirtVal + ' • รวมประกันอุบัติเหตุและของที่ระลึก';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
