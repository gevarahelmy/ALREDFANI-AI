<?php
// admin/theme.php
// هذا الملف يوفر الهيكل الأساسي لصفحات لوحة التحكم (رأس HTML، القائمة الجانبية، بداية المحتوى)
// يجب أن يتم تضمينه في بداية كل صفحة إدارية.

// تأكد من بدء الجلسة وتضمين الدوال الأساسية
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';
require_once '../includes/functions.php';

// التحقق من تسجيل دخول المسؤول
if (!isAdminLoggedIn()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم ALREDFANI AI</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- تضمين ملف CSS الخاص باللوحة الإدارية -->
    <link rel="stylesheet" href="../admin/css/admin.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/alredfani_logo.png" alt="ALREDFANI AI Logo" class="sidebar-logo">
            <h3>ALREDFANI AI</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a></li>
            <li><a href="add_record.php"><i class="fas fa-plus-circle"></i> إضافة سجل</a></li>
            <li><a href="manage_data.php"><i class="fas fa-database"></i> إدارة السجلات</a></li>
            <li><a href="manage_templates.php"><i class="fas fa-layer-group"></i> إدارة القوالب</a></li>
            <li><a href="manage_fields.php"><i class="fas fa-list-alt"></i> إدارة الحقول</a></li>
            <li><a href="upload.php"><i class="fas fa-upload"></i> رفع ملفات</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- هنا سيتم عرض رسائل النظام -->
        <?php displayMessage(); ?>
