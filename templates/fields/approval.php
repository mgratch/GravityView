<?php
/**
 * Approval field output
 *
 * @package GravityView
 * @subpackage GravityView/templates/fields
 *
 */

/**
 * @action `gravityview/field/approval/load_scripts` Trigger loading the field approval javascript
 * @see GravityView_Field_Approval::enqueue_and_localize_script
 * @since TODO
 */

do_action( 'gravityview/field/approval/load_scripts' );

global $gravityview_view;

$entry = GravityView_View::getInstance()->getCurrentEntry();

if (empty($entry)){
	$entry = $gravityview_view->_current_field['entry'];
}

$approved = gform_get_meta( $entry['id'], 'is_approved' );

$strings = GravityView_Field_Approval::get_strings();

if( !empty($approved) && $approved == 'Approved' ) {
	$anchor = $strings['label_approve'];
	$title = $strings['approve_title'];
	$class = ' entry_approved';
} elseif (isset($approved) && $approved === "0") {
	$title = $strings['dissaprove_title'];
	$anchor = $strings['label_disapprove'];
	$class = '';
} else {
	$title = $strings['unapprove_title'];
	$anchor = $strings['label_unapproved'];
	$class = '';
}

?>
<a href="#" class="toggleApproved<?php echo $class; ?>" title="<?php echo $title; ?>" data-approved-status="<?php echo $approved; ?>" data-entry-id="<?php echo esc_attr( $entry['id'] ); ?>" data-form-id="<?php echo esc_attr( $entry['form_id'] ); ?>"><?php echo $anchor; ?></a>