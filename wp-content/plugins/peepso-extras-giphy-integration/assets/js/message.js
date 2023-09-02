(function(root, factory) {
	var moduleName = 'PsGiphyMessage';
	var moduleObject = factory(moduleName, root.jQuery, require('./giphy.js'));

	// export module
	if (typeof module === 'object' && module.exports) {
		module.exports = moduleObject;
	} else {
		root[moduleName] = moduleObject;
	}

	// auto-initialize class
	new moduleObject();
})(window, function(moduleName, $, PsGiphy) {
	return peepso.createClass(moduleName, {
		/**
		 * Class constructor.
		 */
		__constructor: function() {
			$($.proxy(this.onDocumentLoaded, this));
		},

		/**
		 *
		 */
		onDocumentLoaded: function() {
			$(document).on('click', '.ps-chat-window .ps-js-giphy-trigger', $.proxy(this.onToggle, this));
		},

		/**
		 * Toggle giphy image selector chat input.
		 * @param {HTMLEvent} e
		 */
		onToggle: function(e) {
			var $btn = $(e.currentTarget),
				$container = $btn.siblings('.ps-js-giphy-container'),
				dataInitialized = 'ps-giphy-initialized',
				dataLoading = 'ps-giphy-loading',
				$input,
				keyword;

			e.preventDefault();
			e.stopPropagation();

			if ($container.is(':visible')) {
				$container.hide();
				return;
			}

			$container.show();
			if ($container.data(dataInitialized) || $container.data(dataLoading)) {
				return;
			}

			$container.data(dataLoading, true);
			$container.on('input', '.ps-js-giphy-query', $.proxy(this.onSearch, this));
			$container.on('click', '.ps-js-giphy-list img', $.proxy(this.onSelectImage, this));

			this.search($container).done(
				$.proxy(function() {
					$container.find('.ps-js-giphy-query').show();
					$container.data(dataInitialized, true);
					$container.removeData(dataLoading);
				}, this)
			);
		},

		/**
		 * Search giphy images based on keyword.
		 * @param {jQuery} $container
		 * @param {string} [keyword]
		 */
		search: function($container, keyword) {
			return $.Deferred(
				$.proxy(function(defer) {
					var giphy = PsGiphy.getInstance(),
						$loading = $container.find('.ps-js-giphy-loading').show(),
						$list = $container.find('.ps-js-giphy-list').hide();

					giphy.search(keyword).done(
						$.proxy(function(data) {
							this.render($container, data);
							$loading.hide();
							$list.show();
							defer.resolveWith(this);
						}, this)
					);
				}, this)
			);
		},

		/**
		 * Renders gif images into specified container.
		 * @param {jQuery} $container
		 * @param {object[]} data
		 */
		render: function($container, data) {
			var $list = $container.find('.ps-js-giphy-list'),
				$item = $container.find('.ps-js-giphy-list-item'),
				template = peepso.template($item.html()),
				rendition = peepsogiphydata.giphy_rendition_messages || 'fixed_width',
				html;

			html = _.map(data, function(item, index) {
				if (item.images[rendition]) {
					$.extend(item, {
						src: item.images[rendition].url,
						preview: item.images.preview_gif.url
					});
				}
				return template(item);
			});

			$list.html(html.join(''));
		},

		/**
		 * Handle user search event.
		 * @param {HTMLEvent} e
		 */
		onSearch: function(e) {
			var $input = $(e.currentTarget),
				$container = $input.closest('.ps-js-giphy-container'),
				$list = $container.find('.ps-js-giphy-list'),
				$loading = $container.find('.ps-js-giphy-loading');

			$list.hide();
			$loading.show();
			this._onSearch($container, $input);
		},

		_onSearch: _.debounce(function($container, $input) {
			this.search($container, $.trim($input.val()));
		}, 1000),

		/**
		 * Handle user select image event.
		 * @param {HTMLEvent} e
		 */
		onSelectImage: function(e) {
			var $img = $(e.currentTarget),
				$container = $img.closest('.ps-js-giphy-container'),
				id = $container.closest('.ps-chat-window').data('id'),
				src = $img.attr('data-url');

			$container.hide();
			this.post(id, src);
		},

		/**
		 * Post Giphy image as message once selected.
		 * @param {number} id
		 * @param {string} src
		 */
		post: function(id, src) {
			peepso.observer.doAction('msgso_send_message', id, '', {
				type: 'giphy',
				giphy: src
			});
		}
	});
});
