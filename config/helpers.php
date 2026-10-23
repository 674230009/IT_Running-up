<?php
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function normalize_competitor($row)
{
    if (!is_array($row) || !$row) {
        return $row;
    }

    $map = [
        'Competitor_id' => 'competitor_id',
        'Competitor_prefix' => 'competitor_prefix',
        'Competitor_firstname' => 'competitor_firstname',
        'Competitor_lastname' => 'competitor_lastname',
        'Competitor_email' => 'competitor_email',
        'Competitor_phone' => 'competitor_phone',
    ];

    foreach ($map as $source => $target) {
        if (!array_key_exists($target, $row) && array_key_exists($source, $row)) {
            $row[$target] = $row[$source];
        }
    }

    return $row;
}

function normalize_competitors(array $rows)
{
    return array_map('normalize_competitor', $rows);
}

function thai_date($date)
{
    if (!$date) {
        return '-';
    }

    $time = strtotime($date);
    if (!$time) {
        return '-';
    }

    $months = [
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม',
    ];

    return date('j', $time) . ' ' . $months[(int) date('n', $time)] . ' ' . (date('Y', $time) + 543);
}

function event_banner($banner)
{
    $banner = trim((string) $banner);
    $root = dirname(__DIR__);

    if ($banner !== '') {
        if (is_file($root . '/assets/images/events/' . $banner)) {
            return 'assets/images/events/' . $banner;
        }
        if (is_file($root . '/assets/images/' . $banner)) {
            return 'assets/images/' . $banner;
        }
    }

    return 'assets/images/week.jpeg';
}

function event_distances($conn, $eventId)
{
    $stmt = $conn->prepare("SELECT distance_label, price FROM event_distances WHERE event_id = :event_id ORDER BY price ASC");
    $stmt->execute([':event_id' => $eventId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function event_status_label($status)
{
    switch ($status) {
        case 'open':
            return 'เปิดรับสมัคร';
        case 'closed':
            return 'ปิดรับสมัคร';
        default:
            return 'เร็วๆ นี้';
    }
}

function bib_prefix($distanceType)
{
    $distanceType = (string) $distanceType;

    if (strpos($distanceType, '40') !== false || strpos($distanceType, '42') !== false) {
        return 'F';
    }
    if (strpos($distanceType, '25') !== false) {
        return 'H';
    }
    if (strpos($distanceType, '10.5') !== false || strpos($distanceType, '11') !== false) {
        return 'M';
    }

    return 'R';
}
