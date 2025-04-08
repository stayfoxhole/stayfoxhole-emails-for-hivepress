<?php
namespace FoxholeEmails\Routes;

use FoxholeEmails\Routes\SfxhEmailsRoutes;
use HivePress\Models\Booking;
use HivePress\Models\Listing;
use HivePress\Models\User;
use HivePress\Helpers as hp;
use HivePress\Emails;

class SfxhEmailsTestRoute extends SfxhEmailsRoutes {
    const TEST_EMAIL = 'me@philiparudy.com';

    protected $namespace = 'foxhole/v1';

    protected $routes = [
        'login' => [
            'methods' => 'POST',
            'callback' => 'send_email',
            'path' => '/send-email',
        ],
        'test_email' => [
            'methods' => 'POST',
            'callback' => 'test_email',
            'path' => '/test-email',
        ]
    ];

    public function boot() {
        $this->register_routes();
    }

    public function sfxh_balance_due($params) {
        $recipient = isset($params['recipient']) ? $params['recipient'] : self::TEST_EMAIL;

        $user_email_args = hp\merge_arrays(
            $email_args,
            [
                'recipient' => $recipient,
                'tokens'    => [
                ],
            ]
        );
        
        ( new Emails\Sfxh_Balance_Due( $user_email_args ) )->send();

        return rest_ensure_response(['success' => true]);
    }

    public function booking_confirm_user($params) {
        $email = isset($params['recipient']) ? $params['recipient'] : self::TEST_EMAIL;
        $booking_id = isset($params['booking_id']) ? $params['booking_id'] : null;
    
        $wp_user = get_user_by('email', $email);
        if (!$wp_user) {
            return hp\rest_error(404, 'User not found');
        }

        // Get the HivePress User model by ID
        $user = User::query()->get_by_id($wp_user->ID);
        if (!$user) {
            return hp\rest_error(404, 'HivePress User not found');
        }
    
        $booking = $booking_id ? Booking::query()->get_by_id($booking_id) : null;
    
        $user_email_args = [
            'recipient' => $email,
            'tokens'    => [
                'user'      => $user,
                'user_name' => $user->get_display_name(),
                'booking'   => $booking,
            ],
        ];
    
        ( new Emails\Booking_Confirm_User($user_email_args) )->send();
    
        return rest_ensure_response(['success' => true]);
    } 
    
    public function booking_confirm_vendor($params) {
        $email = isset($params['recipient']) ? $params['recipient'] : self::TEST_EMAIL;
        $booking_id = isset($params['booking_id']) ? $params['booking_id'] : null;
    
        $wp_user = get_user_by('email', $email);
        if (!$wp_user) {
            return hp\rest_error(404, 'User not found');
        }

        // Get the HivePress User model by ID
        $user = User::query()->get_by_id($wp_user->ID);
        if (!$user) {
            return hp\rest_error(404, 'HivePress User not found');
        }
    
        $booking = $booking_id ? Booking::query()->get_by_id($booking_id) : null;
    
        $user_email_args = [
            'recipient' => $email,
            'tokens'    => [
                'user'      => $user,
                'user_name' => $user->get_display_name(),
                'booking'   => $booking,
            ],
        ];
    
        ( new Emails\Booking_Confirm_Vendor($user_email_args) )->send();
    
        return rest_ensure_response(['success' => true]);
    } 
    
    public function test_email(\WP_REST_Request $request) {
        $params = $request->get_params();
        $template = $params['template'] ?? null;
    
        if ($template && method_exists($this, $template)) {
            return rest_ensure_response($this->$template($params));
        }

       

        return rest_ensure_response([
            'wmadeit' => "here"
        ]);
    }

