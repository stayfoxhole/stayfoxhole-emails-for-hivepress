<?php
namespace FoxholeEmails\Emails;

defined( 'ABSPATH' ) || exit;

use HivePress\Models\Booking;
use HivePress\Models\Listing;
use HivePress\Models\User;

class SfxhBookingConfirmVendor {
    public $vars = [];

    public function create_body($email) {
        if($email['tokens']['booking']) {
            $booking = $email['tokens']['booking'];
            $this->set_vars($booking->get_id());
        }

        if(count($this->vars)) {
            $email['body'] = $this->get_email_body();
        }

        return $email;
    }

    public function set_vars($booking_id) {
        $booking = Booking::query()->get_by_id($booking_id);
        if (!$booking) {
            error_log("Booking not found for ID: " . $booking_id);
            return false;
        }

        $booking_url = hivepress()->router->get_url( 'booking_view_page', [
            'booking_id' => $booking->get_id(),
        ]);        
    
        $listing = $booking->get_listing();

        if (!$listing) {
            error_log("Listing not found for booking ID: " . $booking_id);
            return false;
        }

        $vendor = $listing ? $listing->get_vendor() : null;
        $host_user = $vendor ? $vendor->get_user() : null;

        if (!$host_user) {
            error_log("User not found for booking ID: " . $booking_id);
        }

        $host_name = $host_user ? $host_user->get_display_name() : 'Unknown Host';
        $host_email = $host_user ? $host_user->get_email() : 'No Email';

        $vars = [
            'user_name'          => $host_name?? 'Guest',
            'listing_title'      => $listing ? $listing->get_title() : 'Unknown Property',
            'booking_id'         => $booking_id,
            'booking_url'         => $booking_url,
        ];
    
        $this->vars = $vars;       
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

                <p><strong>Dear '. $this->vars['user_name'] . ',</strong></p>
                <p>Great news! A guest confirmed a booking for your listing: '. $this->vars['listing_title'] . '</p>
                <p>Click on the following link to view it: <strong><a href='. $this->vars['booking_url'].'>'. $this->vars['booking_url'] . '</strong></p>
                <p>Please ensure your listing is ready for their arrival and feel free to message your guest with any important details.</p>
                <p>Thank you for hosting your listing on Foxhole!</p>
                <p>-The Foxhole Team</p>
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