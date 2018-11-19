<?php

class AC_Column_gpf extends \AC\Column {

	public function __construct() {

		// Identifier, pick an unique name. Single word, no spaces. Underscores allowed.
		$this->set_type( 'column-gpf' );

		// Default column label.
		$this->set_label( __( 'GPF', 'ac-gpf' ) );
	}

	/**
	 * Get the raw, underlying value for the column
	 * Not suitable for direct display, use get_value() for that
	 * This value will be used by 'inline-edit' and get_value().
	 *
	 * @param int $id ID
	 *
	 * @return mixed Value
	 */
	public function get_raw_value( $post_id ) {

		// put all the column logic here to retrieve the value you need
		// For example: $value = get_post_meta ( $post_id, '_my_custom_field_example', true );

		// Retrieving the serialized data
		$gpf_serialized = get_post_meta ( $post_id, '_woocommerce_gpf_data', true );

		// Checking if custom field was found for this post ID. If not, die.
		if ( $gpf_serialized === '' ) {
			return;
		}

		// Checking if 'excluded_product' exists in array to prevent PHP notice
		if ( isset ( $gpf_serialized['exclude_product'] ) ) {

			// Checking if 'exclude_product' is set to on. If yes, die.
			if ( $gpf_serialized['exclude_product'] === 'on' ) {

				return;

			}


		}

		// Get the priority level from the Custom Field
		if ( get_field ( 'gpf_priority_level' ) ) {

			// Set the priority level
			$gpf_feed_status = get_field ( 'gpf_priority_level' );

		} else {

			// If no custom field is available for the priority level. Just say 'on' instead.
			$gpf_feed_status = 'On';

		}

		if ( isset ( $gpf_feed_status ) ) {
			return $gpf_feed_status;
		}
		

	}

}