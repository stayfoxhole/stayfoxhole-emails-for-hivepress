<?php
namespace FoxholeEmails\Pdf;

use TCPDF;
use FoxholeEmails\Pdf\SfxhTcPdf;
use HivePress\Models\Booking;
use HivePress\Models\Listing;
use HivePress\Models\User;

class SfxhPdfPreview {
    private $vars = [
        'user_name'           => '',
        'listing_title'       => '',
        'property_address'    => '',
        'booking_dates_start' => '',
        'booking_dates_end'   => '',
        'booking_nights'      => '',
        'booking_id'          => '',
        'booking_total'       => '',
        'payment_method'      => '',
    ];

    private $price_breakdown_html;
    private $booking;
    private $pdf;

    public function __construct(Booking $booking) {
        $this->booking = $booking;
        $this->set_vars();
        $this->set_pricing_breakdown();

        $this->pdf = new SfxhTcPdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor(SFXH_PDF_AUTHOR);
        $this->pdf->SetTitle(SFXH_PDF_TITLE);
        $this->pdf->SetSubject(SFXH_PDF_SUBJECT);
        $this->pdf->SetKeywords(SFXH_PDF_KEYWORDS);
        $this->pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->pdf->SetFont(SFXH_PDF_FONT, SFXH_PDF_FONT_STYLE, SFXH_PDF_FONT_SIZE);
    }

    public function output() {
        ob_clean();
        flush();

        $this->pdf->AddPage();
        $this->pdf->writeHTML($this->print_receipt_meta(), true, false, true, false, '');
        $this->pdf->Output(FOXHOLE_EMAILS_PLUGIN . 'pdf_library/' . SFXH_PDF_FILENAME, 'F');

        exit;
    }

    public function output_browser() {
        ob_clean();
        flush();

        $this->pdf->AddPage();
        $this->pdf->writeHTML($this->print_receipt_meta(), true, false, true, false, '');
        $this->pdf->Output(SFXH_PDF_FILENAME, 'D'); 

        exit;
    }

    private function print_receipt_meta() {
        $vars = $this->vars;
        $pricing_breakdown_html = $this->price_breakdown_html;
        $logo_path = $this->pdf->get_logo(); 
        $file = FOXHOLE_EMAILS_PLUGIN . 'templates/partials/receipt-meta.php';
        if (file_exists($file)) {
            ob_start();
            $logo_path_escaped = htmlspecialchars($logo_path, ENT_QUOTES);
            include $file;
            return ob_get_clean();
        }

        return '';
    }

    private function set_vars() {
        if (!$this->booking) return;

        $user = $this->booking->get_user();
        $listing = $this->booking->get_listing();
        $booking_id = $this->booking->get_id();

        $start_time = $this->booking->get_start_time();
        $end_time = $this->booking->get_end_time();

        if (!$start_time || !$end_time) {
            error_log("Start or end time missing for booking ID: $booking_id");
            return;
        }

        $check_in_date  = date_i18n('m/d/Y', $start_time);
        $check_out_date = date_i18n('m/d/Y', $end_time);
        $nights_stayed  = round(($end_time - $start_time) / DAY_IN_SECONDS);

        $user_name = $user ? $user->get_display_name() : 'Guest';
        $listing_title = $listing ? $listing->get_title() : 'Unknown Property';

        $orders = wc_get_orders([
            'limit'        => 1,
            'meta_key'     => 'hp_booking',
            'meta_value'   => $booking_id,
            'meta_compare' => '='
        ]);

        $order = $orders ? reset($orders) : null;
        $booking_total = $order ? $order->get_total() : 0;
        $payment_method = $order ? $order->get_payment_method_title() : 'Unknown';

        $this->vars = [
            'user_name'           => $user_name,
            'listing_title'       => $listing_title,
            'booking_dates_start' => $check_in_date,
            'booking_dates_end'   => $check_out_date,
            'booking_nights'      => $nights_stayed,
            'booking_id'          => $booking_id,
            'booking_total'       => number_format($booking_total, 2),
            'payment_method'      => $payment_method,
        ];
    }

    private function set_pricing_breakdown() {
        $price_breakdown = '';
        $booking_id = $this->booking->get_id();

        $orders = wc_get_orders([
            'limit'        => 1,
            'meta_key'     => 'hp_booking',
            'meta_value'   => $booking_id,
            'meta_compare' => '='
        ]);

        $order = $orders ? reset($orders) : null;

        if ($order) {
            foreach ($order->get_items() as $item) {
                $subtotal = number_format($item->get_subtotal(), 2);
                $tax = number_format($item->get_total_tax(), 2);
                $total = number_format($item->get_total(), 2);

                $price_breakdown .= "<tr>
                    <td>" . date('Y-m-d') . "</td>
                    <td>\$$subtotal</td>
                    <td>\$$tax</td>
                    <td>\$0.00</td>
                    <td>\$$total</td>
                </tr>";
            }
        }

        $this->price_breakdown_html = $price_breakdown;
    }
}
