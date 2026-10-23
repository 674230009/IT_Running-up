<?php
// includes/auth.php : ตัวช่วยจัดการ session และสิทธิ์ผู้ใช้งาน (admin / organizer)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_current_user()
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['admin_id'],
        'name' => $_SESSION['admin_name'] ?? 'ผู้ใช้งาน',
        'role' => $_SESSION['admin_role'] ?? 'organizer',
    ];
}

function auth_is_admin()
{
    $user = auth_current_user();
    return $user && $user['role'] === 'admin';
}

function auth_is_organizer()
{
    $user = auth_current_user();
    return $user && $user['role'] === 'organizer';
}

function auth_folder_prefix()
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if (strpos($script, '/admin/') !== false) {
        return '';
    }
    if (strpos($script, '/organizer/') !== false) {
        return '../admin/';
    }

    return 'admin/';
}

function auth_login_url()
{
    return auth_folder_prefix() . 'login.php';
}

function auth_dashboard_url($user = null)
{
    $user = $user ?? auth_current_user();
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $inAdmin = strpos($script, '/admin/') !== false;
    $inOrganizer = strpos($script, '/organizer/') !== false;

    if ($user && $user['role'] === 'admin') {
        if ($inAdmin) {
            return 'dashboard.php';
        }
        if ($inOrganizer) {
            return '../admin/dashboard.php';
        }
        return 'admin/dashboard.php';
    }

    if ($inOrganizer) {
        return 'dashboard.php';
    }
    if ($inAdmin) {
        return '../organizer/dashboard.php';
    }
    return 'organizer/dashboard.php';
}

function auth_require_login()
{
    $user = auth_current_user();
    if (!$user) {
        header('Location: ' . auth_login_url());
        exit();
    }

    return $user;
}

function auth_require_role(array $roles)
{
    $user = auth_require_login();
    if (!in_array($user['role'], $roles, true)) {
        header('Location: ' . auth_dashboard_url($user));
        exit();
    }

    return $user;
}

function auth_login_user(array $admin)
{
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_role'] = $admin['role'] ?? 'organizer';
}
