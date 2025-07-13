<?php
// admin/manage_data.php
// تم تضمين theme.php الذي يقوم ببدء الجلسة وتضمين functions.php و database.php
require_once 'theme.php';

$message = '';
$message_type = '';

// معالجة حذف سجل
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $record_instance_id = $_GET['id'];

    $conn->begin_transaction();
    try {
        // حذف البيانات المرتبطة بالسجل
        $stmt = $conn->prepare("DELETE FROM record_data WHERE record_instance_id = ?");
        $stmt->bind_param("s", $record_instance_id);
        if (!$stmt->execute()) {
            throw new Exception("خطأ في حذف بيانات السجل: " . $stmt->error);
        }
        $stmt->close();

        // حذف السجل نفسه
        $stmt = $conn->prepare("DELETE FROM record_instances WHERE record_instance_id = ?");
        $stmt->bind_param("s", $record_instance_id);
        if (!$stmt->execute()) {
            throw new Exception("خطأ في حذف السجل الرئيسي: " . $stmt->error);
        }
        $stmt->close();

        $conn->commit();
        $message = "تم حذف السجل بنجاح.";
        $message_type = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "حدث خطأ أثناء حذف السجل: " . $e->getMessage();
        $message_type = "error";
    }
    redirect("manage_data.php?message=" . urlencode($message) . "&type=" . urlencode($message_type));
}

// جلب جميع السجلات لعرضها
$records = [];
$result = $conn->query("SELECT ri.record_instance_id, t.name as template_name, ri.created_at FROM record_instances ri JOIN templates t ON ri.template_id = t.id ORDER BY ri.created_at DESC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $record_instance_id = $row['record_instance_id'];
        $record_data = [];

        // جلب بيانات كل حقل للسجل
        $stmt_data = $conn->prepare("SELECT f.field_name, rd.field_value FROM record_data rd JOIN fields f ON rd.field_id = f.id WHERE rd.record_instance_id = ? ORDER BY f.id ASC");
        $stmt_data->bind_param("s", $record_instance_id);
        $stmt_data->execute();
        $data_result = $stmt_data->get_result();
        while ($data_row = $data_result->fetch_assoc()) {
            $record_data[] = $data_row;
        }
        $stmt_data->close();

        $row['data'] = $record_data;
        $records[] = $row;
    }
}

// نهاية theme.php
?>

<div class="container">
    <div class="header">
        <h1>عرض وإدارة السجلات</h1>
        <div class="user-info">
            <span>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? 'المسؤول'); ?>!</span>
            <a href="manage_records_dashboard.php" class="logout-button">العودة لإدارة السجلات</a>
        </div>
    </div>

    <div class="content-section">
        <h2>السجلات الموجودة</h2>
        <?php if (empty($records)): ?>
            <p>لا توجد سجلات مدخلة بعد.</p>
        <?php else: ?>
            <?php foreach ($records as $record): ?>
                <div class="record-card">
                    <h3>القالب: <?php echo htmlspecialchars($record['template_name']); ?></h3>
                    <p><strong>معرف السجل:</strong> <?php echo htmlspecialchars($record['record_instance_id']); ?></p>
                    <p><strong>تاريخ الإنشاء:</strong> <?php echo htmlspecialchars($record['created_at']); ?></p>
                    <table class="data-table" style="width: auto; margin-top: 10px;">
                        <thead>
                            <tr>
                                <th>الحقل</th>
                                <th>القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($record['data'] as $field_data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($field_data['field_name']); ?></td>
                                    <td><?php echo htmlspecialchars($field_data['field_value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="actions" style="margin-top: 15px; justify-content: flex-start;">
                        <a href="edit_record.php?id=<?php echo htmlspecialchars($record['record_instance_id']); ?>" class="action-button primary">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="manage_data.php?action=delete&id=<?php echo htmlspecialchars($record['record_instance_id']); ?>" class="action-button danger" onclick="return confirm('هل أنت متأكد من حذف هذا السجل؟');">
                            <i class="fas fa-trash"></i> حذف
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- لا يوجد تضمين لـ sidebar.php هنا -->
<!-- تم نقل الستايلات الخاصة بـ record-card إلى admin.css -->
