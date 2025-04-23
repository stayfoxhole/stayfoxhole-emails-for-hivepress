<?php
$booking_id = get_the_ID(); 
$pdf_url = add_query_arg( [
	'action'     => 'download_pdf_receipt',
	'booking_id' => $booking_id,
], admin_url( 'admin-ajax.php' ) );
?>

<a href="<?php echo esc_url( $pdf_url ); ?>" class="hp-button" target="_blank" rel="noopener">
	<?php esc_html_e( 'Download PDF Receipt', 'foxhole' ); ?>
</a>
