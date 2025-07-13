<?php
// admin/manage_templates.php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';

// معالجة إضافة قالب جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_name'])) {
    $template_name = trim($_POST['template_name']);
    if (!empty($template_name)) {
        $stmt = $conn->prepare("INSERT INTO templates (name) VALUES (?)");
        $stmt->bind_param("s", $template_name);
        if ($stmt->execute()) {
            $message = "تم إضافة القالب بنجاح.";
            $message_type = "success";
        } else {
            $message = "خطأ: " . $stmt->error;
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "اسم القالب لا يمكن أن يكون فارغاً.";
        $message_type = "error";
    }
    redirect("manage_templates.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
}

// معالجة حذف قالب
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $template_id = $_GET['id'];

    // يجب حذف الحقول المرتبطة بهذا القالب أولاً
    $stmt = $conn->prepare("DELETE FROM fields WHERE template_id = ?");
    $stmt->bind_param("i", $template_id);
    $stmt->execute();
    $stmt->close();

    // ثم حذف القالب نفسه
    $stmt = $conn->prepare("DELETE FROM templates WHERE id = ?");
    $stmt->bind_param("i", $template_id);
    if ($stmt->execute()) {
        $message = "تم حذف القالب وجميع حقوله بنجاح.";
        $message_type = "success";
    } else {
        $message = "خطأ أثناء حذف القالب: " . $stmt->error;
        $message_type = "error";
    }
    $stmt->close();
    redirect("manage_templates.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
}

// جلب جميع القوالب لعرضها
$templates = [];
$result = $conn->query("SELECT id, name FROM templates ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
}

// تضمين ملف التصميم
require_once 'theme.php';
?>

<div class="container">
    <div class="header">
        <h1>إدارة القوالب</h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="manage_records_dashboard.php" class="logout-button">العودة لإدارة السجلات</a>
        </div>
    </div>

    <?php displayMessage(); // عرض رسائل النظام ?>

    <div class="content-section">
        <h2>إضافة قالب جديد</h2>
        <form action="manage_templates.php" method="POST">
            <div class="form-group">
                <label for="template_name">اسم القالب:</label>
                <input type="text" id="template_name" name="template_name" required>
            </div>
            <button type="submit" class="submit-button">إضافة قالب</button>
        </form>
    </div>

    <div class="content-section">
        <h2>القوالب الموجودة</h2>
        <?php if (empty($templates)): ?>
            <p>لا توجد قوالب مدخلة بعد.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>اسم القالب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $template): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($template['id']); ?></td>
                            <td><?php echo htmlspecialchars($template['name']); ?></td>
                            <td class="actions">
                                <a href="manage_fields.php?template_id=<?php echo $template['id']; ?>" class="action-button info">
                                    <i class="fas fa-edit"></i> إدارة الحقول
                                </a>
                                <a href="manage_templates.php?action=delete&id=<?php echo $template['id']; ?>" class="action-button danger" onclick="return confirm('هل أنت متأكد من حذف هذا القالب وجميع حقوله؟');">
                                    <i class="fas fa-trash"></i> حذف
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
