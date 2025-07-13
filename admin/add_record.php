<?php
ini_set('memory_limit', '256M'); // زيادة حد الذاكرة إلى 256 ميجابايت
ini_set('max_execution_time', 300); // زيادة وقت التنفيذ إلى 300 ثانية (5 دقائق)

// admin/add_record.php
// تم تضمين theme.php الذي يقوم ببدء الجلسة وتضمين functions.php و database.php
require_once 'theme.php';

// تفعيل عرض الأخطاء مؤقتًا لتشخيص مشكلة ERR_HTTP_RESPONSE_CODE_FAILURE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';
$message_type = '';
$selected_template_id = $_POST['template_id'] ?? null;
$templates = [];
$fields = [];

// جلب جميع القوالب لعرضها في قائمة الاختيار
$result = $conn->query("SELECT id, name FROM templates ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
}

// إذا تم اختيار قالب، جلب حقوله
if ($selected_template_id) {
    $stmt = $conn->prepare("SELECT id, field_name, field_type, is_required FROM fields WHERE template_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $selected_template_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row;
        }
    }
    $stmt->close();
}

// معالجة إضافة السجل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record_submit'])) {
    $template_id_to_save = $_POST['template_id'];
    $record_instance_id = generateUniqueId('rec_'); // توليد معرف فريد للسجل مع بادئة

    // جلب الحقول مرة أخرى للتأكد من صحتها
    $stmt = $conn->prepare("SELECT id, field_name, field_type, is_required FROM fields WHERE template_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $template_id_to_save);
    $stmt->execute();
    $fields_to_save = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $all_fields_valid = true;
    $conn->begin_transaction();

    try {
        foreach ($fields_to_save as $field) {
            $field_id = $field['id'];
            $field_name = $field['field_name'];
            $is_required = $field['is_required'];
            $field_value = $_POST['field_' . $field_id] ?? '';

            if ($is_required && empty($field_value)) {
                $all_fields_valid = false;
                $message = "الحقل '" . htmlspecialchars($field_name) . "' مطلوب ولا يمكن أن يكون فارغاً.";
                $message_type = "error";
                break;
            }

            $stmt_insert_data = $conn->prepare("INSERT INTO record_data (record_instance_id, field_id, field_value) VALUES (?, ?, ?)");
            $stmt_insert_data->bind_param("sis", $record_instance_id, $field_id, $field_value);
            if (!$stmt_insert_data->execute()) {
                throw new Exception("خطأ في حفظ بيانات الحقل: " . $stmt_insert_data->error);
            }
            $stmt_insert_data->close();
        }

        if ($all_fields_valid) {
            // حفظ معلومات السجل الأساسية
            $stmt_insert_instance = $conn->prepare("INSERT INTO record_instances (record_instance_id, template_id, created_at) VALUES (?, ?, NOW())");
            $stmt_insert_instance->bind_param("si", $record_instance_id, $template_id_to_save);
            if (!$stmt_insert_instance->execute()) {
                throw new Exception("خطأ في حفظ معلومات السجل: " . $stmt_insert_instance->error);
            }
            $stmt_insert_instance->close();

            $conn->commit();
            $message = "تم إضافة السجل بنجاح.";
            $message_type = "success";
            // إعادة توجيه لتجنب إعادة إرسال النموذج
            redirect("add_record.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
        } else {
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = "حدث خطأ: " . $e->getMessage();
        $message_type = "error";
    }
}

// نهاية theme.php
?>

<div class="container">
    <div class="header">
        <h1>إضافة سجل جديد</h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="manage_records_dashboard.php" class="logout-button">العودة لإدارة السجلات</a>
        </div>
    </div>

    <div class="content-section">
        <h2>اختيار القالب</h2>
        <form action="add_record.php" method="POST">
            <div class="form-group">
                <label for="template_select">اختر القالب:</label>
                <select id="template_select" name="template_id" onchange="this.form.submit()">
                    <option value="">-- اختر قالباً --</option>
                    <?php foreach ($templates as $template): ?>
                        <option value="<?php echo $template['id']; ?>" <?php echo ($selected_template_id == $template['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($template['name']); ?>
                        </option>
