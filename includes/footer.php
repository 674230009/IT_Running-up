<?php
// includes/footer.php : ส่วนท้ายเว็บไซต์ IT RUN 2026
$base = $base_url ?? '';
?>
<footer class="site-footer">
  <div style="max-width: 1080px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px;">
    <div style="text-align: left; max-width: 460px;">
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
        <span style="background: var(--brand-gradient); color: #fff; padding: 4px 10px; border-radius: var(--radius-sm); font-weight: 900; font-size: 0.95rem; box-shadow: 0 4px 10px var(--brand-glow);">
          IT RUN 2026
        </span>
        <strong style="color: var(--text-main); font-size: 1.05rem;">ก้าวด้วยใจ สานสายใยไอทีสัมพันธ์</strong>
      </div>
      <p style="font-size: 0.88rem; color: var(--text-secondary); line-height: 1.7;">
        📍 วัดเขารังจันทร์ราม อำเภอเมือง จังหวัดนครสวรรค์<br>
        📅 วันแข่งขัน: วันอาทิตย์ที่ 22 พฤศจิกายน 2569 (2026)
      </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; text-align: left;">
      <span style="font-weight: 800; color: var(--text-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.8px;">เมนูด่วน</span>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 22px;">
        <a href="<?= $base ?>index.php" style="color: var(--text-secondary); text-decoration: none;">หน้าหลัก</a>
        <a href="<?= $base ?>calendar.php" style="color: var(--text-secondary); text-decoration: none;">ปฏิทินงานวิ่ง</a>
        <a href="<?= $base ?>pages/details.php" style="color: var(--text-secondary); text-decoration: none;">รายละเอียดงาน</a>
        <a href="<?= $base ?>pages/status.php" style="color: var(--text-secondary); text-decoration: none;">ตรวจสอบสถานะ/BIB</a>
        <a href="<?= $base ?>pages/register.php" style="color: var(--brand); font-weight: 700; text-decoration: none;">สมัครวิ่งออนไลน์</a>
        <a href="<?= $base ?>portal.php" style="color: var(--brand-ink); font-weight: 700; text-decoration: none;">ผู้จัด / แอดมิน</a>
      </div>
    </div>
  </div>

  <div style="border-top: 1px solid var(--border-color); margin-top: 28px; padding-top: 20px; text-align: center; font-size: 0.82rem; color: var(--text-muted);">
    &copy; 2026 IT RUN PLATFORM — สาขาวิชาเทคโนโลยีสารสนเทศ. สงวนลิขสิทธิ์ทุกประการ.
  </div>
</footer>

</body>
</html>