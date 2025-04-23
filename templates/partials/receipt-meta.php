<?php
defined('ABSPATH') || exit;

echo 
'<div class="container">
    <h2 style="text-align: center;">FOXHOLE LODGING RECEIPT</h2>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Guest Name:</strong>'. $vars['user_name'] . '</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Property:</strong> '. $vars['listing_title'] . '</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Address:</strong>'. $vars['user_name'] . '</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Check-in Date:</strong>'. $vars['booking_dates_start'] .'</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Check-out Date:</strong>'. $vars['booking_dates_end'] . '</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Nights Stayed:</strong>'. $vars['booking_nights'] . '</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Room Rate:</strong> Varies per night (see breakdown)</p>
    <p style="font-size: 16px; line-height: 21px;margin-botton: 3.5px;"><strong>Confirmation Number:</strong>'. $vars['booking_id'] . '</p>

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
        '. $pricing_breakdown_html .'
    </table>


    <p class="total-row"><strong>Total Stay Cost:</strong>'. $vars['booking_total'] . '</p>
    <p class="total-row"><strong>PAID IN FULL - BALANCE DUE:</strong> $0.00</p>
    
    <p class="footer">
        This receipt reflects only the lodging charges and associated taxes per night.
    </p>
</div>';