    public function send_email(\WP_REST_Request $request) {
        $params = $request->get_params();
        $email = self::TEST_EMAIL;
        $booking_id = $params['booking_id'] ?? null;

        if (isset($params['email']) && $params['email']) {
            $email = $params['email'];
        }

        if (!$booking_id) {
            return new \WP_Error('missing_booking_id', 'Booking ID is required', ['status' => 400]);
        }

        $booking_data = $this->get_booking_details($booking_id);

        if (!$booking_data) {
            return new \WP_Error('invalid_booking', 'Booking not found', ['status' => 404]);
        }

        ( new \HivePress\Emails\Otslr_Hp_Email(
            [
                'recipient' => $email,
                'tokens'    => $booking_data,
                'booking_table_rows' => $booking_data['booking_table_rows']
            ]
        ) )->send();

        return rest_ensure_response([
            'success' => true,
            'data' => $email
        ]);
    }

    private function get_booking_details($booking_id) {
        // Retrieve booking object
        $booking = Booking::query()->get_by_id($booking_id);
        if (!$booking) {
            error_log("Booking not found for ID: " . $booking_id);
            return false;
        }
    
        // Retrieve related user and listing using getter methods
        $user = $booking->get_user();
        $listing = $booking->get_listing(); // No need to fetch it again
    
        if (!$user) {
            error_log("User not found for booking ID: " . $booking_id);
        }
        if (!$listing) {
            error_log("Listing not found for booking ID: " . $booking_id);
            return false;
        }
        $args = [
            'limit'        => 1, 
            'meta_key'     => 'hp_booking',
            'meta_value'   => $booking_id, 
            'meta_compare' => '='
        ];
        
        $orders = wc_get_orders($args);
        
        $order_id = !empty($orders) ? reset($orders)->get_id() : null; // Store the order ID or null if not found
        
        $start_time = $booking->get_start_time();
        $end_time = $booking->get_end_time();
    
        // error_log("Booking ID: $booking_id | Retrieved Start Time: " . print_r($start_time, true));
        // error_log("Booking ID: $booking_id | Retrieved End Time: " . print_r($end_time, true));
    
        if (!$start_time || !$end_time) {
            error_log("Start or end time missing for booking ID: " . $booking_id);
            return false;
        }
    
        // Format dates
        $check_in_date  = date_i18n('m/d/Y', $start_time);
        $check_out_date = date_i18n('m/d/Y', $end_time);
        $nights_stayed  = round(($end_time - $start_time) / DAY_IN_SECONDS);
    
        // Retrieve listing price dynamically (Assuming stored in meta field 'listing_price')
        $base_price = get_post_meta($listing->get_id(), 'listing_price', true) ?: 250;
    
        // Generate a dynamic price breakdown using the listing price
        $price_breakdown = $this->generate_price_breakdown($order_id);
        
        return [
            'user_name'          => $user ? $user->get_display_name() : 'Guest',
            'listing_title'      => $listing ? $listing->get_title() : 'Unknown Property',
            'booking_dates_start' => $check_in_date,
            'booking_dates_end'   => $check_out_date,
            'booking_nights'     => $nights_stayed,
            'booking_id'         => $booking_id,
            'payment_method'     => get_post_meta($booking_id, 'payment_method', true) ?: 'Unknown Payment Method',
            'booking_table_rows' => $price_breakdown,
            'booking_total'      => '$' . number_format((float) $total_price, 2)
        ];       
    }

    private function generate_price_breakdown($order_id) {
        $order = wc_get_order($order_id);
        
        $price_breakdown = '';
    
        if ($order) {
            foreach ($order->get_items() as $item) {
                $product_name = $item->get_name();
                $product_price = number_format($item->get_subtotal(), 2);
    
                // Generate each row as raw HTML
                $price_breakdown .= "<tr>
                    <td>" . date('Y-m-d') . "</td> 
                    <td>\$$product_price</td>
                    <td>\$0.00</td>
                    <td>\$0.00</td>
                    <td>\$$product_price</td>
                </tr>";
            }
        }
    
        return $price_breakdown;
    }
    
    private function format_price_breakdown($price_breakdown) {
        $rows = '';
    
        foreach ($price_breakdown as $row) {
            $rows .= "<tr>
                <td>{$row['date']}</td>
                <td>\${$row['lodging']}</td>
                <td>\${$row['state_tax']}</td>
                <td>\${$row['occupancy_tax']}</td>
                <td>\${$row['total']}</td>
            </tr>";
        }
    
        return $rows;
    }
}
