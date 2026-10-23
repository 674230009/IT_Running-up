<?php
require_once __DIR__ . '/../includes/auth.php';
$admin_user = auth_require_role(['admin']);
require_once __DIR__ . '/../config/db.php';

$error = '';
$success = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'organizer') === 'admin' ? 'admin' : 'organizer';

    if ($username === '' || $name === '' || $password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO admins (username, password, name, role)
                VALUES (:username, :password, :name, :role)");
            $stmt->execute([
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':name' => $name,
                ':role' => $role,
            ]);
            header('Location: staff.php?msg=added');
            exit();
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'
                : 'ไม่สามารถบันทึกบัญชีได้: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete']) && $conn) {
    $deleteId = (int) $_GET['delete'];
    if ($deleteId !== (int) $admin_user['id'] && $deleteId > 0) {
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = :id");
        $stmt->execute([':id' => $deleteId]);
        header('Location: staff.php?msg=deleted');
        exit();
    }
    header('Location: staff.php?msg=self');
    exit();
}

$staff = [];
if ($conn) {
    try {
        $staff = $conn->query("SELECT id, username, name, role, created_at FROM admins ORDER BY role ASC, id ASC")->fetchAll();
    } catch (Exception $e) {
        $staff = [];
    }
}

$console_title = 'ผู้ใช้งาน & สิทธิ์';
$console_subtitle = 'จัดการบัญชีผู้จัดงานและผู้ดูแลระบบ';
$console_active = 'staff';
include __DIR__ . '/../includes/console_header.php';
?>

<?php if ($error): ?>
  <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success === 'added'): ?>
  <div class="alert-success">✓ เพิ่มบัญชีผู้ใช้งานเรียบร้อยแล้ว</div>
<?php elseif ($success === 'deleted'): ?>
  <div class="alert-success">✓ ลบบัญชีผู้ใช้งานเรียบร้อยแล้ว</div>
<?php elseif ($success === 'self'): ?>
  <div class="alert-error">⚠️ ไม่สามารถลบบัญชีที่กำลังใช้งานอยู่ได้</div>
<?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h2>➕ เพิ่มบัญชีผู้ใช้งานใหม่</h2>
    <span class="pill-soft"><?= count($staff) ?> บัญชีในระบบ</span>
  </div>

  <form action="staff.php" method="POST">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
      <div class="form-group">
        <label class="form-label">ชื่อผู้ใช้ (Username) <span style="color: red;">*</span></label>
        <input type="text" name="username" class="form-input" required placeholder="เช่น organizer2">
      </div>
      <div class="form-group">
        <label class="form-label">ชื่อที่แสดง <span style="color: red;">*</span></label>
        <input type="text" name="name" class="form-input" required placeholder="เช่น ทีมผู้จัดนครปฐม">
      </div>
      <div class="form-group">
        <label class="form-label">รหัสผ่าน <span style="color: red;">*</span></label>
        <input type="password" name="password" class="form-input" required placeholder="อย่างน้อย 6 ตัวอักษร">
      </div>
      <div class="form-group">
        <label class="form-label">สิทธิ์การใช้งาน</label>
        <select name="role" class="form-select">
          <option value="organizer">ผู้จัดงาน (Organizer)</option>
          <option value="admin">แอดมิน (Administrator)</option>
        </select>
      </div>
    </div>

    <button type="submit" class="btn-primary" style="margin-top: 4px;">บันทึกบัญชี →</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head">
    <h2>👥 บัญชีผู้ใช้งานทั้งหมด</h2>
  </div>

  <div class="table-wrap">
    <table class="custom-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่อผู้ใช้</th>
          <th>ชื่อที่แสดง</th>
          <th>สิทธิ์</th>
          <th>สร้างเมื่อ</th>
          <th style="text-align: right;">จัดการ</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($staff)): ?>
          <?php foreach ($staff as $staffRow): ?>
            <tr>
              <td><?= (int) $staffRow['id'] ?></td>
              <td><strong style="font-family: monospace;"><?= htmlspecialchars($staffRow['username']) ?></strong></td>
              <td><?= htmlspecialchars($staffRow['name']) ?></td>
              <td>
                <?php if (($staffRow['role'] ?? '') === 'admin'): ?>
                  <span class="pill-accent">🛡️ แอดมิน</span>
                <?php else: ?>
                  <span class="pill-soft">📋 ผู้จัดงาน</span>
                <?php endif; ?>
              </td>
              <td style="color: var(--text-muted); font-size: 0.85rem;">
                <?= $staffRow['created_at'] ? date('d/m/Y H:i', strtotime($staffRow['created_at'])) : '-' ?>
              </td>
              <td style="text-align: right;">
                <?php if ((int) $staffRow['id'] === (int) $admin_user['id']): ?>
                  <span style="color: var(--text-muted); font-size: 0.82rem;">บัญชีปัจจุบัน</span>
                <?php else: ?>
                  <a href="staff.php?delete=<?= (int) $staffRow['id'] ?>"
                     onclick="return confirm('ยืนยันลบบัญชี <?= htmlspecialchars($staffRow['username'], ENT_QUOTES) ?> ?')"
                     class="btn-outline" style="color: #ef4444; border-color: #fecaca; padding: 5px 12px; font-size: 0.8rem;">
                    ลบ
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 30px;">ยังไม่มีบัญชีผู้ใช้งาน</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/console_footer.php'; ?>
