<?php
// admin/dashboard.php
session_start(); // بدء الجلسة

require_once '../config/database.php';
require_once '../includes/functions.php';

// التحقق مما إذا كان المسؤول مسجلاً للدخول
if (!isAdminLoggedIn()) {
    redirect('login.php'); // إعادة التوجيه إلى صفحة تسجيل الدخول إذا لم يكن مسجلاً
}

// اسم المستخدم من الجلسة
$username = $_SESSION['username'] ?? 'المسؤول';

// استدعاء ملف theme.php لتضمين التصميم
require_once 'theme.php';
?>

<div class="container">
    <div class="header">
        <h1>لوحة تحكم المسؤول</h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($username); ?>!</span>
            <a href="logout.php" class="logout-button">تسجيل الخروج</a>
        </div>
    </div>

    <div class="content-section">
        <h2>إدارة الملفات والبيانات</h2>
        <div class="button-group">
            <a href="upload.php" class="action-button primary">
                <i class="fas fa-upload"></i> رفع وإدارة الملفات
            </a>
            <a href="manage_records_dashboard.php" class="action-button secondary">
                <i class="fas fa-database"></i> إدارة السجلات اليدوية
            </a>
        </div>
    </div>

    <!-- يمكن إضافة المزيد من الأقسام هنا في المستقبل -->

</div>

<?php
// تضمين الفوتر (إذا كان لديك ملف فوتر منفصل)
// require_once '../includes/footer.php';
?>
