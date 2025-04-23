<?php
/**
 * Meta Boxes
 * screen: email
 * 
 */
add_filter(
	'hivepress/v1/meta_boxes',
	function( $meta_boxes ) {
		$meta_boxes['email_admin'] = [
			'title'  => 'Administrative',
			'screen' => ['email'],

			'fields' => [
				'recipient' => [
					'label'  => 'Recipient',
					'type'   => 'text',
					'_order' => 11,
				],
				'booking_id' => [
					'label'  => 'Booking ID',
					'type'   => 'number',
					'_order' => 12,
                ],
                'send_email_test' => [
                    'label'  => 'Send Email',
                    'caption'      => esc_html__( 'Send Email', 'hivepress' ),
                    'type'         => 'button',
                    'display_type' => 'button',

                    'attributes'   => [
                        'data-class' => 'sfxh-send-test-email'
                    ],
                ],
			],
		];

		return $meta_boxes;
	}
);

/**
 * BLOCKS
 */
add_filter( 
	'hivepress/v1/templates/booking_view_block/blocks', 
	function( $blocks, $template ) {
	if (isset( $blocks['booking_container']['blocks']['booking_footer']['blocks']['booking_actions_primary']['blocks'] )) {
		$blocks['booking_container']['blocks']['booking_footer']['blocks']['booking_actions_primary']['blocks']['pdf_receipt_download'] = [
			'type'   => 'part',
			'path'   => 'booking/view/block/pdf-receipt-download',
			'_order' => 20,
		];
	}

	return $blocks;
}, 10, 2 );

