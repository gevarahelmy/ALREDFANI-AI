<?php
// api/query.php
// هذا الملف هو نقطة النهاية (Endpoint) لتطبيق الجوال

// تحديد نوع المحتوى لضمان أن التطبيق يفهم الرد بشكل صحيح
header('Content-Type: application/json; charset=utf-8');

// المسارات الصحيحة بناءً على الهيكل الجديد
require_once '../config/database.php';
require_once '../includes/functions.php';

// استلام البيانات من التطبيق (عادة تكون بصيغة JSON في body الطلب)
$input = json_decode(file_get_contents('php://input'), true);

// إذا لم يتم إرسال بيانات JSON، حاول البحث في متغيرات POST العادية (للتوافقية والاختبار)
if (empty($input)) {
    $input = $_POST;
}

$action = $input['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid or missing action.'];

// --- البحث عن البيانات ---
if ($action === 'search') {
    $user_query = trim($input['query'] ?? '');

    if (empty($user_query)) {
        $response = ['status' => 'error', 'message' => 'Query is empty.'];
    } else {
        // البحث في الملفات المرفوعة
        $stmt_files = $conn->prepare("SELECT id, file_name, extracted_text, file_type FROM files WHERE extracted_text LIKE ?");
        $search_term = '%' . $user_query . '%';
        $stmt_files->bind_param("s", $search_term);
        $stmt_files->execute();
        $files_result = $stmt_files->get_result();
        
        $query_results = [];

        while ($file_row = $files_result->fetch_assoc()) {
            $query_results[] = [
                'source' => 'file',
                'file_name' => $file_row['file_name'],
                'content' => mb_substr($file_row['extracted_text'], 0, 200) . '...', // إرجاع مقتطف فقط
                'type' => $file_row['file_type']
            ];
        }
        $stmt_files->close();

        // البحث في السجلات اليدوية (نفس الكود من index.php)
        $matching_record_instance_ids = [];
        $stmt_records = $conn->prepare("SELECT DISTINCT record_instance_id FROM record_data WHERE field_value LIKE ?");
        $stmt_records->bind_param("s", $search_term);
        $stmt_records->execute();
        $records_result = $stmt_records->get_result();
        while ($record_row = $records_result->fetch_assoc()) {
            $matching_record_instance_ids[] = $record_row['record_instance_id'];
        }
        $stmt_records->close();

        if (!empty($matching_record_instance_ids)) {
            $placeholders = implode(',', array_fill(0, count($matching_record_instance_ids), '?'));
            $types = str_repeat('s', count($matching_record_instance_ids));
            $stmt_full_records = $conn->prepare("SELECT rd.record_instance_id, f.field_name, rd.field_value, t.name as template_name
                                                FROM record_data rd
                                                JOIN fields f ON rd.field_id = f.id
                                                JOIN record_instances ri ON rd.record_instance_id = ri.record_instance_id
                                                JOIN templates t ON ri.template_id = t.id
                                                WHERE rd.record_instance_id IN ($placeholders)
                                                ORDER BY rd.record_instance_id, f.id");
            $stmt_full_records->bind_param($types, ...$matching_record_instance_ids);
            $stmt_full_records->execute();
            $full_records_result = $stmt_full_records->get_result();

            $grouped_records = [];
            while ($row = $full_records_result->fetch_assoc()) {
                $grouped_records[$row['record_instance_id']]['template_name'] = $row['template_name'];
                $grouped_records[$row['record_instance_id']]['data'][] = [
                    'field_name' => $row['field_name'],
                    'field_value' => $row['field_value']
                ];
            }
            $stmt_full_records->close();

            foreach ($grouped_records as $instance_id => $record_data) {
                $query_results[] = [
                    'source' => 'manual_record',
                    'template_name' => $record_data['template_name'],
                    'data' => $record_data['data']
                ];
            }
        }

        if (empty($query_results)) {
            $response = ['status' => 'not_found', 'message' => 'لم يتم العثور على معلومات ذات صلة.'];
        } else {
            $response = ['status' => 'success', 'results' => $query_results];
        }
    }
}

// يمكنك إضافة حالات أخرى مثل 'authenticate' هنا بنفس الطريقة

// إرسال الرد النهائي بصيغة JSON
// JSON_UNESCAPED_UNICODE مهم جدًا لدعم اللغة العربية
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>
