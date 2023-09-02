import $ from 'jquery';
import './page-classifieds';

// Handle send message.
$(function() {
	$( document ).on( 'click', '.ps-js-wpadverts-message', function( e ) {
		let $btn = $( e.currentTarget ),
			id = $btn.data( 'id' );

		e.preventDefault();
		e.stopPropagation();

		if ( window.ps_messages ) {
			ps_messages.new_message( id, false, $btn[0] );
		}
	});
});
