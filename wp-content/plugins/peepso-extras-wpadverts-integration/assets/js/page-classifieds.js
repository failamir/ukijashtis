(function( $, factory ) {

	var PsPageClassifieds = factory( $ );
	var ps_page_classifieds = new PsPageClassifieds('.ps-js-classifieds');

})( jQuery, function( $ ) {

function PsPageClassifieds() {
	if ( PsPageClassifieds.super_.apply( this, arguments ) ) {
		$( $.proxy( this.init_page, this ) );
	}
}

// inherit from `PsPageAutoload`
peepso.npm.inherits( PsPageClassifieds, PsPageAutoload );

peepso.npm.objectAssign( PsPageClassifieds.prototype, {

	init_page: function() {
		this._search_$query = $('.ps-js-classifieds-query').on('input', $.proxy( this._filter, this ));
		this._search_$location = $('.ps-js-classifieds-location').on('input', $.proxy( this._filter, this ));
		this._search_$order = $('.ps-js-classifieds-sortby').on('change', $.proxy( this._filter, this ));
		this._search_$category = $('.ps-js-classifieds-category').on('change', $.proxy( this._filter, this ));

		// exit if container is not found
		if ( ! this._search_$ct.length ) {
			return this;
		}

		// toggle search filter form
		$('.ps-form-search-opt').on('click', $.proxy( this._toggle, this ));

		// delete confirmation
		this._search_$ct.on('click', '.ps-link--delete', $.proxy( this._delete, this ));
		this._search_params.user_id = +peepsodata.userid || undefined;
		this._search_params.category = this._search_$category.val() || 0;
		this._filter();
	},

	_search_url: 'classifiedsajax.search',

	_search_params: {
		uid: peepsodata.currentuserid,
		user_id: undefined,
		query: '',
		location: '',
		category: 0,
		order_by: undefined,
		order: undefined,
		page: 1
	},

	_search_render_html: function( data ) {
		var itemTemplate = peepso.template( peepsowpadvertsdata.listItemTemplate ),
			query = this._search_params.query,
			html = '',
			classifiedData, reQuery, highlight, i;

		if ( data.classifieds && data.classifieds.length ) {
			for ( i = 0; i < data.classifieds.length; i++ ) {
				classifiedData = data.classifieds[ i ];

				// Decode html entities on description.
				classifiedData.content = $('<div/>').html( classifiedData.content || '' ).text();

				// Highlight keyword found in title and description.
				if ( query ) {
					reQuery = _.filter( query.split(' '), function( str ) { return str !== '' });
					reQuery = RegExp('(' + reQuery.join('|') + ')', 'ig');
					highlight = '<span style="background:' + peepso.getLinkColor(0.3) +  '">$1</span>';
					classifiedData.title = ( classifiedData.title || '' ).replace( reQuery, highlight );
					classifiedData.content = ( classifiedData.content || '' ).replace( reQuery, highlight );
					if ( classifiedData.location ) {
						classifiedData.location = classifiedData.location.replace( reQuery, highlight );
					}
				}

				html += itemTemplate( $.extend( {}, classifiedData ) );
			}
		}

		return html;
	},

	_search_get_items: function() {
		return this._search_$ct.children('.ps-classified__item-wrapper');
	},

	/**
	 * @param {object} params
	 * @returns jQuery.Deferred
	 */
	_fetch: function( params ) {
		return $.Deferred( $.proxy(function( defer ) {

			// Multiply limit value by 2 which translate to 2 rows each call.
			params = $.extend({}, params );
			if ( ! _.isUndefined( params.limit ) ) {
				params.limit *= 2;
			}

			this._fetch_xhr && this._fetch_xhr.abort();
			this._fetch_xhr = peepso.disableAuth().disableError().getJson( this._search_url, params, $.proxy(function( response ) {
				if ( response.success ) {
					defer.resolveWith( this, [ response.data ]);
				} else {
					defer.rejectWith( this, [ response.errors ]);
				}
			}, this ));
		}, this ));
	},

	/**
	 * Filter search based on selected elements.
	 */
	_filter: function() {
		var order = ( this._search_$order.val() || '' ).split('|');

		// abort current request
		this._fetch_xhr && this._fetch_xhr.abort();

		this._search_params.query = $.trim( this._search_$query.val() );
		this._search_params.location = $.trim( this._search_$location.val() );
		this._search_params.category = this._search_$category.val();
		this._search_params.order_by = order[0] || undefined;
		this._search_params.order = order[1] || undefined;
		this._search_params.page = 1;
		this._search();
	},

	/**
	 * Toggle search filter form.
	 */
	_toggle: function() {
		$('.ps-js-page-filters').stop().slideToggle();
	},

	/**
	 *
	 * Toggle more information.
	 */
	_delete: function( e ) {
		var $btn = $( e.currentTarget ),
			$action = $btn.closest('.ps-js-action'),
			$confirm = $action.siblings('.ps-js-delete-confirm'),
			url = $btn.data('url'),
			id = $btn.data('id'),
			nonce = $btn.data('nonce');

		e.preventDefault();
		e.stopPropagation();
		$action.hide();
		$confirm.show();

		$confirm.off('click');
		$confirm.one('click', '.ps-js-delete-no, .ps-js-delete-yes', function( e ) {
			var $btn = $( e.currentTarget ),
				isConfirm = $btn.hasClass('ps-js-delete-yes');

			e.preventDefault();
			e.stopPropagation();

			// Canceled.
			if ( ! isConfirm ) {
				$confirm.hide();
				$action.show();
				return;
			}

			// Show spinner.
			$btn.siblings('.ps-icon-trash').hide();
			$btn.siblings('.ps-icon-spinner').show();

			// Do ajax remove classified.
			$.ajax({
				url: url,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'adverts_delete',
					id: id,
					ajax: '1',
					_ajax_nonce: nonce
				}
			}).done(function( json ) {
				$btn.closest('.ps-js-classifieds-item').remove();
			});
		});
	}

});

return PsPageClassifieds;

});
