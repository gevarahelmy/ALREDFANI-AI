<?php
// admin/manage_fields.php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';
$template_id = $_GET['template_id'] ?? null;

if (!$template_id) {
    redirect('manage_templates.php?message=' . urlencode('الرجاء اختيار قالب أولاً.') . '&type=error');
}

// جلب اسم القالب
$template_name = '';
$stmt = $conn->prepare("SELECT name FROM templates WHERE id = ?");
$stmt->bind_param("i", $template_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $template_name = $row['name'];
} else {
    redirect('manage_templates.php?message=' . urlencode('القالب غير موجود.') . '&type=error');
}
$stmt->close();

// معالجة إضافة حقل جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['field_name'])) {
    $field_name = trim($_POST['field_name']);
    $field_type = $_POST['field_type'] ?? 'text'; // نوع الحقل الافتراضي
    $is_required = isset($_POST['is_required']) ? 1 : 0;

    if (!empty($field_name)) {
        $stmt = $conn->prepare("INSERT INTO fields (template_id, field_name, field_type, is_required) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $template_id, $field_name, $field_type, $is_required);
        if ($stmt->execute()) {
            $message = "تم إضافة الحقل بنجاح.";
            $message_type = "success";
        } else {
            $message = "خطأ: " . $stmt->error;
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "اسم الحقل لا يمكن أن يكون فارغاً.";
        $message_type = "error";
    }
    redirect("manage_fields.php?template_id=" . $template_id . "&message=" . urlencode($message) . "&type=" . urlencode($message_type));
}

// معالجة حذف حقل
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $field_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM fields WHERE id = ? AND template_id = ?");
    $stmt->bind_param("ii", $field_id, $template_id);
    if ($stmt->execute()) {
        $message = "تم حذف الحقل بنجاح.";
        $message_type = "success";
    } else {
        $message = "خطأ أثناء حذف الحقل: " . $stmt->error;
        $message_type = "error";
    }
    $stmt->close();
    redirect("manage_fields.php?template_id=" . $template_id . "&message=" . urlencode($message) . "&type=" . urlencode($message_type));
}

// جلب جميع الحقول لهذا القالب
$fields = [];
$stmt = $conn->prepare("SELECT id, field_name, field_type, is_required FROM fields WHERE template_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $template_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fields[] = $row;
    }
}
$stmt->close();

// تضمين ملف التصميم
require_once 'theme.php';
?>

<div class="container">
    <div class="header">
        <h1>إدارة حقول القالب: <?php echo htmlspecialchars($template_name); ?></h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="manage_templates.php" class="logout-button">العودة للقوالب</a>
        </div>
    </div>

    <?php displayMessage(); // عرض رسائل النظام ?>

    <div class="content-section">
        <h2>إضافة حقل جديد</h2>
        <form action="manage_fields.php?template_id=<?php echo $template_id; ?>" method="POST">
            <div class="form-group">
                <label for="field_name">اسم الحقل:</label>
                <input type="text" id="field_name" name="field_name" required>
            </div>
            <div class="form-group">
                <label for="field_type">نوع الحقل:</label>
                <select id="field_type" name="field_type">
                    <option value="text">نص</option>
                    <option value="number">رقم</option>
                    <option value="email">بريد إلكتروني</option>
                    <option value="date">تاريخ</option>
                    <option value="textarea">نص طويل</option>
                </select>
            </div>
            <div class="form-group">
                <input type="checkbox" id="is_required" name="is_required" value="1">
                <label for="is_required" style="display: inline-block; margin-right: 10px;">مطلوب</label>
            </div>
            <button type="submit" class="submit-button">إضافة حقل</button>
        </form>
    </div>

    <div class="content-section">
        <h2>الحقول الموجودة</h2>
        <?php if (empty($fields)): ?>
            <p>لا توجد حقول مدخلة لهذا القالب بعد.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>اسم الحقل</th>
                        <th>نوع الحقل</th>
                        <th>مطلوب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fields as $field): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($field['id']); ?></td>
                            <td><?php echo htmlspecialchars($field['field_name']); ?></td>
                            <td><?php echo htmlspecialchars($field['field_type']); ?></td>
                            <td><?php echo $field['is_required'] ? 'نعم' : 'لا'; ?></td>
                            <td class="actions">
                                <a href="manage_fields.php?action=delete&id=<?php echo $field['id']; ?>&template_id=<?php echo $template_id; ?>" class="action-button danger" onclick="return confirm('هل أنت متأكد من حذف هذا الحقل؟');">
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
