<?php
require_once __DIR__ . '/../includes/auth.php';
$user = auth_require_role(['admin', 'organizer']);
require_once __DIR__ . '/../config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = in_array($_POST['status'] ?? 'open', ['open', 'closed', 'upcoming'], true) ? $_POST['status'] : 'open';

    $labels = $_POST['distance_label'] ?? [];
    $prices = $_POST['distance_price'] ?? [];
    $distanceRows = [];
    foreach ($labels as $i => $label) {
        $label = trim((string) $label);
        $price = (int) ($prices[$i] ?? 0);
        if ($label !== '' && $price > 0) {
            $distanceRows[] = ['label' => $label, 'price' => $price];
        }
    }

    if ($title === '' || $event_date === '' || $location === '' || $province === '') {
        $error = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
    } elseif (empty($distanceRows)) {
        $error = 'กรุณาเพิ่มระยะทางและราคาอย่างน้อย 1 รายการ';
    } else {
        $distances = implode(' · ', array_column($distanceRows, 'label'));
        $price_min = min(array_column($distanceRows, 'price'));
        $price_max = max(array_column($distanceRows, 'price'));

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("
                INSERT INTO events (title, event_date, location, province, distances, price_min, price_max, description, status)
                VALUES (:title, :date, :loc, :prov, :dist, :pmin, :pmax, :desc, :status)
            ");
            $stmt->execute([
                ':title' => $title,
                ':date' => $event_date,
                ':loc' => $location,
                ':prov' => $province,
                ':dist' => $distances,
                ':pmin' => $price_min,
                ':pmax' => $price_max,
                ':desc' => $description,
                ':status' => $status,
            ]);

            $eventId = (int) $conn->lastInsertId();
            $distStmt = $conn->prepare("INSERT INTO event_distances (event_id, distance_label, price) VALUES (:event_id, :label, :price)");
            foreach ($distanceRows as $row) {
                $distStmt->execute([
                    ':event_id' => $eventId,
                    ':label' => $row['label'],
                    ':price' => $row['price'],
                ]);
            }

            $conn->commit();
            header('Location: events_manage.php?msg=added');
            exit();
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
}

$console_title = 'เพิ่มงานวิ่งใหม่';
$console_subtitle = 'สร้างงานวิ่งและกำหนดรายละเอียดลงในปฏิทิน';
$console_active = 'event_add';
include __DIR__ . '/../includes/console_header.php';
?>

<div style="margin-bottom: 4px;">
  <a href="events_manage.php" class="btn-outline" style="font-size: 0.85rem; padding: 7px 16px;">← กลับรายการงานวิ่ง</a>
</div>

<div class="panel">
  <?php if ($error): ?>
    <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form action="event_add.php" method="POST">
    <div class="form-group">
      <label class="form-label">ชื่องานวิ่ง <span style="color: red;">*</span></label>
      <input type="text" name="title" class="form-input" required placeholder="เช่น Nakhon Pathom Marathon 2027">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
      <div class="form-group">
        <label class="form-label">วันที่จัดกิจกรรม <span style="color: red;">*</span></label>
        <input type="date" name="event_date" class="form-input" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">จังหวัดที่จัด <span style="color: red;">*</span></label>
        <input type="text" name="province" class="form-input" required placeholder="เช่น นครปฐม">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">สถานที่จัดงาน <span style="color: red;">*</span></label>
      <input type="text" name="location" class="form-input" required placeholder="เช่น มหาวิทยาลัยมหิดล ศาลายา">
    </div>

    <div class="form-group">
      <label class="form-label">ระยะทางและค่าสมัคร <span style="color: red;">*</span></label>
      <div style="display: grid; gap: 10px;">
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
            <input type="text" name="distance_label[]" class="form-input" placeholder="<?= $i === 0 ? 'เช่น 5 km (Fun Run)' : 'ระยะทางเพิ่มเติม (ไม่บังคับ)' ?>" <?= $i === 0 ? 'required' : '' ?>>
            <input type="number" name="distance_price[]" class="form-input" min="0" placeholder="ราคา (฿)" <?= $i === 0 ? 'required' : '' ?>>
          </div>
        <?php endfor; ?>
      </div>
      <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 6px;">กรอกอย่างน้อย 1 ระยะ ระบบจะคำนวณช่วงราคาให้อัตโนมัติ</p>
    </div>

    <div class="form-group">
      <label class="form-label">สถานะการรับสมัคร</label>
      <select name="status" class="form-select">
        <option value="open">เปิดรับสมัคร</option>
        <option value="upcoming">เร็วๆ นี้</option>
        <option value="closed">ปิดรับสมัคร</option>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">รายละเอียดกิจกรรมเพิ่มเติม</label>
      <textarea name="description" class="form-textarea" rows="4" placeholder="กรอกรายละเอียดกติกา สิ่งที่นักวิ่งจะได้รับ กำหนดการ..."></textarea>
    </div>

    <button type="submit" class="btn-primary" style="width: 100%; padding: 13px; font-size: 1.02rem;">บันทึกงานวิ่งลงปฏิทิน</button>
  </form>
</div>

<?php include __DIR__ . '/../includes/console_footer.php'; ?>
