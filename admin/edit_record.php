<?php
// admin/edit_record.php
session_start();

require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$message = '';
$message_type = '';
$record_instance_id = $_GET['id'] ?? null;

if (!$record_instance_id) {
    redirect('manage_data.php?message=' . urlencode('لم يتم تحديد سجل للتعديل.') . '&type=error');
}

// جلب معلومات السجل والقالب
$record_info = null;
$stmt = $conn->prepare("SELECT ri.template_id, t.name as template_name FROM record_instances ri JOIN templates t ON ri.template_id = t.id WHERE ri.record_instance_id = ?");
$stmt->bind_param("s", $record_instance_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $record_info = $row;
} else {
    redirect('manage_data.php?message=' . urlencode('السجل غير موجود.') . '&type=error');
}
$stmt->close();

$template_id = $record_info['template_id'];
$template_name = $record_info['template_name'];

// جلب حقول القالب
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

// جلب القيم الحالية للسجل
$current_values = [];
$stmt = $conn->prepare("SELECT field_id, field_value FROM record_data WHERE record_instance_id = ?");
$stmt->bind_param("s", $record_instance_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $current_values[$row['field_id']] = $row['field_value'];
}
$stmt->close();

// معالجة تحديث السجل
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_record_submit'])) {
    $all_fields_valid = true;
    $conn->begin_transaction();

    try {
        foreach ($fields as $field) {
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

            // تحديث القيمة في record_data
            $stmt_update_data = $conn->prepare("UPDATE record_data SET field_value = ? WHERE record_instance_id = ? AND field_id = ?");
            $stmt_update_data->bind_param("ssi", $field_value, $record_instance_id, $field_id);
            if (!$stmt_update_data->execute()) {
                throw new Exception("خطأ في تحديث بيانات الحقل: " . $stmt_update_data->error);
            }
            $stmt_update_data->close();
        }

        if ($all_fields_valid) {
            $conn->commit();
            $message = "تم تحديث السجل بنجاح.";
            $message_type = "success";
            redirect("edit_record.php?id=" . urlencode($record_instance_id) . "&message=" . urlencode($message) . "&type=" . urlencode($message_type));
        } else {
            $conn->rollback();
        }
    } catch (Exception $e) {
        $conn->rollback();
        $message = "حدث خطأ: " . $e->getMessage();
        $message_type = "error";
    }
}

// تضمين ملف التصميم
require_once 'theme.php';
?>

<div class="container">
    <div class="header">
        <h1>تعديل سجل: <?php echo htmlspecialchars($record_instance_id); ?></h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="manage_data.php" class="logout-button">العودة لإدارة السجلات</a>
        </div>
    </div>

    <?php displayMessage(); // عرض رسائل النظام ?>

    <div class="content-section">
        <h2>تعديل بيانات السجل (القالب: <?php echo htmlspecialchars($template_name); ?>)</h2>
        <form action="edit_record.php?id=<?php echo htmlspecialchars($record_instance_id); ?>" method="POST">
            <?php foreach ($fields as $field): ?>
                <div class="form-group">
                    <label for="field_<?php echo $field['id']; ?>">
                        <?php echo htmlspecialchars($field['field_name']); ?>
                        <?php echo $field['is_required'] ? '<span style="color: red;">*</span>' : ''; ?>:
                    </label>
                    <?php
                    $input_type = 'text';
                    if ($field['field_type'] === 'number') {
                        $input_type = 'number';
                    } elseif ($field['field_type'] === 'email') {
                        $input_type = 'email';
                    } elseif ($field['field_type'] === 'date') {
                        $input_type = 'date';
                    }
                    $field_current_value = $current_values[$field['id']] ?? '';
                    ?>
                    <?php if ($field['field_type'] === 'textarea'): ?>
                        <textarea id="field_<?php echo $field['id']; ?>" name="field_<?php echo $field['id']; ?>" <?php echo $field['is_required'] ? 'required' : ''; ?>><?php echo htmlspecialchars($field_current_value); ?></textarea>
                    <?php else: ?>
                        <input type="<?php echo $input_type; ?>" id="field_<?php echo $field['id']; ?>" name="field_<?php echo $field['id']; ?>" value="<?php echo htmlspecialchars($field_current_value); ?>" <?php echo $field['is_required'] ? 'required' : ''; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="edit_record_submit" class="submit-button">حفظ التعديلات</button>
        </form>
    </div>
</div>
