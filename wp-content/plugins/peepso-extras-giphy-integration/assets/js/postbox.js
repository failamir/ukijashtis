import $ from 'jquery';
import _ from 'underscore';
import peepso, { observer } from 'peepso';
import Giphy from './giphy';

class PostboxGiphy {
	/**
	 * Initialize postbox GIPHY image selector dropdown.
	 *
	 * @param {JQuery} $postbox
	 */
	constructor( $postbox ) {
		this.$postbox = $postbox;
		this.$postboxTab = this.$postbox.$posttabs;
		this.$postboxStatusTextarea = this.$postbox.$textarea;
		this.$postboxStatus = this.$postboxStatusTextarea.closest( '.ps-postbox-status' );
		this.$postboxGiphy = this.$postbox.find( '.ps-postbox-tabs [data-tab-id=giphy]' );

		this.$preview = this.$postboxGiphy.find( '.ps-js-giphy-preview' ).hide();
		this.$selector = this.$postboxGiphy.find( '.ps-js-giphy-selector' );
		this.$loading = this.$selector.find( '.ps-js-giphy-loading' );
		this.$query = this.$selector.find( '.ps-js-giphy-query' );
		this.$result = this.$selector.find( '.ps-js-giphy-list' );
		this.$slider = this.$result.parent();

		// Throttle functions.
		this.onScroll = _.throttle( this.onScroll, 500 );

		this.$preview.on( 'click', '.ps-js-giphy-change', e => this.onChangeImage( e ) );
		this.$query.on( 'input', e => this.onInput( e ) );
		this.$result.on( 'click', 'img', e => this.onSelect( e.target ) );
		this.$slider.find( '.ps-js-giphy-nav-left' ).on( 'click', () => this.onScroll( 'left' ) );
		this.$slider.find( '.ps-js-giphy-nav-right' ).on( 'click', () => this.onScroll( 'right' ) );

		this.giphy = null;
		this.itemTemplate = peepso.template( this.$slider.find( '.ps-js-giphy-list-item' ).html() );

		this.$postboxTab.on( 'peepso_posttabs_show-giphy', $.proxy( this.show, this ) );
		this.$postboxTab.on( 'peepso_posttabs_cancel-giphy', $.proxy( this.cancel, this ) );
		this.$postboxTab.on( 'peepso_posttabs_submit-giphy', $.proxy( this.post, this ) );

		// Filters and actions.
		observer.addAction( 'postbox_type_set', $.proxy( this.actionPostboxTypeSet, this ), 10, 2 );
		observer.addFilter( 'peepso_postbox_can_submit', $.proxy( this.filterCanSubmit, this ), 10, 2 );
	}

	/**
	 * Switch UI to show giphy post type.
	 */
	show() {
		this.$postboxStatus.show();
		this.$postboxGiphy.show();

		if ( ! this.giphy ) {
			this.giphy = Giphy.getInstance();
			this.search();
		}
	}

	/**
	 * Cancel creating giphy post.
	 */
	cancel() {
		this.selectedImage = null;

		this.$postboxGiphy.hide();
		this.$preview.hide();
		this.$selector.show();

		this.$postbox.on_change();
	}

	/**
	 * Finalize creating giphy post.
	 */
	post() {
		let filterName = 'postbox_req_' + this.$postbox.guid;
		observer.addFilter( filterName, this.filterPostboxReq, 10, 1, this );
		this.$postbox.save_post();
		observer.removeFilter( filterName, this.filterPostboxReq, 10 );
	}

	/**
	 * Get selected date and time.
	 *
	 * @returns {string|undefined}
	 */
	value() {
		let value;

		if ( this.selectedImage ) {
			value = this.selectedImage;
		}

		return value;
	}

	/**
	 * Search for images based on a keyword.
	 *
	 * @param {string} [keyword]
	 */
	search( keyword = '' ) {
		this.$result.hide();
		this.$loading.show();

		clearTimeout( this.searchDelay );
		let searchDelay = ( this.searchDelay = setTimeout( () => {
			this.giphy.search( keyword ).done( data => {
				if ( this.searchDelay === searchDelay ) {
					this.render( data );
					this.$loading.hide();
					this.$result.show();
					this.$query.show();
				}
			} );
		}, 1000 ) );
	}

