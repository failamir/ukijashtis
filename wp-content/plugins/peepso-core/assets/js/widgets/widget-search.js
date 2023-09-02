(function ($, debounce, modules, site_url, rest_url, data) {
	const REST_URL = rest_url;
	const SITE_URL = site_url;
	const SHOW_IMAGES = +data.show_images;
	const SHOW_EMPTY_SECTIONS = +data.show_empty_sections;
	const TEXT_NO_RESULTS = data.text_no_results;

	let uniqueId = 0;

	class WidgetSearch {
		constructor(element) {
			this.uniqueId = ++uniqueId;

			this.$element = $(element);
			this.$query = this.$element.find('.ps-js-query');
			this.$loading = this.$element.find('.ps-js-loading').hide();
			this.$results = this.$element.find('.ps-js-result').hide();

			this.$query.on('input', e => {
				let query = this.$query.val().trim();

				// Do not search on empty query.
				if (query) {
					this.$results.hide();
					this.$loading.show();
					this.searchWithDelay();
				} else {
					this.$results.hide();
					this.$loading.hide();
				}
			});

			// Automatically trigger search if input is not empty.
			if (this.$query.val().trim()) {
				this.$query.triggerHandler('input');
			}
		}

		/**
		 * Calls AJAX search endpoint based on the current query string.
		 */
		search() {
			let query = this.$query.val().trim(),
				endpoint = `${REST_URL}search`,
				endpoint_id = `${endpoint}_widget_${this.uniqueId}`,
				params = { query },
				transport;

			// Do not search on empty query.
			if (!query) {
				this.$loading.hide();
				this.$results.hide();
				return;
			}

			transport = modules.request.get(endpoint_id, endpoint, params);
			transport
				.then(json => {
					this.render(json);
					this.$loading.hide();
					this.$results.show();
				})
				.catch(() => {
					this.$loading.hide();
				});
		}

		/**
		 * Delayed search to be used with the input event.
		 */
		searchWithDelay() {
			// Replace the function with the debounced one on first call.
			this.searchWithDelay = debounce(this.search, 1000);
			this.searchWithDelay();
		}

		/**
		 * Render search result.
		 *
		 * @param {Object} data
		 */
		render(data) {
			let results = data.results,
				sections = data.meta.sections,
				image_size = 100,
				has_result = !!SHOW_EMPTY_SECTIONS,
				html = '';

			if (results) {
				for (const key in results) {
					const section = sections[key];

					html += `<div data-type="${key}">`;
					html += `<div><h3><a href="${section.url}">${section.title}</a></h3></div>`;

					if (results.hasOwnProperty(key) && results[key].length) {
						html += `<ul style="padding: 0px 0px 30px 10px; margin: 0px 0px 20px 0px; clear: both;">`;
						results[key].forEach(item => {
							has_result = true;
							html += `<li data-type="${key}" data-id="${item.id}" style="list-style: none; margin-bottom: 15px; clear: both;">`;

							// Thumb
							if (SHOW_IMAGES) {
								let image = item.image,
									opacity = 1;

								if (!image) {
									image = `${SITE_URL}/wp-content/plugins/peepso-core/assets/images/embeds/no_preview_available.png`;
									opacity = 0.1;
								}

								html +=
									`<div style="background: url('${image}'); width: ${image_size}px; height: ${image_size}px; opacity: ${opacity};` +
									` float: left; margin: 0px 10px 10px 0px; background-size: cover; background-position: center center;"></div>`;
							}

							// Title
							html += `<a href="${item.url}"><strong>${item.title}</strong></a>`;

							// Metadata.
							if (item.meta) {
								let meta_html = '';
								for (let meta_key in item.meta) {
									if (item.meta.hasOwnProperty(meta_key)) {
										let meta = item.meta[meta_key];
										meta_html += `<span style="margin-right: 5px"><i class="${meta.icon}"></i> ${meta.title}</span>`;
									}
								}

								if (meta_html) {
									html += `<small style="font-size:12px;opacity:0.5"><br/>${meta_html}</small>`;
								}
							}

							// Text
							if (item.text) {
								html += `<br/><span style="font-size: 14px">${item.text}</span>`;
							}

							html += '</li>';
						});
						html += '</ul>';
					} else {
						html += `<p>${TEXT_NO_RESULTS}<br/><br/></p>`;
					}

					html += '</div>';
				}
			}

			if (!has_result) {
				html = `<p>${TEXT_NO_RESULTS}<br/><br/></p>`;
			}

			this.$results.html(html);
		}
	}

	$(() => {
		$('.ps-js-widget-search').each(function () {
			new WidgetSearch(this);
		});
	});
})(
	jQuery,
	_.debounce,
	peepso.modules,
	peepsodata.site_url,
	peepsodata.rest_url,
	peepsodata.search || {}
);
