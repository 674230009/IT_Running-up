<?php
$base_url = '../';
$page_title = "รายละเอียดการแข่งขัน IT RUN 2026 | IT RUN";
$active_nav = 'details';
include __DIR__ . '/../includes/header.php';
?>

<div class="main-wrapper" style="max-width: 920px;">
  <section class="page-hero" style="padding: 36px 38px; margin-bottom: 24px;">
    <span class="page-eyebrow">📋 Race Details</span>
    <h1 class="page-title-lg" style="font-size: 2.05rem;">รายละเอียดงาน IT RUN 2026</h1>
    <p class="page-lead">รวมระยะทาง ค่าสมัคร สิ่งที่ได้รับ รางวัล และกำหนดการจัดงานไว้ในที่เดียว</p>
    <div class="hero-actions">
      <a href="../calendar.php" class="btn-light">🗓️ ดูปฏิทินงานวิ่ง</a>
      <a href="register.php" class="btn-primary">🏃 สมัครวิ่ง</a>
    </div>
  </section>

  <!-- หมวดค่าสมัคร (Distance & Fee Table) -->
  <div class="card-box" style="border-top: 5px solid var(--brand);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
      <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
        <span>🏃</span> ระยะทางและค่าสมัคร IT RUN 2026
      </h2>
      <span class="badge-open">เปิดรับสมัคร</span>
    </div>
    
    <img src="../assets/images/week.jpeg" alt="Race Information" class="section-img" onerror="this.onerror=null; this.src='../assets/images/week.jpeg';">
    
    <div style="overflow-x: auto;">
      <table class="custom-table">
        <thead>
          <tr>
            <th>ประเภทระยะทาง</th>
            <th>Early Bird</th>
            <th>ราคาปกติ</th>
            <th>เวลาปล่อยตัว</th>
            <th>สิ่งที่ได้รับ</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>40 กม. (Full Marathon)</strong></td>
            <td><strong style="color: var(--brand); font-size: 1.15rem;">฿1,800</strong></td>
            <td style="color: var(--text-muted);">฿2,000</td>
            <td>03:30 น.</td>
            <td>เสื้อวิ่ง, Bib, เหรียญ, <span style="color: var(--brand); font-weight: 700;">เสื้อ Finisher</span></td>
          </tr>
          <tr>
            <td><strong>25 กม. (Half Marathon)</strong></td>
            <td><strong style="color: var(--brand); font-size: 1.15rem;">฿1,400</strong></td>
            <td style="color: var(--text-muted);">฿1,600</td>
            <td>04:30 น.</td>
            <td>เสื้อวิ่ง, Bib, เหรียญ, <span style="color: var(--brand); font-weight: 700;">เสื้อ Finisher</span></td>
          </tr>
          <tr>
            <td><strong>11 กม. (Mini Marathon)</strong></td>
            <td><strong style="color: var(--brand); font-size: 1.15rem;">฿800</strong></td>
            <td style="color: var(--text-muted);">฿1,000</td>
            <td>05:30 น.</td>
            <td>เสื้อวิ่ง, Bib, เหรียญ, <span style="color: var(--brand); font-weight: 700;">เสื้อ Finisher</span></td>
          </tr>
          <tr>
            <td><strong>5 กม. (Fun Run)</strong></td>
            <td><strong style="color: var(--brand); font-size: 1.15rem;">฿500</strong></td>
            <td style="color: var(--text-muted);">฿600</td>
            <td>06:00 น.</td>
            <td>เสื้อวิ่ง, Bib, เหรียญที่ระลึก</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- หมวดสิ่งที่ได้รับ -->
  <div class="card-box">
    <h2 style="font-size: 1.45rem; font-weight: 800; margin-bottom: 15px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
      <span>🎁</span> สิ่งที่ผู้สมัครจะได้รับ (Runner Kit)
    </h2>
    <img src="../assets/images/week.jpeg" alt="Runner Kit" class="section-img" onerror="this.onerror=null; this.src='../assets/images/week.jpeg';">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 15px; margin-top: 15px;">
      <div style="background: var(--brand-soft); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px;">
        <div style="font-weight: 700; color: var(--brand); margin-bottom: 4px;">👕 เสื้อวิ่งที่ระลึก IT RUN 2026</div>
        <p style="font-size: 0.9rem; color: var(--text-secondary);">เนื้อผ้าระบายเหงื่อ Micro-Dry ใส่สบาย เบา กระชับ เหมาะกับการวิ่งทุกระยะทาง</p>
      </div>

      <div style="background: var(--brand-soft); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px;">
        <div style="font-weight: 700; color: #2563eb; margin-bottom: 4px;">🏷️ หมายเลขประจำตัว (BIB Card)</div>
        <p style="font-size: 0.9rem; color: var(--text-secondary);">พร้อมชิปจับเวลามาตรฐานสากล (Timing Chip) สำหรับระยะ 11, 25, 40 กม.</p>
      </div>

      <div style="background: var(--brand-soft); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px;">
        <div style="font-weight: 700; color: #059669; margin-bottom: 4px;">🏅 เหรียญรางวัลที่ระลึก</div>
        <p style="font-size: 0.9rem; color: var(--text-secondary);">หล่อขึ้นรูปโลหะ 3D พรีเมียม พร้อมสายคล้องคอสำหรับผู้เข้าเส้นชัยทุกคน</p>
      </div>

      <div style="background: var(--brand-soft); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px;">
        <div style="font-weight: 700; color: #d97706; margin-bottom: 4px;">🎽 เสื้อผู้พิชิต Finisher</div>
        <p style="font-size: 0.9rem; color: var(--text-secondary);">มอบเป็นเกียรติยศพิเศษเฉพาะผู้เข้าเส้นชัยในระยะ 11, 25, และ 40 กม.</p>
      </div>
    </div>
  </div>

  <!-- หมวดรางวัล -->
  <div class="card-box">
    <h2 style="font-size: 1.45rem; font-weight: 800; margin-bottom: 15px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
      <span>🏆</span> รางวัลการแข่งขัน
    </h2>
    <img src="../assets/images/week.jpeg" alt="Prizes" class="section-img" onerror="this.onerror=null; this.src='../assets/images/week.jpeg';">
    <p style="font-size: 1.05rem; font-weight: 700; color: var(--brand); margin-bottom: 8px;">
      ถ้วยรางวัลเกียรติยศแบ่งตามกลุ่มอายุ และประเภทชาย/หญิง:
    </p>
    <ul style="padding-left: 20px; line-height: 2; font-size: 0.98rem; color: #334155;">
      <li><strong>ระยะ 40 กม. / 25 กม. / 11 กม. :</strong> ถ้วยรางวัลอันดับ 1-3 ชาย และ 1-3 หญิง แบ่งตามกลุ่มอายุ (อายุไม่เกิน 29 ปี, 30-39 ปี, 40-49 ปี, 50 ปีขึ้นไป)</li>
      <li><strong>ระยะ 5 กม. :</strong> ถ้วยรางวัล Fun Run อันดับ 1-5 ชาย และ 1-5 หญิง (อายุไม่เกิน 15 ปี และ 16 ปีขึ้นไป)</li>
      <li><strong>ถ้วยรางวัลพิเศษ :</strong> นักวิ่งแต่งกายแฟนซีโดนใจ และชมรมที่ส่งผู้เข้าร่วมมากที่สุด</li>
    </ul>
  </div>

  <!-- หมวดสถานที่และกำหนดการ -->
  <div class="card-box">
    <h2 style="font-size: 1.45rem; font-weight: 800; margin-bottom: 15px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
      <span>📍</span> สถานที่จัดงานและกำหนดการ
    </h2>
    <img src="../assets/images/week.jpeg" alt="Event Location" class="section-img" onerror="this.onerror=null; this.src='../assets/images/week.jpeg';">
    <div style="background: var(--brand-soft); border-radius: var(--radius-md); padding: 20px; border: 1px solid var(--border-color); font-size: 1rem; line-height: 1.85;">
      <div><strong>สถานที่:</strong> ณ วัดเขารังจันทร์ราม อำเภอเมือง จังหวัดนครสวรรค์</div>
      <div><strong>วันที่จัดงาน:</strong> วันอาทิตย์ที่ 22 พฤศจิกายน 2569 (2026)</div>
      <div><strong>เปิดรับสมัคร:</strong> วันนี้ จนถึงวันที่ 1 พฤศจิกายน 2569</div>
      <div><strong>รับอุปกรณ์ล่วงหน้า:</strong> วันเสาร์ที่ 21 พฤศจิกายน 2569 เวลา 10:00 - 18:00 น. ณ บริเวณจุดจัดกิจกรรม</div>
    </div>
  </div>

  <!-- ปุ่มสมัครล่างสุด -->
  <div class="card-box" style="text-align: center; padding: 40px 20px; background: linear-gradient(135deg, #fff 0%, var(--brand-soft) 100%); border: 1.5px solid rgba(249, 115, 22,0.2);">
    <h3 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 10px; color: var(--text-main);">
      พร้อมร่วมเป็นส่วนหนึ่งของ IT RUN 2026 แล้วหรือยัง?
    </h3>
    <p style="color: var(--text-secondary); margin-bottom: 25px; font-size: 1rem;">
      อย่ารอช้า จำนวนผู้เข้าแข่งขันมีจำกัดในแต่ละระยะทาง
    </p>
    <a href="../calendar.php" class="btn-primary" style="font-size: 1.2rem; padding: 15px 50px; box-shadow: 0 10px 25px var(--brand-glow);">
      👉 เลือกงานวิ่งและสมัครออนไลน์
    </a>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
