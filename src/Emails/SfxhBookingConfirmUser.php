<?php
namespace FoxholeEmails\Emails;

defined( 'ABSPATH' ) || exit;

use HivePress\Models\Booking;
use HivePress\Models\Listing;
use HivePress\Models\User;

class SfxhBookingConfirmUser {
    public $vars = [];

    public function create_body($email) {
        if($email['tokens']['booking']) {
            $booking = $email['tokens']['booking'];
            $this->set_vars($booking->get_id());
        }

        $email['body'] = $this->get_email_body();

        return $email;
    }

    public function set_vars($booking_id) {
        $booking = Booking::query()->get_by_id($booking_id);
        if (!$booking) {
            error_log("Booking not found for ID: " . $booking_id);
            return false;
        }
    
        $user = $booking->get_user();
        $listing = $booking->get_listing();
    
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
        $order_id = !empty($orders) ? reset($orders)->get_id() : null;
    
        $start_time = $booking->get_start_time();
        $end_time = $booking->get_end_time();
    
        if (!$start_time || !$end_time) {
            error_log("Start or end time missing for booking ID: " . $booking_id);
            return false;
        }
    
        $check_in_date  = date_i18n('m/d/Y', $start_time);
        $check_out_date = date_i18n('m/d/Y', $end_time);
        $nights_stayed  = round(($end_time - $start_time) / DAY_IN_SECONDS);
    
        $base_price = get_post_meta($listing->get_id(), 'listing_price', true) ?: 250;
    
        $price_breakdown = $this->generate_price_breakdown($order_id);

        error_log(print_r($price_breakdown, true));
        error_log(print_r($price_breakdown, true));
    
        // Compute total price from order items
        $booking_total = 0;
        if ($order_id) {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                $booking_total += $item->get_total();
            }
        }

        $user = $booking->get_user();
        $name = $user ? $user->get_display_name() : 'Guest';

        $vars = [
            'user_name'          => $name ?? 'Guest',
            'listing_title'      => $listing ? $listing->get_title() : 'Unknown Property',
            'booking_dates_start' => $check_in_date,
            'booking_dates_end'   => $check_out_date,
            'booking_nights'     => $nights_stayed,
            'booking_id'         => $booking_id,
            'payment_method'     => get_post_meta($booking_id, 'payment_method', true) ?: 'Unknown Payment Method',
            'booking_total'      => number_format($booking_total, 2), // Ensure this is defined
            'pricing_breakdown' => $price_breakdown
        ];
    
        $this->vars = $vars;       
    }

    private function generate_price_breakdown($order_id) {
        $order = wc_get_order($order_id);
        $price_breakdown = '';
    
        if ($order) {
            foreach ($order->get_items() as $item) {
                $product_name  = $item->get_name();
                $subtotal      = number_format($item->get_subtotal(), 2);
                $tax           = number_format($item->get_total_tax(), 2);
                $total         = number_format($item->get_total(), 2); // includes tax & discounts
    
                $price_breakdown .= "<tr>
                    <td>" . date('Y-m-d') . "</td> 
                    <td>\$$subtotal</td>
                    <td>\$$tax</td>
                    <td>\$0.00</td>
                    <td>\$$total</td>
                </tr>";
            }
        }
    
        return $price_breakdown;
    }
    

    public function get_email_body() {
        $site_logo = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($site_logo, 'full');

        if (!$logo_url) {
            $logo_url = get_template_directory_uri() . '/path-to-default-logo.png'; // Fallback logo
        }

        $content ='';
        if(count($this->vars)) {
            $content = '<div class="container">
                <div style="width: 100%; background-color: #f5f5f5; text-align: center; padding: 40px 0;">
                        <img width="229" src="https://local-wp-philip.s3.us-east-1.amazonaws.com/foxhole.png" alt="Logo" style="max-width: 229px;width: 229px;min-width: 229px; height: auto;">
                </div>
                <h2>FOXHOLE LODGING RECEIPT</h2>
                <p><strong>Guest Name:</strong>'. $this->vars['user_name'] . '</p>
                <p><strong>Property:</strong> '. $this->vars['listing_title'] . '</p>
                <p><strong>Address:</strong>'. $this->vars['user_name'] . '</p>
                <p><strong>Check-in Date:</strong>'. $this->vars['booking_dates_start'] .'</p>
                <p><strong>Check-out Date:</strong>'. $this->vars['booking_dates_end'] . '</p>
                <p><strong>Nights Stayed:</strong>'. $this->vars['booking_nights'] . '</p>
                <p><strong>Room Rate:</strong> Varies per night (see breakdown)</p>
                <p><strong>Confirmation Number:</strong>'. $this->vars['booking_id'] . '</p>

                <h3>Payment Breakdown:</h3>
                <h3>Payment Breakdown:</h3>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Lodging Cost</th>
                        <th>Tax</th>
                        <th>Fees</th>
                        <th>Total Per Night</th>
                    </tr>
                    '. $this->vars['pricing_breakdown'] .'
                </table>


                <p class="total-row"><strong>Total Stay Cost:</strong>'. $this->vars['booking_total'] . '</p>
                <p class="total-row"><strong>PAID IN FULL - BALANCE DUE:</strong> $0.00</p>
                
                <p class="footer">
                    This receipt reflects only the lodging charges and associated taxes per night.
                </p>
            </div>';
        }

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
}