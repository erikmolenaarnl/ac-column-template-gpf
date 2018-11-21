<?php

class AC_Column_gpf extends \AC\Column\Meta {

	public function __construct() {

		// Identifier, pick an unique name. Single word, no spaces. Underscores allowed.
		$this->set_type( 'column-gpf' );

		// Default column label.
		$this->set_label( __( 'GPF', 'ac-gpf' ) );
	}

	/**
	 * The meta key for this column.
	 * @return string
	 */
	public function get_meta_key() {
		return 'gpf_priority_level';
	}

	/**
	 * The meta type for this column.
	 * @return string
	 */
	public function get_meta_type() {
		return 'post';
	}

	/**
	 * The meta key for the woocommerce GPF excluded data.
	 * @return string
	 */
	public function get_wc_gpf_key() {
		return '_woocommerce_gpf_data';
	}

	/**
	 * Get the array key that would exclude a product from the feed.
	 * @return string
	 */
	public function get_wc_gpf_excluded_key() {
		return 'exclude_product';
	}

	/**
	 * Get the value that excludes a product from the feed.
	 * @return string
	 */
	public function get_wc_gpf_excluded_value() {
		return 'on';
	}

	/**
	 * Get the metadata value for filter comparison.
	 */
	public function get_wc_gpf_filter_value() {
		return serialize( array( $this->get_wc_gpf_excluded_key() => $this->get_wc_gpf_excluded_value() ) );
	}

	/**
	 * Check if a post is excluded from the product feed.
	 */
	public function product_is_excluded( $post_id ) {

		// Retrieving the serialized data
		$gpf_serialized = get_post_meta( $post_id, $this->get_wc_gpf_key(), true );

		// Checking if custom field was found. If not, die.
		// Since this is an array type field (serialized) it should always return an array.
		if ( ! is_array( $gpf_serialized ) ) {
			return false;
		}

		$key = $this->get_wc_gpf_excluded_key();
		$val = $this->get_wc_gpf_excluded_value();

		// Checking if 'excluded_product' exists in array AND if it is set to 'on'
		if ( isset ( $gpf_serialized[ $key ] ) && $gpf_serialized[ $key ] === $val ) {
			return true;
		}

		return false;
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

		// Checking if this post is excluded.
		if ( $this->product_is_excluded( $post_id ) ) {
			return __( 'Excluded', 'ac-column-template-gpf' );
		}

		$value = parent::get_raw_value( $post_id );

		if ( ! $value ) {
			return __( 'Default Priority', 'ac-column-template-gpf' );
		}

		return $value;

	}

}
