<?php
require 'C:/laragon/www/Project_Akhir/vendor/autoload.php';
require 'C:/laragon/www/Project_Akhir/app/Config/Paths.php';

$paths = new Config\Paths();
$app   = \Config\Services::codeigniter();
$app->initialize();

$rows = (new App\Models\LokasiModel())->getLokasiWithCount();

echo "=== keys first row ===\n" . json_encode(array_keys($rows[0] ?? [])) . "\n";
echo "=== rows (string keys only) ===\n";
foreach ($rows as $r) {
    echo json_encode(array_filter($r, 'is_string', ARRAY_FILTER_USE_KEY), JSON_UNESCAPED_UNICODE) . "\n";
}
echo "=== current view expr: array_column(lokasi,'total_barang') ===\n";
echo json_encode(array_map('intval', array_column($rows, 'total_barang'))) . "\n";
echo "=== needed: total_unit per lokasi ===\n";
echo json_encode(array_map('intval', array_column($rows, 'total_unit'))) . "\n";
