-- 1. สร้างฐานข้อมูล (หากยังไม่มี) และกำหนด Character Set
CREATE DATABASE IF NOT EXISTS it_running_db 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 2. เลือกใช้งานฐานข้อมูล
USE it_running_db;

-- 3. สร้างตาราง
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    location VARCHAR(255) NULL,
    province VARCHAR(100) NULL,
    distances VARCHAR(255) NULL,
    price_min INT DEFAULT 500,
    price_max INT DEFAULT 1800,
    banner VARCHAR(255) DEFAULT 'default_banner.jpg',
    description TEXT NULL,
    status ENUM('open','closed','upcoming') DEFAULT 'open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_distances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    distance_label VARCHAR(50) NOT NULL,
    price INT NOT NULL,
    UNIQUE KEY uq_event_distance (event_id, distance_label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS competitors (
    Competitor_id VARCHAR(20) PRIMARY KEY,
    Competitor_prefix VARCHAR(10) NULL,
    Competitor_firstname VARCHAR(50) NOT NULL,
    Competitor_lastname VARCHAR(50) NOT NULL,
    Competitor_email VARCHAR(100) NOT NULL,
    Competitor_phone VARCHAR(20) NULL,
    faculty VARCHAR(100) NULL,
    major VARCHAR(100) NULL,
    study_year INT(2) DEFAULT 0,
    shirt_size VARCHAR(5) NOT NULL,
    register_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending','paid') DEFAULT 'pending',
    bib_number VARCHAR(20) NULL,
    amount INT DEFAULT 500,
    distance_type VARCHAR(50) DEFAULT '5 km (Fun Run)',
    event_id INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin','organizer') NOT NULL DEFAULT 'organizer',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO admins (username, password, name, role) VALUES
('admin', '$2y$10$iA1U9BKu19NaFCg9UBk4D.EFOwV9EVTLDwaBOQ3iJMwruRRLm92Ia', 'ผู้ดูแลระบบ IT RUN', 'admin'),
('organizer', '$2y$10$iA1U9BKu19NaFCg9UBk4D.EFOwV9EVTLDwaBOQ3iJMwruRRLm92Ia', 'ผู้จัดงาน IT RUN', 'organizer');

INSERT IGNORE INTO events (id, title, event_date, location, province, distances, price_min, price_max, banner, description, status) VALUES
(1, 'IT RUN 2026 ก้าวด้วยใจ ไอทีสัมพันธ์', '2026-11-22', 'วัดเขารังจันทร์ราม อ.เมือง', 'นครสวรรค์', 'ฟันรัน · มินิมาราธอน', 500, 1800, 'it_run_banner.jpg', 'งานวิ่งสานสัมพันธ์นักศึกษาและประชาชนทั่วไป ชิงถ้วยรางวัลและเหรียญที่ระลึกสุดพิเศษ', 'open'),
(2, 'Nakhon Pathom Run ครั้งที่ 1', '2026-10-25', 'องค์พระปฐมเจดีย์ อ.เมืองนครปฐม', 'นครปฐม', 'มินิมาราธอน · ฟันรัน', 500, 800, 'np_run.jpg', 'สัมผัสบรรยากาศวิ่งรอบองค์พระปฐมเจดีย์ แลนด์มาร์กสำคัญของจังหวัดนครปฐม', 'open'),
(3, 'MUSS Mini Marathon ครั้งที่ 11', '2026-11-21', 'สนามกีฬาฟุตบอล วิทยาลัยวิทยาศาสตร์การกีฬา ม.มหิดล (ศาลายา)', 'นครปฐม', 'มินิมาราธอน · ฟันรัน', 600, 900, 'muss_run.jpg', 'จัดโดยวิทยาลัยวิทยาศาสตร์และเทคโนโลยีการกีฬา มหาวิทยาลัยมหิดล ศาลายา', 'open'),
(4, 'Banglen Run For Life 2026', '2026-11-22', 'วัดบางเลน อ.บางเลน', 'นครปฐม', 'มินิมาราธอน · ฟันรัน', 450, 750, 'banglen.jpg', 'วิ่งการกุศลเพื่อสุขภาพและส่งเสริมการท่องเที่ยวชุมชนริมน้ำบางเลน', 'open'),
(5, 'Speed 42 กำแพงแสนมาราธอน', '2027-07-25', 'สนามกีฬา 1 ม.เกษตรศาสตร์ กำแพงแสน', 'นครปฐม', 'ฟูลมาราธอน · ฮาล์ฟมาราธอน', 900, 1600, 'speed42.jpg', 'สนามประลองความเร็วระยะไกล มาตรฐานสากล วิ่งท่ามกลางบรรยากาศธรรมชาติ', 'upcoming');

INSERT IGNORE INTO event_distances (event_id, distance_label, price) VALUES
(1, '5 km (Fun Run)', 500),
(1, '11 km', 800),
(1, '25 km', 1400),
(1, '40 km', 1800),
(2, '5 km (Fun Run)', 500),
(2, '10.5 km (Mini Marathon)', 800),
(3, '5 km (Fun Run)', 600),
(3, '10.5 km (Mini Marathon)', 900),
(4, '5 km (Fun Run)', 450),
(4, '10.5 km (Mini Marathon)', 750),
(5, '21.1 km (Half Marathon)', 900),
(5, '42.195 km (Full Marathon)', 1600);
