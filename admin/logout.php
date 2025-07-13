<?php
// admin/logout.php
session_start(); // بدء الجلسة

// تدمير جميع متغيرات الجلسة
$_SESSION = array();

// إذا كان سيتم استخدام ملفات تعريف الارتباط (الكوكيز)، فقم بحذف ملف تعريف الارتباط الخاص بالجلسة.
// هذا سيتلف ملف تعريف الارتباط الخاص بالجلسة، بدلاً من مجرد انتهاء صلاحيته.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit();
?>
