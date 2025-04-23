<?php
namespace FoxholeEmails\Admin\Ajax;

use FoxholeEmails\Pdf\SfxhPdfPreview;
use HivePress\Models\Booking;
use TCPDF;

class SfxhStreamPdf {
    public function __construct() {
        add_action('wp_ajax_download_pdf_receipt', [$this, 'download_pdf']);
        add_action('wp_ajax_nopriv_download_pdf_receipt', [$this, 'download_pdf']);
    }

    public function download_pdf() {
        if (!isset($_GET['booking_id'])) {
            wp_send_json_error(['message' => 'Missing booking_id']);
            return;
        }

        $booking_id = absint($_GET['booking_id']);
        $booking = Booking::query()->get_by_id($booking_id);

        if (!$booking) {
            wp_send_json_error(['message' => 'Booking not found']);
            return;
        }

        $pdf = new SfxhPdfPreview($booking);
        $pdf->output_browser();
    }
}