	/**
	 * Render search result.
	 *
	 * @param {Array.<Object>}
	 */
	render( data ) {
		let rendition = peepsogiphydata.giphy_rendition_comments || 'fixed_width';
		let html = data.map( item => {
			if ( item.images[ rendition ] ) {
				item.src = item.images[ rendition ].url;
				item.preview = item.images.preview_gif.url;
			}
			return this.itemTemplate( item );
		} );

		this.$result.html( html.join( '' ) );
	}

	/**
	 * Select an image.
	 *
	 * @param {string} srcPreview
	 * @param {string} srcActual
	 */
	select( srcPreview, srcActual ) {
		this.selectedImage = srcActual;

		this.$selector.hide();
		this.$preview.find( 'img' ).attr( 'src', srcPreview );
		this.$preview.show();

		this.$postbox.on_change();
	}

	/**
	 * Scroll image listing to the left/right.
	 *
	 * @param {string} direction
	 */
	scroll( direction ) {
		let isRTL = peepso.rtl,
			viewportWidth = this.$slider.width(),
			currentMargin = parseInt( this.$result.css( isRTL ? 'marginRight' : 'marginLeft' ) ) || 0,
			maxMargin;

		if ( direction === ( isRTL ? 'right' : 'left' ) ) {
			currentMargin = Math.min( currentMargin + viewportWidth, 0 );
		} else if ( direction === ( isRTL ? 'left' : 'right' ) ) {
			let $lastItem = this.$result.children( 'span' ).last();
			if ( isRTL ) {
				maxMargin = Math.abs( $lastItem.position().left );
			} else {
				maxMargin = $lastItem.position().left + $lastItem.width() - viewportWidth;
			}
			currentMargin -= Math.min( viewportWidth, maxMargin );
		}
		this.$result.css( isRTL ? 'marginRight' : 'marginLeft', currentMargin );
	}

	/**
	 * Handle change image.
	 *
	 * @param {Event} e
	 */
	onChangeImage( e ) {
		e.preventDefault();
		e.stopPropagation();

		this.selectedImage = null;
		this.$postbox.on_change();

		this.$preview.hide();
		this.$selector.show();
	}

	/**
	 * Handle query input.
	 *
	 * @param {Event} e
	 */
	onInput( e ) {
		let keyword = e.target.value;

		this.$result.hide();
		this.$loading.show();
		this.search( keyword.trim() );
	}

	/**
	 * Handle image scrolling event.
	 *
	 * @param {string} direction
	 */
	onScroll( direction ) {
		this.scroll( direction );
	}

	/**
	 * Handle select image.
	 *
	 * @param {Element} img
	 */
	onSelect( img ) {
		this.select( img.src, img.getAttribute( 'data-url' ) );
	}

	/**
	 * Filter hook for "postbox_req".
	 *
	 * @param {Object} params
	 * @returns {Object}
	 */
	filterPostboxReq( params ) {
		let value = this.value();
		if ( value ) {
			params.type = 'giphy';
			params.giphy = value;
		}

		return params;
	}

	/**
	 * Filter hook for "peepso_postbox_can_submit".
	 *
	 * @param {Object} flags
	 * @param {JQuery} $postbox
	 * @returns {Object}
	 */
	filterCanSubmit( flags, $postbox ) {
		if ( this.$postbox === $postbox && this.$postboxTab.current_tab_id === 'giphy' ) {
			flags.hard.push( !! this.value() );
		}
		return flags;
	}

	/**
	 * Action hook for "postbox_type_set".
	 *
	 * @param {JQuery} $postbox
	 * @param {string} type
	 */
	actionPostboxTypeSet( $postbox, type ) {
		if ( $postbox === this.$postbox ) {
			if ( type === 'giphy' && this.$postboxTab.current_tab_id !== 'giphy' ) {
				this.$postbox.find( '[data-tab=giphy]' ).trigger( 'click' );
			}
		}
	}
}

// Initialize class on main postbox initialization.
observer.addAction(
	'peepso_postbox_addons',
	addons => {
		let wrapper = {
			init() {},
			set_postbox( $postbox ) {
				if ( $postbox.find( '[data-tab-id=giphy]' ).length ) {
					new PostboxGiphy( $postbox );
				}
			}
		};
		addons.push( wrapper );
		return addons;
	},
	10,
	1
);
