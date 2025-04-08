<?php
namespace HivePress\Emails;

defined( 'ABSPATH' ) || exit;

use HivePress\Helpers as hp;

class Sfxh_Balance_Due extends Email {
	public function __construct( $args = [] ) {
		$args = hp\merge_arrays(
			[
				'subject' => 'Balance due',
				'body'    => '',
			],
			$args
		);

		parent::__construct( $args );
	}

	/**
	 * Class initializer.
	 *
	 * @param array $meta Form meta.
	 */
	public static function init( $meta = [] ) {
		$meta = hp\merge_arrays(
			[
				'label'       => esc_html__( 'Balance Due', 'hivepress-marketplace' ),
				'description' => esc_html__( 'This email is sent to users when a balance is due in order to complete a booking.', 'hivepress-marketplace' ),
				'recipient'   => hivepress()->translator->get_string( 'vendor' ),
				'tokens'      => [ 'user_name', 'order_number', 'order_amount', 'order_url', 'user' ],
			],
			$meta
		);

		parent::init( $meta );
	}
}
