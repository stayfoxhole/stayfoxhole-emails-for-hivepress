<?php
namespace FoxholeEmails\Routes;

abstract class SfxhEmailsRoutes {
    protected $routes = [];
    protected $namespace = 'foxhole/v1';
    
    private $api_key = null;
    private $mp_namepsace = 'mp/v1';

    public function __construct() {
        $this->set_api_key();

        add_filter( 'nonce_life', [$this, 'extend_rest_nonce_lifetime'] );
    }

    /**
     * Let's bump this up one extra hour to 25 hours.
     * @return int
     */
    public function extend_rest_nonce_lifetime() {
        return 86400 + 3600;
    }
    
    public function set_api_key() {
        $this->api_key = get_option('mpdt_api_key', '');
    }

    public function register_routes( ) {
        foreach($this->routes as $handle => $route) {
            register_rest_route($this->namespace, $route['path'], [
                'methods' => $route['methods'],
                'callback' => [$this, $route['callback']],
                'permission_callback' => '__return_true'
            ]);
        }
    }

    public function get_site_url() {
        if(strpos(site_url(), 'localhost') !== false) {
            return 'http://localhost:8081';
        }

        return site_url();
    }

    public function get_mp_endpoint($route, $id = null) {
        $mp_path = $this->routes[$route]['mp_path'] ?? null;
  
        if($mp_path && $id) {
            $rest_url_prefix = rest_get_url_prefix();
            $mp_path = str_replace('{id}', $id, $mp_path);
        
            return trailingslashit($this->get_site_url()) . $rest_url_prefix . '/' . $this->mp_namepsace . $mp_path;
        }

        return null;
    }

    public function otslr_mp_remote_post($url) {
        if(!$this->api_key) {
            return new \WP_Error('api_key_missing', 'API key is missing', ['status' => 400]);
        }
        
        $response = wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'MEMBERPRESS-API-KEY' => $this->api_key,
            ]
        ]);

        return $response;
    }
}