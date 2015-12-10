/**
 * Javascript for Entry Approval
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.0
 *
 * globals jQuery, gvGlobals, ajaxurl
 */
(function( $ ) {

	"use strict";

	var self = {
		'response': { 'status': '' }
	}, approval_dd, maybeDT;

	self.init = function(){
		$('body').append('<ul id="gvdt-dropdown" class="dropdown">' +
				'<li><a href="#" data-status="" title="'+gvApproval.text.unapprove_title+'" > '+gvApproval.text.label_unapproved+'</a></li>' +
				'<li><a href="#" data-status="Approved" title="'+gvApproval.text.approve_title+'" > '+gvApproval.text.label_approve+'</a></li>' +
				'<li><a href="#" data-status="0" title="'+gvApproval.text.dissaprove_title+'" > '+gvApproval.text.label_disapprove+'</a></li>' +
				'</ul>');

		approval_dd = $("#gvdt-dropdown");
		maybeDT = $('.gv-datatables');

		self.dtCheck( maybeDT );

		$(approval_dd).on('click','li', function( evt ){
			self.select_approval( evt );
			evt.stopPropagation();
			return false;
		});

		/**
		 * Hide the appoval drop down on an exit click
 		 */
		$(document).on('click', function( evt ){
			if ($(evt.target).hasClass('toggleApproved')){
				return;
			} else if ($(evt.target).prop('id') === 'gvdt-dropdown'){
				return;
			}
			var elem = $(".active");
			$( elem ).removeClass('active');
		});
	};

	/**
	 * Check if the DataTables Extension is in use
	 * @param maybeDT
     */
	self.dtCheck = function( maybeDT ){

		if (maybeDT.length !== 0){
			$(maybeDT).on( 'draw.dt', function () {
				$( '.toggleApproved' ).on( 'click', function( e ) {
					self.toggle_approval(e);
				});
			});
		} else {
			$( '.toggleApproved' ).on( 'click', function( e ) {
				self.toggle_approval(e);
			});
		}
	};

	/**
	 * Toggle a specific entry
	 *
	 * @param e The clicked entry event object
	 * @returns {boolean}
	 */
	self.toggle_approval = function ( e ) {
		e.preventDefault();

		var el = e.target,
			entry_id = $( el ).attr('data-entry-id'),
			form_id = $( el ).attr('data-form-id');

		$( el ).parent().addClass('active');
		$( el ).after(approval_dd).addClass('active');

	};

	/**
	 * Select a status for the currently selected entry
	 * @param evt
	 * @returns {boolean}
     */
	self.select_approval = function( evt ){

		var el = $( evt.target ).parents('#gvdt-dropdown').prev(),
			set_approved = $( evt.target ).attr('data-status'),
			entry_id = $( el ).attr('data-entry-id'),
			form_id = $( el ).attr('data-form-id');

		$( el ).addClass( 'loading' );
		self.update_approval( entry_id, form_id, set_approved, el );
		evt.stopPropagation();
		return false;
	};


	/**
	 * Update an entry status via AJAX
	 */
	self.update_approval = function ( entry_id, form_id, set_approved, $target ) {
		
		var data = {
			action: 'gv_update_approved',
			entry_id: entry_id,
			form_id: form_id,
			approved: set_approved,
			nonce: gvApproval.nonce
		};

		$.post( gvApproval.ajaxurl, data, function ( response ) {
			if ( response ) {
				self.response = $.parseJSON( response );
				if( 'Approved' === self.response.status ) {
					$target.attr( 'data-approved-status', 'Approved' )
							.prop( 'title', gvApproval.text.approve_title )
							.text( gvApproval.text.label_approve )
							.addClass( 'entry_approved' );
				} else if ('0' === self.response.status){
					$target.attr( 'data-approved-status', '0' )
							.prop( 'title', gvApproval.text.disapprove_title )
							.text( gvApproval.text.label_disapprove )
							.removeClass( 'entry_approved' );
				} else {
					$target.attr( 'data-approved-status', '' )
							.prop( 'title', gvApproval.text.unapprove_title )
							.text( gvApproval.text.label_unapproved )
							.removeClass( 'entry_approved' );
				}
				$target.removeClass( 'loading active' );
				$target.parent().removeClass('active');
			}
		});

		return true;
	};

	self.init();

} (jQuery) );
