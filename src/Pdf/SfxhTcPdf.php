<?php
namespace FoxholeEmails\Pdf;

use TCPDF;

class SfxhTcPdf extends TCPDF
{
    const DEFAULT_LOGO = 'logo.jpg';
    const TITLE = '<< STAY FOXHOLE >>';

    private $vars = [
        'user_name'           => 'John Doe',
        'listing_title'       => 'The Aviator’s Retreat',
        'property_address'    => '123 Airstrip Blvd, Green Bay, WI 54304',
        'booking_dates_start' => '2025-04-15',
        'booking_dates_end'   => '2025-04-18',
        'booking_nights'      => 3,
        'booking_id'          => 'FOX123456',
        'booking_total'       => '$589.47',
        'pricing_breakdown'   => '
            <tr>
                <td>2025-04-15</td>
                <td>$150.00</td>
                <td>$12.00</td>
                <td>$5.00</td>
                <td>$167.00</td>
            </tr>
            <tr>
                <td>2025-04-16</td>
                <td>$150.00</td>
                <td>$12.00</td>
                <td>$5.00</td>
                <td>$167.00</td>
            </tr>
            <tr>
                <td>2025-04-17</td>
                <td>$230.00</td>
                <td>$18.40</td>
                <td>$7.07</td>
                <td>$255.47</td>
            </tr>'
    ];

    public function Header()
    {
        $this->writeHTML($this->print_html_header(), true, false, true, false, '');
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function print_html_header() {
        $logo_path = $this->get_logo(); 
    
        $header_file = FOXHOLE_EMAILS_PLUGIN . 'templates/partials/header.php';
        if (file_exists($header_file)) {
            ob_start();
            $logo_path_escaped = htmlspecialchars($logo_path, ENT_QUOTES);
            include $header_file;
            return ob_get_clean();
        }
    
        return ''; 
    }

    public function get_logo() {
        return FOXHOLE_EMAILS_PLUGIN . 'resources/images/' . self::DEFAULT_LOGO;
    }

    public function get_vars() {

    }
}
