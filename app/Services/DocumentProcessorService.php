<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyField;
use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class DocumentProcessorService
{
    /**
     * Process a template content or file replacing placeholders with actual values.
     */
    public function process(string $content, DocumentTemplate $template, ?Model $contextModel = null): string
    {
        $replacements = $this->getReplacements($template, $contextModel);
        
        // Match <# variable #>
        preg_match_all('/<#\s*(.*?)\s*#>/', $content, $matches);
        
        if (empty($matches[0])) {
            return $content;
        }

        $processedContent = $content;
        foreach ($matches[0] as $index => $placeholder) {
            $varName = $matches[1][$index];
            foreach ($replacements as $key => $data) {
                if (strtolower($key) === strtolower($varName)) {
                    $value = $data['value'];
                    if ($data['is_bold']) {
                        $value = "<strong>{$value}</strong>";
                    }
                    $processedContent = str_replace($placeholder, $value, $processedContent);
                    break;
                }
            }
        }

        return $processedContent;
    }

    /**
     * Process a docx file replacing placeholders.
     */
    public function processDocx(string $filePath, DocumentTemplate $template, ?Model $contextModel = null): string
    {
        $replacements = $this->getReplacements($template, $contextModel);
        
        $tempPath = storage_path('app/temp');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        $tempFile = $tempPath . '/' . uniqid('docx_') . '.docx';
        
        if (!Storage::exists($filePath)) {
            throw new \Exception("El archivo de plantilla no existe en el almacenamiento. Si cambiaste de disco (ej. a S3), asegúrate de que el archivo esté disponible.");
        }

        $content = Storage::get($filePath);
        if (empty($content)) {
            throw new \Exception("El archivo de plantilla está vacío o no se pudo descargar del almacenamiento.");
        }
        
        file_put_contents($tempFile, $content);

        $zip = new \ZipArchive();
        if ($zip->open($tempFile) === true) {
            // Process document, headers and footers
            $files = ['word/document.xml'];
            for ($i = 1; $i <= 10; $i++) {
                $files[] = "word/header{$i}.xml";
                $files[] = "word/footer{$i}.xml";
            }

            foreach ($files as $file) {
                $xml = $zip->getFromName($file);
                if ($xml === false) continue;

                foreach ($replacements as $key => $data) {
                    $value = htmlspecialchars((string)$data['value']);
                    
                    // If bold, we wrap the value in a bold run structure
                    // This breaks out of the current text tag and creates a bold one
                    if ($data['is_bold']) {
                        $value = "</w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t>{$value}</w:t></w:r><w:r><w:t>";
                    }

                    // This regex allows any number of XML tags between the parts of the placeholder
                    $pattern = '/&lt;#\s*(?:<[^>]+>)*\s*' . preg_quote($key, '/') . '\s*(?:<[^>]+>)*\s*#&gt;/i';
                    $xml = preg_replace($pattern, $value, $xml);
                }
                
                $zip->addFromString($file, $xml);
            }
            
            $zip->close();
        }

        return $tempFile;
    }

    private function getReplacements(DocumentTemplate $template, ?Model $contextModel = null): array
    {
        $replacements = [];
        $company = $template->company;

        // Custom Template Variables
        foreach ($template->variables as $field) {
            $replacements[$field->name] = [
                'value' => $field->value,
                'is_bold' => $field->is_bold
            ];
        }

        // Context Model Attributes
        if ($contextModel) {
            $attributes = $contextModel->toArray();
            foreach ($attributes as $key => $value) {
                if (is_string($value) || is_numeric($value)) {
                    $replacements[$key] = [
                        'value' => $value,
                        'is_bold' => false
                    ];
                }
            }

            if (isset($contextModel->user)) {
                $replacements['nombre_empleado'] = ['value' => $contextModel->user->name, 'is_bold' => false];
                $replacements['email_empleado'] = ['value' => $contextModel->user->email, 'is_bold' => false];
            }
        }

        // Standard Company info
        $replacements['empresa_nombre'] = ['value' => $company->name, 'is_bold' => false];
        $replacements['empresa_rnc'] = ['value' => $company->rnc, 'is_bold' => false];
        $replacements['empresa_direccion'] = ['value' => $company->address, 'is_bold' => false];

        return $replacements;
    }

    /**
     * Extract variables from a template content or file.
     */
    public function extractVariables(DocumentTemplate $template): array
    {
        $variables = [];

        if ($template->file_path && str_ends_with($template->file_path, '.docx')) {
            if (Storage::exists($template->file_path)) {
                $tempFile = tempnam(sys_get_temp_dir(), 'docx');
                file_put_contents($tempFile, Storage::get($template->file_path));
                $zip = new \ZipArchive();
                if ($zip->open($tempFile) === true) {
                    $files = ['word/document.xml'];
                    for ($i = 1; $i <= 10; $i++) {
                        $files[] = "word/header{$i}.xml";
                        $files[] = "word/footer{$i}.xml";
                    }

                    foreach ($files as $file) {
                        $xml = $zip->getFromName($file);
                        if ($xml !== false) {
                            $text = strip_tags($xml);
                            // Match &lt;# variable #&gt; and <# variable #>
                            preg_match_all('/(?:&lt;|<)#\s*(.*?)\s*#(?:&gt;|>)/', $text, $matches);
                            if (!empty($matches[1])) {
                                foreach ($matches[1] as $var) {
                                    $variables[] = trim($var);
                                }
                            }
                        }
                    }
                    $zip->close();
                }
                unlink($tempFile);
            }
        } elseif ($template->content) {
            preg_match_all('/<#\s*(.*?)\s*#>/', $template->content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $var) {
                    $variables[] = trim($var);
                }
            }
        }

        return array_values(array_unique($variables));
    }
}
