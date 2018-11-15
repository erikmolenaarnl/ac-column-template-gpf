<?php

/**
 * --------------------------------------------
 * This column is for the PRO version only.
 * This adds editing and sorting to the column.
 * --------------------------------------------
 */
class ACP_Column_gpf extends AC_Column_gpf
	implements \ACP\Editing\Editable, \ACP\Sorting\Sortable, \ACP\Export\Exportable {

	public function editing() {
		return new ACP_Editing_Model_gpf( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model_gpf( $this );
	}

	public function export() {
		return new ACP_Export_Model_gpf( $this );
	}

}

/**
 * Editing class. Adds editing functionality to the column.
 */
class ACP_Editing_Model_gpf extends \ACP\Editing\Model {

	/**
	 * Editing view settings
	 *
	 * @return array Editable settings
	 */
	public function get_view_settings() {

		// available types: text, textarea, media, float, togglable, select, select2_dropdown and select2_tags
		$settings = array(
			'type' => 'text',
		);

		// (Optional) Only applies to type: togglable, select, select2_dropdown and select2_tags
		// $settings['options'] = array( 'value_1', 'value_2', 'etc.' );

		// (Optional) If a selector is provided, editable will be delegated to the specified targets
		// $settings['js']['selector'] = 'a.my-class';

		// (Optional) Only applies to the type 'select2_dropdown'. Populates the available select2 dropdown values through ajax.
		// Ajax callback used is 'get_editable_ajax_options()'.
		// $settings['ajax_populate'] = true;

		return $settings;
	}

	/**
	 * Saves the value after using inline-edit
	 *
	 * @param int   $id    Object ID
	 * @param mixed $value Value to be saved
	 */
	public function save( $id, $value ) {

		// Store the value that has been entered with inline-edit
		// For example: update_post_meta( $id, '_my_custom_field_example', $value );

	}

}

/**
 * Sorting class. Adds sorting functionality to the column.
 */
class ACP_Sorting_Model_gpf extends \ACP\Sorting\Model {

	// This was optional and the function was removed because we want to sort by raw value only.
	
}



/**
 * Export class. Adds export functionality to the column.
 */
class ACP_Export_Model_gpf extends \ACP\Export\Model {

	public function get_value( $id ) {

		// Start editing here.

		// Add the value you would like to be exported.
		// For example: $value = get_post_meta( $id, '_my_custom_field_example', true );

		$value = $this->column->get_raw_value( $id );

		// Stop editing.

		return $value;
	}

}