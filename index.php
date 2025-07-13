<?php
// public/index.php
session_start();

require_once 'config/database.php';
require_once 'includes/functions.php';

$query_results = [];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $user_query = trim($_POST['query']);
    $auth_key_input = $_POST['auth_key'] ?? '';
    $auth_value_input = $_POST['auth_value'] ?? '';

    if (empty($user_query)) {
        $message = "الرجاء إدخال استعلام للبحث.";
        $message_type = "error";
    } else {
        // البحث في الملفات المرفوعة
        // تأكد أن عمود 'extracted_text' موجود في جدول 'files' وممتلئ بالبيانات
        $stmt_files = $conn->prepare("SELECT id, file_name, extracted_text, file_type FROM files WHERE extracted_text LIKE ?");
        $search_term = '%' . $user_query . '%';
        $stmt_files->bind_param("s", $search_term);
        $stmt_files->execute();
        $files_result = $stmt_files->get_result();

        while ($file_row = $files_result->fetch_assoc()) {
            // هنا يمكن تطبيق منطق الحماية للملفات إذا كان هناك
            // حاليا، لا يوجد حماية مطبقة على الملفات المرفوعة في هذا الكود
            $query_results[] = [
                'source' => 'file',
                'file_name' => $file_row['file_name'],
                'content' => $file_row['extracted_text'],
                'type' => $file_row['file_type']
            ];
        }
        $stmt_files->close();

        // البحث في السجلات اليدوية
        // 1. البحث عن record_instance_id بناءً على قيمة الحقل
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
            // 2. جلب جميع بيانات الحقول للسجلات المطابقة
            $placeholders = implode(',', array_fill(0, count($matching_record_instance_ids), '?'));
            // تأكد من أن عدد الـ placeholders يطابق عدد الـ parameters في bind_param
            $types = str_repeat('s', count($matching_record_instance_ids)); // كل المعرفات هي سلاسل نصية
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
                // هنا يمكن تطبيق منطق الحماية للسجلات اليدوية
                // حاليا، لا يوجد حماية مطبقة على السجلات اليدوية في هذا الكود
                $query_results[] = [
                    'source' => 'manual_record',
                    'record_instance_id' => $instance_id,
                    'template_name' => $record_data['template_name'],
                    'data' => $record_data['data']
                ];
            }
        }

        if (empty($query_results)) {
            $message = "لم يتم العثور على معلومات ذات صلة باستعلامك.";
            $message_type = "info";
        } else {
            $message = "تم العثور على " . count($query_results) . " نتيجة.";
            $message_type = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALREDFANI AI - استعلام وتحليل البيانات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            direction: rtl;
            text-align: right;
            padding: 20px;
            box-sizing: border-box;
        }
        .main-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            text-align: center;
        }
        .main-container h1 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: bold;
        }
        .main-container .logo {
            margin-bottom: 30px;
        }
        .main-container .logo img {
            max-width: 250px; /* حجم الشعار */
            height: auto;
            margin: 0 auto 30px auto; /* توسيط الشعار */
            display: block; /* لضمان عمل margin auto */
        }
        .search-form {
            margin-bottom: 30px;
        }
        .search-form input[type="text"] {
            width: calc(100% - 120px);
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease;
        }
        .search-form input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }
        .search-form button {
            width: 100px;
            padding: 12px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-right: 10px; /* Space between input and button */
        }
        .search-form button:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .auth-fields {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .auth-fields input {
            width: 180px;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }
        .results-section {
            margin-top: 30px;
            text-align: right;
        }
        .results-section h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: inline-block;
        }
        .result-item {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: right;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .result-item h3 {
            color: #333;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 20px;
        }
        .result-item p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
        }
        .result-item .source-tag {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .result-item .file-content {
            white-space: pre-wrap; /* Preserves whitespace and wraps text */
            word-wrap: break-word; /* Breaks long words */
            max-height: 200px; /* Limit height */
            overflow-y: auto; /* Add scroll if content overflows */
            border: 1px solid #eee;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            margin-top: 10px;
        }
        .message {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Table for structured data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table th,
        .data-table td {
            padding: 10px 15px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        .data-table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        .data-table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo">
            <img src="assets/images/alredfani_logo.png" alt="ALREDFANI AI Logo">
        </div>
        <h1>منصة ذكية للاستعلام والتحليل التلقائي للبيانات</h1>

        <?php displayMessage(); // عرض رسائل النظام ?>

        <form action="index.php" method="POST" class="search-form">
            <input type="text" name="query" placeholder="اسأل عن أي معلومة..." value="<?php echo htmlspecialchars($_POST['query'] ?? ''); ?>" required>
            <button type="submit">بحث</button>
            <!--
            <div class="auth-fields">
                <input type="text" name="auth_key" placeholder="مفتاح التحقق (اختياري)">
                <input type="password" name="auth_value" placeholder="قيمة التحقق (اختياري)">
            </div>
            -->
        </form>

        <?php if (!empty($query_results)): ?>
            <div class="results-section">
                <h2>نتائج البحث</h2>
                <?php foreach ($query_results as $result): ?>
                    <div class="result-item">
                        <?php if ($result['source'] === 'file'): ?>
                            <span class="source-tag">ملف مرفوع</span>
                            <h3>الملف: <?php echo htmlspecialchars($result['file_name']); ?> (<?php echo htmlspecialchars($result['type']); ?>)</h3>
                            <p><strong>المحتوى المستخلص:</strong></p>
                            <div class="file-content"><?php echo htmlspecialchars($result['content']); ?></div>
                        <?php elseif ($result['source'] === 'manual_record'): ?>
                            <span class="source-tag">سجل يدوي</span>
                            <h3>القالب: <?php echo htmlspecialchars($result['template_name']); ?> (معرف السجل: <?php echo htmlspecialchars($result['record_instance_id']); ?>)</h3>
                            <p><strong>البيانات:</strong></p>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>الحقل</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result['data'] as $field_data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($field_data['field_name']); ?></td>
                                            <td><?php echo htmlspecialchars($field_data['field_value']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
