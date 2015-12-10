<?php

/**
 * Add custom options for address fields
 */
class GravityView_Field_Approval extends GravityView_Field {

	var $name = 'approval';

	var $is_searchable = false;

	var $is_sortable = true;

	var $group = 'gravityview';

	var $contexts = array( 'single', 'multiple' );

	public function __construct() {

		$this->label = esc_attr__( 'Approval', 'gravityview' );

		$this->description =  esc_attr__( 'Approve entries from the View. Requires users have `gravityview_moderate_entries` capability or higher.', 'gravityview' );

		parent::__construct();

		add_filter( 'gravityview_entry_default_fields', array( $this, 'filter_gravityview_entry_default_field' ), 10, 3 );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ) );

		add_action( 'gravityview/field/approval/load_scripts', array( $this, 'enqueue_and_localize_script' ) );

		add_filter( 'gravityview/common/sortable_fields', array( $this, 'add_approval_field_to_sort_list' ), 10, 2 );

		add_filter('gravityview_search_criteria', array( $this, 'enable_sorting_by_approval' ), 10, 4 );

	}

	/**
	 * Register the field approval script
	 * @since TODO
	 * @return void
	 */
	function register_script() {
		wp_register_script( 'gravityview-field-approval', GRAVITYVIEW_URL . 'assets/js/field-approval.js', array('jquery'), GravityView_Plugin::version, true);
		wp_register_style( 'gravityview-field-approval-css', GRAVITYVIEW_URL . 'assets/css/field-approval.css', GravityView_Plugin::version, true );
	}

	/**
	 * Get the strings used in the field approval field
	 * @since TODO
	 * @return array
	 */
	static public function get_strings() {

		/**
		 * @filter `gravityview/field/approval/text` Modify the text values used in field approval
		 * @param array $field_approval_text Array with `label_approve`, `label_disapprove`, `approve_title`, and `unapprove_title` keys.
		 * @since TODO
		 */
		$field_approval_text = apply_filters( 'gravityview/field/approval/text', array(
			'label_approve' => __( 'Approve', 'gravityview' ) ,
			'label_disapprove' => __( 'Disapprove', 'gravityview' ),
			'label_unapproved' => __( 'Unapproved', 'gravityview' ),
			'approve_title' => __( 'Entry not approved for directory viewing. Click to approve this entry.', 'gravityview'),
			'dissaprove_title' => __( 'Entry approved or unapproved for directory viewing. Click to disapprove this entry.', 'gravityview'),
			'unapprove_title' => __( 'Entry approved or unapproved for directory viewing. Click to reset approval status for this entry.', 'gravityview'),
		) );

		return $field_approval_text;
	}

	/**
	 * Register the field approval script and output the localized text JS variables
	 * @since TODO
	 * @return void
	 */
	function enqueue_and_localize_script() {

		// The script is already registered and enqueued
		if( wp_script_is( 'gravityview-field-approval', 'enqueued' ) ) {
			return;
		}

		wp_enqueue_script( 'gravityview-field-approval' );

		wp_enqueue_style( 'gravityview-field-approval-css' );

		$field_approval_text = self::get_strings();

		wp_localize_script( 'gravityview-field-approval', 'gvApproval', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'gravityview_ajaxgfentries'),
			'text' => array_map( 'esc_js', $field_approval_text ),
		));

	}

	/**
	 * Add Fields to the field list
	 * @param array $entry_default_fields Array of fields shown by default
	 * @param string|array $form form_ID or form object
	 * @param string $context  Either 'single', 'directory', 'header', 'footer'
	 *
	 * @return mixed
	 */
	public function filter_gravityview_entry_default_field( $entry_default_fields, $form, $context ) {

		if( !isset( $entry_default_fields[ "{$this->name}" ] ) ) {
			$entry_default_fields[ "{$this->name}" ] = array(
				'label' => $this->label,
				'desc'	=> $this->description,
				'type' => $this->name,
			);
		}

		return $entry_default_fields;
	}

	public function enable_sorting_by_approval( $criteria, $form_ids, $view_id ) {

		// If the search is being sorted
		if( ! empty( $criteria['sorting']['key'] ) ) {

			$criteria['sorting']['key'] = 'is_approved';

		}

		return $criteria;
	}


	/**
	 * Add the Approval Field to the Sort Options Select Box
	 * @param $fields
	 * @param $formid
	 * @return mixed
	 */
	public function add_approval_field_to_sort_list( $fields, $formid ){
		$approval_field = array(
			'label' => $this->label,
			'type' => $this->name
		);

		$fields['approval'] = $approval_field;

		return $fields;
	}

}

new GravityView_Field_Approval;
