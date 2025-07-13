<?php
// includes/functions.php

// دالة لإعادة التوجيه (Redirect)
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// دالة للتحقق مما إذا كان المسؤول مسجلاً للدخول
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// دالة لتنسيق نتائج البحث كجدول HTML
function formatResultAsTable($data) {
    if (empty($data)) {
        return "<p class='no-results'>لا توجد نتائج لعرضها.</p>";
    }

    $html = '<table class="results-table">';
    $html .= '<thead><tr>';
    // رؤوس الأعمدة (افتراضية أو من البيانات)
    $headers = array_keys($data[0]);
    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    $html .= '</tr></thead>';
    $html .= '<tbody>';
    foreach ($data as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

// دالة لاستخلاص النص من أنواع مختلفة من الملفات
function extractTextFromFile($filePath, $fileType) {
    $extractedText = '';
    // المسار الكامل للملف على الخادم، مع الأخذ في الاعتبار أن functions.php في includes/
    $fullFilePath = __DIR__ . '/../' . $filePath;

    if (!file_exists($fullFilePath)) {
        error_log("File not found for extraction: " . $fullFilePath);
        return "خطأ: الملف غير موجود للاستخلاص.";
    }

    switch ($fileType) {
        case 'text/plain':
        case 'text/csv':
        case 'application/json':
            $extractedText = file_get_contents($fullFilePath);
            break;
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // .xlsx
            // **التعديل هنا: استخدام مسار مطلق لضمان التضمين**
            // **وإضافة تحقق إضافي**
            $simplexlsx_path = __DIR__ . '/../libraries/simplexlsx/SimpleXLSX.php';
            $simplexlsxex_path = __DIR__ . '/../libraries/simplexlsx/SimpleXLSXEx.php';

            if (file_exists($simplexlsx_path)) {
                require_once $simplexlsx_path;
            } else {
                error_log("SimpleXLSX.php not found at: " . $simplexlsx_path);
                return "خطأ: ملف SimpleXLSX.php غير موجود.";
            }

            if (file_exists($simplexlsxex_path)) {
                require_once $simplexlsxex_path;
            } else {
                error_log("SimpleXLSXEx.php not found at: " . $simplexlsxex_path);
                // ليس بالضرورة أن يكون خطأ فادحًا إذا لم يكن SimpleXLSXEx.php موجودًا
                // ولكن من الأفضل تضمينه إذا كان متاحًا
            }


            if (class_exists('Shuchkin\SimpleXLSX')) {
                try {
                    if ($xlsx = Shuchkin\SimpleXLSX::parse($fullFilePath)) {
                        $rows = $xlsx->rows();
                        if (!empty($rows)) {
                            // استخلاص البيانات كـ "مفتاح: قيمة"
                            $header = array_shift($rows); // الصف الأول كرؤوس
                            $extractedData = [];
                            foreach ($rows as $row) {
                                $rowData = [];
                                foreach ($row as $key => $value) {
                                    if (isset($header[$key])) {
                                        $rowData[] = htmlspecialchars($header[$key]) . ': ' . htmlspecialchars($value);
                                    } else {
                                        $rowData[] = htmlspecialchars($value); // إذا لم يكن هناك رأس
                                    }
                                }
                                $extractedData[] = implode(' | ', $rowData);
                            }
                            $extractedText = implode("\n", $extractedData);
                        } else {
                            $extractedText = "ملف Excel فارغ أو لا يحتوي على بيانات.";
                        }
                    } else {
                        $extractedText = "خطأ في تحليل ملف Excel: " . Shuchkin\SimpleXLSX::parseError();
                        error_log("SimpleXLSX Error: " . Shuchkin\SimpleXLSX::parseError());
                    }
                } catch (Exception $e) {
                    $extractedText = "خطأ غير متوقع أثناء تحليل ملف Excel: " . $e->getMessage();
                    error_log("Exception in SimpleXLSX: " . $e->getMessage());
                }
            } else {
                $extractedText = "خطأ: مكتبة SimpleXLSX غير موجودة أو لم يتم تحميلها بشكل صحيح.";
                error_log("SimpleXLSX class not found after require_once.");
            }
            break;
        // يمكنك إضافة حالات أخرى لأنواع ملفات مثل PDF أو DOCX هنا
        default:
            $extractedText = "نوع الملف غير مدعوم للاستخلاص: " . $fileType;
            break;
    }
    return $extractedText;
}

// دالة لتنظيف النص للاستخدام في البحث (إزالة علامات الترقيم، تحويل إلى أحرف صغيرة، إلخ)
function cleanTextForSearch($text) {
    $text = mb_strtolower($text, 'UTF-8'); // تحويل إلى أحرف صغيرة
    $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text); // إزالة علامات الترقيم والرموز (مع دعم Unicode)
    $text = preg_replace('/\s+/', ' ', $text); // استبدال مسافات متعددة بمسافة واحدة
    return trim($text);
}

// دالة لتحديد نوع MIME للملف
function getMimeType($filePath) {
    // يجب أن يكون المسار كاملاً للملف على الخادم
    if (!file_exists($filePath)) {
        error_log("File not found for MIME type detection: " . $filePath);
        return false; // أو رمي استثناء
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    return $mimeType;
}

// دالة لتنسيق رسائل النظام
function displayMessage() {
    if (isset($_GET['message']) && isset($_GET['type'])) {
        $message = htmlspecialchars($_GET['message']);
        $type = htmlspecialchars($_GET['type']);
        echo "<div class='message $type'>$message</div>";
    }
}

// دالة لتوليد معرف فريد (UUID v4)
function generateUniqueId($prefix = '') {
    // UUID v4
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return $prefix . vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
?>
