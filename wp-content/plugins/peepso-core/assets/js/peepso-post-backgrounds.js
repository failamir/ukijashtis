(function ($, peepso) {
	const observer = peepso.observer;

	class PostboxBackground {
		constructor($postbox) {
			this.$postbox = $postbox;
			this.$postboxTab = this.$postbox.$posttabs;
			this.$postboxBtnPreview = this.$postbox.find('.ps-js-btn-preview');
			this.$postboxStatusTextarea = this.$postbox.$textarea;
			this.$postboxStatus = this.$postboxStatusTextarea.closest('.ps-postbox-status');
			this.$postboxBackground = this.$postbox.find(
				'.ps-postbox-tabs [data-tab-id=post_backgrounds]'
			);
			this.$postboxBackgroundShortcut = this.$postbox.find('#post_backgrounds');

			this.$presets = this.$postboxBackground.find('.peepso-background-item');
			this.$background = this.$postboxBackground.find('.ps-js-post-background');
			this.$text = this.$postboxBackground.find('.ps-js-post-background-text');

			this.$postboxTab.on('peepso_posttabs_show-post_backgrounds', () => this.show());
			this.$postboxTab.on('peepso_posttabs_cancel-post_backgrounds', () => this.cancel());
			this.$postboxTab.on('peepso_posttabs_submit-post_backgrounds', () => this.post());
			this.$postboxBackgroundShortcut.on('click', () => {
				this.$postboxTab.find('[data-tab=post_backgrounds]').click();
			});

			this.$presets.on('click', e => this.select(e.currentTarget));
			this.$text.on('keyup', this.onKeyup.bind(this));
			this.$text.on('input', this.onInput.bind(this));

			observer.addAction(
				'postbox_type_set',
				($postbox, type) => {
					if ($postbox === this.$postbox && type === 'post_backgrounds') {
						this.$postboxBackgroundShortcut.trigger('click');
					}
				},
				10,
				2
			);

			observer.addFilter(
				'peepso_postbox_can_submit',
				(flags, $postbox) => {
					if ($postbox === this.$postbox) {
						let type = this.$postbox.$posttabs.current_tab_id;
						if ('post_backgrounds' === type) {
							let text = this.$text.html();
							let sanitized = text.replace(/<br\s*\/?>/gi, '\n').trim();
							flags.hard.push(!!sanitized);
						}
					}

					return flags;
				},
				10,
				2
			);
		}

		/**
		 * Show the postbox background type.
		 */
		show() {
			this.$postboxBtnPreview.hide();
			this.$postboxStatus.hide();
			this.$postboxBackground.show();

			// Turn all newlines into HTML.
			let contentHtml = this.$postboxStatusTextarea.val().replace(/(?:\r\n|\r|\n)/g, '<br>');
			this.$text.html(contentHtml);

			// Select the first preset if nothing is selected.
			let $selected = this.$presets.filter('.active');
			if (!$selected.length) {
				this.select(this.$presets.eq(0));
			}
		}

		/**
		 * Select a background preset.
		 *
		 * @param {Element|JQuery} preset
		 */
		select(preset) {
			let $preset = $(preset),
				bgImage = $preset.css('background-image'),
				bgColor = $preset.attr('data-background'),
				textColor = $preset.attr('data-text-color'),
				id = $preset.attr('data-preset-id');

			// Update background and text.
			this.$background.css('background-image', bgImage).attr('data-background', bgColor);
			this.$text
				.css('color', textColor)
				.attr('data-text-color', textColor)
				.attr('data-preset-id', id);

			// Update selected preset.
			$preset.addClass('active');
			this.$presets.not($preset).removeClass('active');

			// Sync placeholder text color.
			if (!this.__styleOverride) {
				this.__styleOverride = document.createElement('style');
				this.__styleOverride.id = 'peepso-post-background';
				document.head.appendChild(this.__styleOverride);
			}
			this.__styleOverride.innerHTML = `.ps-post__background-text:before { color: ${textColor} !important }`;

			// Focus on the "textarea".
			this.focus();
		}

		cancel() {
			this.select(this.$presets.eq(0));
			this.$text.html('');
		}

		post() {
			let filterName = 'postbox_req_' + this.$postbox.guid;

			observer.addFilter(filterName, this.postSetRequest, 10, 1, this);
			this.$postbox.save_post();
			observer.removeFilter(filterName, this.postSetRequest, 10);
		}

		postSetRequest(req) {
			req.type = 'post_backgrounds';
			req.content = this.$text[0].innerText;
			req.preset_id = this.$text.attr('data-preset-id');
			req.background = this.$background.attr('data-background');
			req.text_color = this.$text.attr('data-text-color');

			return req;
		}

		/**
		 * Handle focus.
		 */
		focus() {
			if (!this.$text.html().trim()) {
				// Should just focus on the element if it is empty.
				setTimeout(() => this.$text[0].focus(), 0);
			} else {
				setTimeout(() => {
					try {
						let sel = window.getSelection();
						let range = document.createRange();
						let node = this.$text[0];

						// https://stackoverflow.com/questions/2388164/set-focus-on-div-contenteditable-element/59437681#59437681
						node = node.childElementCount > 0 ? node.lastChild : node;

						range.setStart(node, 1);
						range.setEnd(node, 1);
						sel.removeAllRanges();
						sel.addRange(range);
					} catch (e) {}
				}, 0);
			}
		}

		onKeyup() {
			// Get the text representation of the contenteditable.
			let contentText = this.$text[0].innerText;

			this.$postboxStatusTextarea.val(contentText);
			this.$postboxStatusTextarea.trigger('keyup');
			this.$postboxBtnPreview.hide();
		}

		onInput() {
			let content = this.$text.html(),
				sanitized = content
					.replace(/<([a-z]+)[^>]*>/g, '<$1>')
					.replace(/<(\/?)a>/g, '<$1span>');

			if (content !== sanitized) {
				this.$text.html(sanitized);
			}
		}
	}

	// Initialize class on main postbox initialization.
	observer.addAction(
		'peepso_postbox_addons',
		addons => {
			let wrapper = {
				init() {},
				set_postbox($postbox) {
					if ($postbox.find('#post_backgrounds').length) {
						new PostboxBackground($postbox);
					}
				}
			};

			addons.push(wrapper);
			return addons;
		},
		10,
		1
	);
})(jQuery, window.peepso);
