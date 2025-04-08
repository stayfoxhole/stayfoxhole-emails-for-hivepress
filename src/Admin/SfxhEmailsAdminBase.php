<?php
namespace FoxholeEmails\Admin;

defined( 'ABSPATH' ) || exit;

class SfxhEmailsAdminBase {
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'my_custom_admin_script_enqueue']);
    }

    function my_custom_admin_script_enqueue($hook) {
        global $post;
    
        if ($hook === 'post.php' && isset($_GET['action']) && $_GET['action'] === 'edit') {
            if (isset($_GET['post'])) {
                wp_register_script(
                    'sfxh-test-email',
                    FOXHOLE_EMAILS_PLUGIN_URL . 'resources/assets/js/sfxh-test-email.js',
                    ['jquery'],
                    '1.0.01',
                    true
                );
    
                wp_localize_script('sfxh-test-email', 'sfxhEmails', [
                    'siteUrl'   => get_site_url(),
                    'restUrl'   => esc_url_raw(rest_url()),
                    'restNonce' => wp_create_nonce('wp_rest'),
                ]);
    
                wp_enqueue_script('sfxh-test-email');
            }
        }
    }
}


