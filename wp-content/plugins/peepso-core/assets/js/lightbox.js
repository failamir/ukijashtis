import { hooks } from 'peepso';
import imagesLoaded from 'imagesloaded';
import Swipe from 'swipejs';

(function ($, Swipe, factory) {
	var PsLightbox = factory($, Swipe),
		instance = new PsLightbox();

	// Open comment section on page load when the URL contains comment data.
	var showAttachmentOnLoad = !!window.location.href.match(/#.*comment(\.\d+)+/);
	$(function () {
		// Auto disable if the lightbox function is not immediately on page load.
		setTimeout(function () {
			showAttachmentOnLoad = false;
		}, 6000);
	});

	peepso.lightbox = function (data, options) {
		if (data === 'close') {
			instance.close();
			return;
		}

		if (typeof data !== 'function') {
			if (showAttachmentOnLoad) {
				options = options || {};
				options.showAttachment = true;
				showAttachmentOnLoad = false;
			}

			instance.open(data, options || {});
			return;
		}

		instance.options = options || {};
		instance.init();
		instance.showLoading();
		data(function (data, options) {
			if (showAttachmentOnLoad) {
				options = options || {};
				options.showAttachment = true;
				showAttachmentOnLoad = false;
			}

			instance.hideLoading();
			instance.open(data, options || {});
		});
	};

	peepso.simple_lightbox = function (src) {
		peepso.lightbox(
			[{ content: '<div style="display:inline-block;"><img src="' + src + '" /></div>' }],
			{
				simple: true
			}
		);
	};
})(jQuery || $, Swipe, function ($, Swipe) {
	var TEMPLATE = peepsolightboxdata.template.replace(/>\s+</g, '><');
	var clsDataOpened = 'ps-lightbox-data--opened';
	var clsCloseInvert = 'ps-lightbox-close--invert';

	function PsLightbox() {}

	PsLightbox.prototype = {
		init: function () {
			if (!this.$container) {
				this.$container = $(TEMPLATE);
				this.$padding = this.$container.find('.ps-lightbox-padding');
				this.$wrapper = this.$container.find('.ps-lightbox-wrapper');
				this.$close = this.$container.find('.ps-lightbox-close');
				this.$object = this.$container.find('.ps-lightbox-object');
				this.$prev = this.$container.find('.ps-lightbox-arrow-prev');
				this.$next = this.$container.find('.ps-lightbox-arrow-next');
				this.$spinner = this.$container.find('.ps-lightbox-spinner');
				this.$attachment = this.$container.find('.ps-lightbox-data');
				this.$btnattachment = this.$container.find('.ps-lightbox-data-toggle');
				this.$imagelink = this.$container.find('.ps-lightbox-imagelink');

				this.attachevents();

				this.$container.appendTo(document.body);
			}

			// disable zooming on mobile
			if (this.isMobile()) {
				if (!this.$viewport) {
					this.$viewport = $('meta[name=viewport]');
					if (!this.$viewport.length) {
						this.$viewport = $('<meta name="viewport" content="" />').appendTo('head');
					}
				}

				this.vpNoZoom = 'width=device-width, user-scalable=no';
				if (!this.vpValue) {
					this.vpValue = this.$viewport.attr('content');
					this.$viewport.attr('content', this.vpNoZoom);
				}
			}

			// check if we want to show simple lightbox
			if (this.options.simple) {
				this.$container.addClass('ps-lightbox-simple');
			} else {
				this.$container.removeClass('ps-lightbox-simple');
			}

			this.$prev.hide();
			this.$next.hide();
			this.$container.show();

			// set height on for simple lightbox
			this.resetHeight();
			if (this.options.simple) {
				this.setHeight();
			}

			// attach event
			var $win = $(window);
			$win.off('resize.ps-lightbox');
			if (this.options.simple) {
				$win.on('resize.ps-lightbox', $.proxy(this.setHeight, this));
			}

			// close on Esc key
			var $doc = $(document.body);
			$doc.off('keyup.ps-lightbox').on(
				'keyup.ps-lightbox',
				$.proxy(function (e) {
					if (e.keyCode === 27) {
						this.close();
					}
				}, this)
			);
		},

		open: function (data, options) {
			this.data = data || [];
			this.options = options || {};
			this.index = options.index || 0;

			this.init();

			// add tag to identify the content type
			this.$container.removeClass('ps-lightbox--' + this.$container.attr('data-type'));
			this.$object.removeClass('ps-lightbox__object--' + this.$object.attr('data-type'));
			if (this.options.type) {
				this.$container.addClass('ps-lightbox--' + this.options.type);
				this.$object.addClass('ps-lightbox__object--' + this.options.type);
				this.$container.attr('data-type', this.options.type);
				this.$object.attr('data-type', this.options.type);
			} else {
				this.$container.removeAttr('data-type');
				this.$object.removeAttr('data-type');
			}

			// populate item placeholders.
			if (!this.options.simple) {
				var $swipe = $(`
					<div class="ps-lightbox__object-inner">
						<div class="ps-lightbox__object-container"></div>
					</div>
				`);

				_.each(this.data, function (item, index) {
					$swipe.children().append(`
					<div class="ps-lightbox__object-item ps-js-item" data-spinner="1">
						<div class="ps-lightbox__spinner ps-lightbox-spinner" style="display:block;"></div>
					</div>
				`);
				});

				this.$object.empty();
				this.$object.append($swipe);
				this.mySwipe = Swipe($swipe[0], {
					draggable: true,
					startSlide: this.index,
					callback: $.proxy(function (index, elem) {
						this.go(index);
					}, this)
				});
			}

			this.togglenav();
			this.go(this.index);

			if (this.options.showAttachment) {
				var currWidth = window.innerWidth || document.documentElement.clientWidth,
					maxMobileWidth = 979;

				delete this.options.showAttachment;

				if (currWidth <= maxMobileWidth) {
					this.showAttachment();
				} else {
					this.hideAttachment();
				}
			} else {
				this.hideAttachment();
			}

			// add body style
			$('body').addClass('ps-lightbox--open');
		},

		close: function (e) {
			if (!this.$container) {
				return;
			}

			this.$container.hide();
			this.$object.empty();
			this.$attachment.empty();

			// reset zooming on mobile
			if (this.isMobile()) {
				if (this.vpValue) {
					this.$viewport.attr('content', this.vpValue);
					this.vpValue = false;
				}
			}

			// detach event
			$(window).off('resize.ps-lightbox');
			$(document.body).off('keyup.ps-lightbox');

			// remove body style
			$('body').removeClass('ps-lightbox--open');
		},

		go: function (index) {
			var html, img, src;

			if (typeof this.options.beforechange === 'function') {
				this.options.beforechange(this);
			}

			if (this.data[index]) {
				// save current state
				if (this.data[this.index]) {
					html = this.$attachment.html().trim();
					if (html) {
						this.data[this.index].attachment = html;
					}
				}

				// update content
				this.index = index;
				this.$attachment.html(this.data[this.index].attachment || '');

				if (!this.options.simple) {
					var $item = this.$object.find('.ps-js-item').eq(this.index);

					// Fix 2 items continuous bug.
					// https://github.com/lyfeyaj/swipe/issues/19
					if (this.mySwipe.getNumSlides() === 2) {
						$item = $item.add(this.$object.find('.ps-js-item').eq(this.index + 2));
					}

					if ($item.data('spinner')) {
						$item.removeData('spinner');
						$item.removeAttr('data-spinner');

						let html = this.data[this.index].content,
							$img = $('<div />').append(html).children('img');

						if (!$img.length) {
							$item.html(html);
						} else {
							imagesLoaded($img[0], () => {
								$item.html(html);
							});
						}
					}

					this.$btnattachment.show();
				} else {
					this.$object.html(this.data[this.index].content);

					img = this.$object.find('img');
					if (img.length) {
						this.showLoading();
						this.$close.hide();
						img.on(
							'load',
							$.proxy(function () {
								this.hideLoading();
								this.$close.show();
								this.resetHeight();
								this.setHeight.apply(this);
							}, this)
						);
						// update link
						src = img
							.attr('src')
							.replace('/thumbs/', '/')
							.replace(/_(l|m_s|s_s)\./, '.');
						this.$imagelink.find('a').attr('href', src);
					}
					if (this.options.nofulllink) {
						this.$imagelink.hide();
					} else {
						this.$imagelink.show();
					}

					this.$btnattachment.hide();
				}
			}

			if (typeof this.options.afterchange === 'function') {
				this.options.afterchange(this);
			}

			$(document).trigger('ps_lightbox_navigate');
		},

		prev: function () {
			if ('function' === typeof this.options.prev) {
				// Custom navigation function is expected to open a new lightbox instance.
				this.close();
				this.options.prev();
			} else {
				this.mySwipe.prev();
			}
		},

		next: function () {
			if ('function' === typeof this.options.next) {
				// Custom navigation function is expected to open a new lightbox instance.
				this.close();
				this.options.next();
			} else {
				this.mySwipe.next();
			}
		},

		togglenav: function () {
			var $navs = this.$prev.add(this.$next);

			// detach navigation events.
			this.$container.off('click.ps-lightbox', '.ps-lightbox-arrow-prev');
			this.$container.off('click.ps-lightbox', '.ps-lightbox-arrow-next');
			$(window).off('keyup.ps-lightbox');

			var hidenav = false;

			if (this.options.nonav) {
				if ('function' === typeof this.options.nonav) {
					hidenav = this.options.nonav();
				} else {
					hidenav = this.options.nonav;
				}
			} else if (!this.data.length || this.data.length <= 1) {
				hidenav = true;
			}

			if (hidenav) {
				$navs.hide();
				return;
			}

			$navs.show();

			// attach mouse navigation events.
			this.$container.on(
				'click.ps-lightbox',
				'.ps-lightbox-arrow-prev',
				$.proxy(this.prev, this)
			);
			this.$container.on(
				'click.ps-lightbox',
				'.ps-lightbox-arrow-next',
				$.proxy(this.next, this)
			);

			// attach keyboard navigation events.
			$(window).on(
				'keyup.ps-lightbox',
				$.proxy(function (e) {
					var key = e.keyCode;
					if (key === 37) {
						this.prev();
					} else if (key === 39) {
						this.next();
					}
				}, this)
			);
		},

		isAttachmentOpened: function () {
			return this.$attachment.hasClass(clsDataOpened);
		},

		showAttachment: function () {
			this.$attachment.addClass(clsDataOpened);
			this.$close.addClass(clsCloseInvert);

			this.toggleMobileView();
			hooks.addAction('browser_resize', 'lightbox', () => this.toggleMobileView());
		},

		hideAttachment: function () {
			this.$attachment.removeClass(clsDataOpened);
			this.$close.removeClass(clsCloseInvert);

			hooks.removeAction('browser_resize', 'lightbox');
			this.toggleMobileView();
		},

		showLoading: function () {
			this.$object.hide();
			this.$spinner.show();
		},

		hideLoading: function () {
			this.$spinner.hide();
			this.$object.show();
		},

		attachevents: function () {
			// attach close popup handler.
			this.$container.on(
				'click.ps-lightbox',
				'.ps-lightbox-padding',
				$.proxy(function (e) {
					if (e.target === e.currentTarget) {
						e.stopPropagation();
						this.close();
					}
				}, this)
			);

			// attach toggle attachment button
			this.$btnattachment.on(
				'click',
				$.proxy(function () {
					if (this.isAttachmentOpened()) {
						this.hideAttachment();
					} else {
						this.showAttachment();
					}
				}, this)
			);

			// attach close button handler
			this.$close.on(
				'click',
				$.proxy(function () {
					if (this.isAttachmentOpened()) {
						this.hideAttachment();
					} else {
						this.close();
					}
				}, this)
			);

			// attach close on view full image
			this.$imagelink.on(
				'click',
				'a',
				$.proxy(function () {
					this.close();
				}, this)
			);

			// do not navigate lightbox items when pressing arrow inside attachment
			this.$attachment.on('keyup.ps-lightbox', function (e) {
				e.stopPropagation();
			});
		},

		isMobile: function () {
			var mobile = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i;
			var isMobile = mobile.test(navigator.userAgent);

			this.isMobile = isMobile
				? function () {
						return true;
				  }
				: function () {
						return false;
				  };

			return isMobile;
		},

		resetHeight: function () {
			this.$object.find('img').css('maxHeight', '');
		},

		setHeight: _.debounce(function () {
			this.$object.find('img').css('maxHeight', this.$wrapper.height());
		}, 100),

		toggleMobileView: function () {
			var currWidth = window.innerWidth || document.documentElement.clientWidth,
				maxWidth = 979,
				clsAttachmentOpened = 'ps-lightbox--comments';

			if (currWidth <= maxWidth) {
				this.isAttachmentOpened()
					? this.$container.addClass(clsAttachmentOpened)
					: this.$container.removeClass(clsAttachmentOpened);
			} else {
				this.$container.removeClass(clsAttachmentOpened);
			}
		}
	};

	return PsLightbox;
});
