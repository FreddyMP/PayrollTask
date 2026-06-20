<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$template = \App\Models\DocumentTemplate::first();
var_dump($template->file_path);
var_dump(\Storage::exists($template->file_path));
$content = \Storage::get($template->file_path);
var_dump(strlen($content));

$tempPath = storage_path('app/temp');
if (!file_exists($tempPath)) {
    mkdir($tempPath, 0755, true);
}
$tf = $tempPath . '/test.docx';
file_put_contents($tf, $content);
var_dump("File exists after put: " . (file_exists($tf) ? 'Yes' : 'No'));

$zip = new \ZipArchive();
var_dump("Open res: " . $zip->open($tf));
var_dump("Close res: " . $zip->close());
var_dump("File exists after zip close: " . (file_exists($tf) ? 'Yes' : 'No'));
