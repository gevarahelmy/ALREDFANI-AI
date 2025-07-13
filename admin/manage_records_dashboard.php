<?php
// admin/manage_records_dashboard.php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// تضمين ملف التصميم
require_once 'theme.php';
?>

<div class="container">
    <div class="header">
        <h1>إدارة السجلات اليدوية</h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="dashboard.php" class="logout-button">العودة للوحة التحكم الرئيسية</a>
        </div>
    </div>

    <div class="content-section">
        <h2>خيارات إدارة السجلات</h2>
        <div class="button-group">
            <a href="manage_templates.php" class="action-button primary">
                <i class="fas fa-file-alt"></i> إدارة القوالب
            </a>
            <a href="manage_fields.php" class="action-button secondary">
                <i class="fas fa-list-alt"></i> إدارة الحقول
            </a>
            <a href="add_record.php" class="action-button info">
                <i class="fas fa-plus-circle"></i> إضافة سجل جديد
            </a>
            <a href="manage_data.php" class="action-button warning">
                <i class="fas fa-table"></i> عرض وإدارة السجلات
            </a>
        </div>
    </div>
</div>
