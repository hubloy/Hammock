<?php
namespace HubloyMembership\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Base Model
 * All models extend this class
 *
 * @since 1.0.0
 *
 * @package JP
 */
class Model extends Component {

	/**
	 * ID of the model object.
	 *
	 * @since  1.0.0
	 *
	 * @var int|string
	 */
	protected $id;

	/**
	 * Model name.
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	protected $name;


	/**
	 * An array containing the serialized array which is stored in the DB.
	 *
	 * This data can be used to determine which fields have been changed since
	 * the object was loaded from DB.
	 *
	 * This field is populated by Soko_Base_Core::populate()
	 *
	 * @var array
	 */
	public $_saved_data = array();

	/**
	 * Model Contstuctor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->on_create();
		/**
		 * Actions to execute when constructing the parent Model.
		 *
		 * @since  1.0.0
		 * @param object $this The Model object.
		 */
		do_action( 'hubloy-membership_base_model_construct', $this );
	}

	/**
	 * Called in construct
	 */
	public function on_create() {

	}

	/**
	 * Set field value, bypassing the __set validation.
	 *
	 * Used for loading from db.
	 *
	 * @since  1.0.0
	 *
	 * @param string $field
	 * @param mixed  $value
	 */
	public function set_field( $field, $value ) {
		// Don't deserialize values of "private" fields.
		if ( '_' !== $field[0] ) {

			// Only set values of existing fields, don't create a new field.
			if ( property_exists( $this, $field ) ) {
				$this->$field = $value;
			}
		}
	}

	/**
	 * Resets the fields value to the value that is stored in the DB.
	 *
	 * @since  1.0.1.0
	 *
	 * @param  string $field Name of the field.
	 * @return mixed The reset value.
	 */
	public function reset_field( $field ) {
		$result = null;

		// Don't modify values of "private" fields.
		if ( '_' !== $field[0] ) {

			// Only reset values of existing fields.
			if ( property_exists( $this, $field )
				&& isset( $this->_saved_data[ $field ] )
			) {
				$this->$field = $this->_saved_data[ $field ];
				$result       = $this->$field;
			}
		}

		return $result;
	}

	/**
	 * Called before saving model.
	 *
	 * @since  1.0.0
	 */
	public function before_save() {
		do_action( 'hubloy-membership_base_model_before_save', $this );
	}

	/**
	 * Abstract method to save model data.
	 *
	 * @since  1.0.0
	 */
	public function save() {
		throw new Exception( 'Method to be implemented in child class' );
	}

	/**
	 * Called after saving model data.
	 *
	 * @since  1.0.0
	 */
	public function after_save() {
		do_action( 'hubloy-membership_base_model_after_save', $this );
	}

	/**
	 * Get object properties.
	 *
	 * @since  1.0.0
	 *
	 * @return array of fields.
	 */
	public function get_object_vars() {
		return get_object_vars( $this );
	}
}

