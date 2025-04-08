<?php
namespace FoxholeEmails\Emails;

defined( 'ABSPATH' ) || exit;

class SfxhBalanceDue {
    public $vars = [];

    public function create_body($email) {
        $this->set_vars();
        $email['body'] = $this->get_email_body();

        return $email;
    }

    public function set_vars() {
        $vars = [];
    
        $this->vars = $vars;       
    }

    public function get_email_body() {
        $site_logo = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($site_logo, 'full');
        if (!$logo_url) {
            $logo_url = get_template_directory_uri() . '/path-to-default-logo.png'; // Fallback logo
        }
        $header_path = FOXHOLE_EMAILS_PLUGIN . 'templates/partials/header.php';
        $header_html = $this->render_template($header_path, [
            'logo_url' => $logo_url,
        ]);

        $address = '5th Street Stay - 531 Upper';
        $payment_link = 'https://platform.stayfoxhole.com/short?a0=1110daf7-fef1-4f72-9588-6673a5e5ebe3';

        $content ='';

        $content = '<div class="container">
            ' . $header_html  . '
            <p>Dear <strong>Guest Name:</strong>'. $this->vars['user_name'] . '</p>
            <p>Your balance is due to finalize your reservation for property '.$address.'</p>
            <p>Please use the following link to proceed with the balance payment:</p>
            <p><strong>Secured Payment Form:</strong>'. $payment_link .'</p>
            <p>Please feel free to contact us should you need any assistance. We\'d be happy to help.</p>
            <p>STAYFOXHOLE ,<br /> https://stayfoxhole.com</p>
        </div>';
        
        return '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { width: 100%; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; }
                h2 { color: #2d89ef; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background-color: #f4f4f4; }
                .total-row { font-weight: bold; }
                .footer { text-align: center; font-size: 12px; margin-top: 10px; color: #777; }
            </style>
        </head>
        <body>
            '.$content.'
        </body>
        </html>';
    }

    public function render_template($path, $data = []) {
        if (!file_exists($path)) {
            return '';
        }
    
        extract($data); 
        return include $path; 
    }    
}