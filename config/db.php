<?php
// config/db.php : เชื่อมต่อฐานข้อมูลและเตรียมโครงสร้างพื้นฐาน
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "it_running_db";
$conn = null;
$db_error = null;

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Exception $ex) {
        $db_error = $ex->getMessage();
    }
}

// สร้างตารางจากไฟล์ SQL กลางเมื่อฐานข้อมูลยังว่าง
if ($conn) {
    try {
        $hasEvents = $conn->query("SHOW TABLES LIKE 'events'")->fetchColumn();
        if (!$hasEvents) {
            $sqlFile = __DIR__ . '/../database/it_runing.sql';
            if (is_file($sqlFile)) {
                $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents($sqlFile));
                foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
                    $conn->exec($statement);
                }
            }
        }
    } catch (Exception $e) {
        $db_error = $e->getMessage();
    }
}

// อัปเดตคอลัมน์ที่จำเป็นสำหรับฐานข้อมูลเดิม
if ($conn) {
    try {
        $cols = $conn->query("SHOW COLUMNS FROM `competitors`")->fetchAll(PDO::FETCH_COLUMN);
        $columns = [
            'payment_status' => "ENUM('pending', 'paid') DEFAULT 'pending'",
            'bib_number' => "VARCHAR(20) DEFAULT NULL",
            'amount' => "INT DEFAULT 500",
            'distance_type' => "VARCHAR(50) DEFAULT '5 km (Fun Run)'",
            'event_id' => "INT DEFAULT 1",
            'faculty' => "VARCHAR(100) DEFAULT NULL",
            'study_year' => "INT(2) DEFAULT 0",
        ];
        foreach ($columns as $column => $definition) {
            if (!in_array($column, $cols, true)) {
                $conn->exec("ALTER TABLE `competitors` ADD `$column` $definition");
            }
        }
    } catch (Exception $e) {
        // ข้ามหากตารางยังไม่พร้อม
    }
}

// ตรวจสอบบทบาทผู้ใช้งานและสร้างบัญชีผู้จัดเริ่มต้น
if ($conn) {
    try {
        $adminCols = $conn->query("SHOW COLUMNS FROM `admins`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('role', $adminCols, true)) {
            $conn->exec("ALTER TABLE `admins` ADD `role` ENUM('admin', 'organizer') NOT NULL DEFAULT 'organizer'");
        }

        $conn->exec("UPDATE `admins` SET `role` = 'admin' WHERE `username` = 'admin'");

        $organizerStmt = $conn->prepare("INSERT INTO `admins` (`username`, `password`, `name`, `role`)
            VALUES ('organizer', :password, 'ผู้จัดงาน IT RUN', 'organizer')
            ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `role` = 'organizer'");
        $defaultPassword = '$2y$10$iA1U9BKu19NaFCg9UBk4D.EFOwV9EVTLDwaBOQ3iJMwruRRLm92Ia';
        $organizerStmt->execute([
            ':password' => $defaultPassword,
        ]);

        $legacyFix = $conn->prepare("UPDATE `admins` SET `password` = :new WHERE `password` = :old");
        foreach ([
            '$2y$10.22a/rP5ZgK6l4mFk8kO5HqjXjA2qUye',
            '$2y$10$w09aJg9xXf2kGqU6sPqJb.22a/rP5ZgK6l4mFk8kO5HqjXjA2qUye',
        ] as $legacyHash) {
            $legacyFix->execute([':new' => $defaultPassword, ':old' => $legacyHash]);
        }
    } catch (Exception $e) {
        // ข้ามหากตารางยังไม่พร้อม
    }
}
?>
