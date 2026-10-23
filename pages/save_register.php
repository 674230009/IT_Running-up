<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!$conn) {
        die("❌ ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาเปิด MySQL ใน XAMPP Control Panel <a href='register.php'>ย้อนกลับ</a>");
    }
    try {
        // แยกข้อมูลระยะทางและราคา (เช่น "5 km (Fun Run)|500")
        $dist_raw = $_POST['distance_type'] ?? '';
        $dist_parts = explode('|', $dist_raw);
        $distance_name = trim($dist_parts[0] ?? '');
        $event_id = intval($_POST['event_id'] ?? 0);

        // ตรวจสอบงานวิ่งและราคาจากฐานข้อมูล (ไม่เชื่อราคาจากฟอร์ม)
        $eventStmt = $conn->prepare("SELECT id, title, status FROM events WHERE id = :id LIMIT 1");
        $eventStmt->execute([':id' => $event_id]);
        $event = $eventStmt->fetch();

        if (!$event) {
            die("❌ ไม่พบงานวิ่งที่เลือก <a href='calendar.php'>กลับไปเลือกงานวิ่ง</a>");
        }
        if ($event['status'] !== 'open') {
            die("❌ งานวิ่งนี้ปิดรับสมัครแล้ว <a href='../event.php?id=" . (int) $event_id . "'>กลับไปดูรายละเอียด</a>");
        }

        $priceStmt = $conn->prepare("SELECT price FROM event_distances WHERE event_id = :event_id AND distance_label = :label LIMIT 1");
        $priceStmt->execute([':event_id' => $event_id, ':label' => $distance_name]);
        $distance_price = $priceStmt->fetchColumn();

        if ($distance_name === '' || $distance_price === false) {
            die("❌ ระยะทางที่เลือกไม่ถูกต้อง <a href='register.php?event_id=" . (int) $event_id . "'>กลับไปเลือกใหม่</a>");
        }
        $amount = (int) $distance_price;

        // รับข้อมูลผู้สมัคร
        $prefix = trim($_POST['Competitor_prefix'] ?? 'นาย');
        $firstname = trim($_POST['Competitor_firstname'] ?? '');
        $lastname = trim($_POST['Competitor_lastname'] ?? '');
        $phone = trim($_POST['Competitor_phone'] ?? '');
        $email = trim($_POST['Competitor_email'] ?? '');
        $faculty = trim($_POST['faculty'] ?? '');
        $major = trim($_POST['major'] ?? '');
        $study_year = intval($_POST['study_year'] ?? 1);
        $shirt_size = trim($_POST['shirt_size'] ?? 'M');

        // สร้างรหัสผู้สมัครอัตโนมัติ (เช่น RUN26-A482)
        $competitor_id = 'RUN26-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

        // ตรวจสอบความถูกต้องของข้อมูลเบื้องต้น
        if ($firstname === '' || $lastname === '' || $phone === '' || $email === '') {
            die("❌ กรุณากรอกข้อมูลสำคัญให้ครบถ้วน <a href='register.php?event_id=" . (int) $event_id . "'>ย้อนกลับไปแก้ไข</a>");
        }

        // เพิ่มข้อมูลลงในตาราง competitors
        $sql = "INSERT INTO competitors 
                (competitor_id, competitor_prefix, competitor_firstname, competitor_lastname, 
                 competitor_email, competitor_phone, faculty, major, study_year, 
                 distance_type, shirt_size, payment_status, amount, event_id, register_date) 
                VALUES 
                (:id, :prefix, :fname, :lname, :email, :phone, :faculty, :major, :year, 
                 :distance, :shirt, 'pending', :amount, :event_id, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id'       => $competitor_id,
            ':prefix'   => $prefix,
            ':fname'    => $firstname,
            ':lname'    => $lastname,
            ':email'    => $email,
            ':phone'    => $phone,
            ':faculty'  => $faculty,
            ':major'    => $major,
            ':year'     => $study_year,
            ':distance' => $distance_name,
            ':shirt'    => $shirt_size,
            ':amount'   => $amount,
            ':event_id' => $event_id
        ]);

        // นำทางไปยังขั้นตอนถัดไป
        if ($amount > 0) {
            header("Location: payment.php?id=" . urlencode($competitor_id));
            exit();
        } else {
            header("Location: confirm.php?id=" . urlencode($competitor_id));
            exit();
        }

    } catch (PDOException $e) {
        die("❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage() . " <br><a href='register.php'>ย้อนกลับ</a>");
    }
} else {
    header("Location: register.php");
    exit();
}
?>

