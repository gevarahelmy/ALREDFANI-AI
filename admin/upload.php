<?php
ini_set('memory_limit', '256M'); // زيادة حد الذاكرة إلى 256 ميجابايت
ini_set('max_execution_time', 300); // زيادة وقت التنفيذ إلى 300 ثانية (5 دقائق)

// admin/upload.php
ob_start(); // ابدأ التقاط المخرجات لمنع مشكلة Headers already sent
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// معالجة رفع الملف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload'])) {
    $file_name = $_FILES['file_upload']['name'];
    $file_tmp_name = $_FILES['file_upload']['tmp_name'];
    $file_size = $_FILES['file_upload']['size'];
    $file_error = $_FILES['file
