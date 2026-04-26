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
     * Process a .docx file using PHPWord TemplateProcessor.
     */
    public function processDocx(string $filePath, DocumentTemplate $template, ?Model $contextModel = null): string
    {
        $replacements = $this->getReplacements($template, $contextModel);
        $fullPath = storage_path('app/public/' . $filePath);
        
        $tempFile = tempnam(sys_get_temp_dir(), 'docx');
        copy($fullPath, $tempFile);

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
}
