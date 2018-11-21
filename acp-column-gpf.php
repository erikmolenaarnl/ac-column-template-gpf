<?php

/**
 * --------------------------------------------
 * This column is for the PRO version only.
 * This adds editing and sorting to the column.
 * --------------------------------------------
 */
class ACP_Column_gpf extends AC_Column_gpf
	implements \ACP\Editing\Editable, \ACP\Filtering\Filterable, \ACP\Sorting\Sortable, \ACP\Search\Searchable, \ACP\Export\Exportable {

	public function editing() {
		return new ACP_Editing_Model_gpf( $this );
	}

	public function filtering() {
		return new ACP_Filtering_Model_gpf( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model_gpf( $this );
	}

	public function search() {
		return new ACP_Search_Model_gpf( $this );
	}

	public function export() {
		return new ACP_Export_Model_gpf( $this );
	}

	/**
	 * Append custom GPF filter/search options to an existing array.
	 * @param  array  $options
	 * @return array
	 */
	public function add_wc_gpf_options( $options = array() ) {
		$options['__gpf_included'] = __( 'Included', 'ac-column-template-gpf' ); // Default Priority.
		$options['__gpf_excluded'] = __( 'Excluded', 'ac-column-template-gpf' );
		return $options;
	}

	/**
	 * Get the query for filter out excluded products.
	 * @param  array  $meta_query
	 * @param  bool   $reversed
	 * @return array
	 */
	public function get_wc_gpf_excluded_query( $meta_query = array(), $reversed = false ) {

		$gpf_query = array(
			'key'     => $this->get_wc_gpf_key(),
			'value'   => $this->get_wc_gpf_filter_value(),
			'compare' => $reversed ? 'LIKE' : 'NOT LIKE',
		);

		return array(
			'relation' => 'AND',
			$meta_query,
			$gpf_query,
		);
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
 * Filtering class. Adds filter functionality to the column.
 */
class ACP_Filtering_Model_gpf extends \ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$data = parent::get_filtering_data();
		$data['options'] = $this->column->add_wc_gpf_options( $data['options'] );
		return $data;
	}

	/**
	 * Uses the parent method and adds filters to check for the excluded status of a product.
	 * @inheritdoc
	 */
	public function get_filtering_vars( $vars ) {
		$val      = $this->get_filter_value();
		$reversed = false;

		switch ( $val ) {
			case '__gpf_included':
				$vars = array( 'meta_query' => array(
					'relation' => 'OR',
					array(
						'key'   => $this->column->get_meta_key(),
						'value' => '',
					),
					array(
						'key'     => $this->column->get_meta_key(),
						'compare' => 'NOT EXISTS',
					)
				) );
			break;
			case '__gpf_excluded':
				$reversed = true;
			break;
			default:
				$vars = parent::get_filtering_vars( $vars );
			break;
		}

		if ( ! isset( $vars['meta_query'] ) ) {
			return $vars;
		}

		$vars['meta_query'] = $this->column->get_wc_gpf_excluded_query( $vars['meta_query'], $reversed );

		return $vars;
	}

}


/**
 * Sorting class. Adds sorting functionality to the column.
 */
class ACP_Sorting_Model_gpf extends \ACP\Sorting\Model {

	// This was optional and the function was removed because we want to sort by raw value only.

}


use \ACP\Search\Operators;
use \ACP\Search\Value;
use \ACP\Search\Query\Bindings;
use \ACP\Helper\Select\Options;

/**
 * Searching class. Adds search functionality to the column.
 */
class ACP_Search_Model_gpf extends \ACP\Search\Comparison\Meta
	implements ACP\Search\Comparison\Values
{
	public $column = null;

	public function __construct( $column ) {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::NEQ,
		) );

		$this->column = $column;

		parent::__construct( $operators, $column->get_meta_key(), $column->get_meta_type() );
	}

	public function get_values() {
		$values = $this->column->filtering()->get_meta_values();
		$values = array_combine( $values, $values );
		$values = $this->column->add_wc_gpf_options( $values );
		return Options::create_from_array( $values );
	}

	/**
	 * @inheritDoc
	 */
	public function create_query_bindings( $operator, Value $value ) {
		$bindings   = new Bindings();
		$meta_query = array();
		$val        = ( $value instanceof Value ) ? $value->get_value() : $value;
		$reversed   = false;

		switch ( $val ) {
			case '__gpf_included':
				$value = new Value( '', 'string' );
				switch ( $operator ) {
					case '=':
						$operator = Operators::IS_EMPTY;
					break;
					case '!=':
						$operator = Operators::NOT_IS_EMPTY;
					break;
				}
				$meta_query = $this->get_meta_query( $operator, $value );
			break;
			case '__gpf_excluded':
				if ( '=' === $operator ) {
					$reversed = true;
				}
			break;
			default:
				$meta_query = $this->get_meta_query( $operator, $value );
			break;
		}

		$bindings->meta_query(
			$this->column->get_wc_gpf_excluded_query( $meta_query, $reversed )
		);

		return $bindings;
	}

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
