# IT RUN 2026

เว็บไซต์รับสมัครงานวิ่งออนไลน์ รองรับหลายงานวิ่ง พร้อมระบบหลังบ้านสำหรับผู้จัดงานและผู้ดูแลระบบ

## คุณสมบัติหลัก

- หน้าหลัก ปฏิทินงานวิ่ง และหน้ารายละเอียดงานแบบ dynamic
- สมัครวิ่ง เลือกระยะทางและราคาตามงานจากฐานข้อมูล
- ชำระเงิน ยืนยันการชำระเงิน และตรวจสอบสถานะ / E-BIB
- ระบบหลังบ้านแยกสิทธิ์ `admin` และ `organizer`
- จัดการงานวิ่ง ผู้สมัคร อนุมัติการชำระเงิน ออก BIB และจัดการบัญชีผู้ใช้งาน
- ใช้ PDO Prepared Statements และ `htmlspecialchars` เพื่อความปลอดภัย

## โครงสร้างโปรเจกต์

```text
IT_Running-up/
├─ admin/                      หน้าหลังบ้าน (login, dashboard, events, runners, staff)
├─ organizer/                  แดชบอร์ดผู้จัดงาน
├─ assets/
│  ├─ css/style.css            ธีมและเลย์เอาต์ทั้งระบบ
│  └─ images/                  โลโก้ รูปประกอบ และ QR ตัวอย่าง
├─ config/
│  ├─ db.php                   เชื่อมต่อฐานข้อมูล + migration อัตโนมัติ
│  └─ helpers.php              ฟังก์ชันช่วยเหลือ เช่น วันที่ไทยและระยะทาง
├─ database/
│  └─ it_runing.sql            สคริปต์สร้างตารางและข้อมูลตัวอย่าง
├─ includes/
│  ├─ header.php, footer.php   ส่วนกลางของหน้าสาธารณะ
│  ├─ console_header.php       เมนูหลังบ้าน
│  ├─ console_footer.php
│  └─ auth.php                 session และสิทธิ์ผู้ใช้งาน
├─ pages/
│  ├─ details.php              รายละเอียดงาน IT RUN 2026
│  ├─ register.php             ฟอร์มสมัครวิ่ง
│  ├─ save_register.php        บันทึกผู้สมัคร
│  ├─ payment.php              หน้าชำระเงิน
│  ├─ confirm.php              ยืนยันชำระเงิน / E-BIB
│  └─ status.php               ตรวจสอบสถานะ
├─ calendar.php                ปฏิทินและตัวกรองงานวิ่ง
├─ event.php                   รายละเอียดงานวิ่งแบบ dynamic
├─ index.php                   หน้าหลัก
├─ portal.php                  ทางเข้าสำหรับผู้จัดงาน / แอดมิน
└─ README.md
```

## การติดตั้ง

1. วางโปรเจกต์ไว้ใน `C:\xampp\htdocs\web_db\IT_Running-up`
2. เปิด Apache และ MySQL จาก XAMPP Control Panel
3. เปิดเว็บที่ `http://localhost/web_db/IT_Running-up/`

ฐานข้อมูล `it_running_db` จะถูกสร้างและนำเข้าจาก `database/it_runing.sql` อัตโนมัติเมื่อเปิดระบบครั้งแรก หากต้องการ import เอง:

```bash
mysql -u root it_running_db < database/it_runing.sql
```

ค่าการเชื่อมต่ออยู่ใน `config/db.php` (ค่าเริ่มต้น `root` ไม่มีรหัสผ่าน)

## บัญชีทดลอง

| บทบาท | ชื่อผู้ใช้ | รหัสผ่าน |
|---|---|---|
| ผู้ดูแลระบบ | `admin` | `123456` |
| ผู้จัดงาน | `organizer` | `123456` |

เข้าสู่ระบบที่ `admin/login.php` หรือผ่านเมนู “สำหรับผู้จัดงาน / แอดมิน”

## เส้นทางการใช้งาน

```text
index.php
   ↓
calendar.php → event.php?id=...
   ↓
pages/register.php?event_id=...
   ↓
pages/save_register.php
   ↓
pages/payment.php?id=...
   ↓
pages/confirm.php?id=...&action=paid
   ↓
pages/status.php
```

## ฐานข้อมูล

- `events` ข้อมูลงานวิ่ง
- `event_distances` ระยะทางและราคาของแต่ละงาน
- `competitors` ข้อมูลผู้สมัคร
- `admins` บัญชีผู้จัดงานและผู้ดูแลระบบ (`role` = `admin` / `organizer`)

`config/db.php` จะเพิ่มคอลัมน์ที่ขาดให้ฐานข้อมูลเดิมโดยอัตโนมัติ (เช่น `faculty`, `study_year`, `role`)

## การเพิ่มงานวิ่งใหม่

1. เพิ่มข้อมูลในตาราง `events`
2. เพิ่มระยะทางและราคาในตาราง `event_distances` ตาม `event_id`
3. วางแบนเนอร์ใน `assets/images/` ตามชื่อไฟล์ในคอลัมน์ `banner`
4. หากไม่มีรูป ระบบจะใช้ `assets/images/week.jpeg` แทน

## การทดสอบ

```bash
php -l index.php
php -l calendar.php
php -l event.php
php -l portal.php
```

ตรวจ syntax ทั้งโปรเจกต์ได้ด้วย:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

