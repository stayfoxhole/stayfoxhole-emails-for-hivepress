<?php
namespace FoxholeEmails\Includes\Template;

class SfxhTemplate {
    const TEMPLATE_PATH = 'templates-wp';

    protected $templates = [
        [
            'file' => 'template-foxhole-pdf.php',
            'name' => 'Foxhole PDF Receipt',
        ],
    ];

    public function add_template($templates) {
        foreach ($this->templates as $template) {
            if (file_exists(FOXHOLE_EMAILS_PLUGIN . self::TEMPLATE_PATH . '/' . $template['file'])) {
                $templates[$template['file']] = __($template['name'], 'stayfoxhole');
            }
        }

        return $templates;
    }

    public function load_template($template) {
        foreach ($this->templates as $tpl) {
            $template_path = FOXHOLE_EMAILS_PLUGIN . self::TEMPLATE_PATH . '/' . $tpl['file'];
            if (file_exists($template_path) && is_page_template($tpl['file'])) {
                return $template_path;
            }
        }

        return $template;
    }
}
