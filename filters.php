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

add_filter( 'wp_mail_from', function( $original_email_address ) {
    return 'no-reply@stayfoxhole.com'; 
});

add_filter( 'wp_mail_from_name', function( $original_email_from ) {
    return 'STAY FOXHOLE'; 
});

add_filter( 'hivepress/v1/emails/listing_expire', function($email) {
	$email['body'] = '';

	return $email;
});