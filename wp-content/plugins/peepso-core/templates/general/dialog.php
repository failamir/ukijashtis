<div class="ps-modal__wrapper">
	<div class="ps-modal__container">
		<div class="ps-modal {{= data.opts.wide ? 'ps-modal--wide' : '' }}">
			<div class="ps-modal__inner">
				<div class="ps-modal__header ps-js-header">
					<div class="ps-modal__title ps-js-title">{{= data.opts.title }}</div>
					<a href="#" class="ps-modal__close ps-js-close"><i class="gcis gci-times"></i></a>
				</div>
				<div class="ps-modal__body ps-js-body">
					<div class="ps-modal__content">{{= data.html }}</div>
				</div>
				{{ if (data.opts.actions) { }}
				<div class="ps-modal__footer ps-js-footer">
					<div class="ps-modal__actions">
						{{ var actions = data.opts.actions; for (var i = 0; i < actions.length; i++) { }}
						<button class="ps-btn ps-btn--sm {{= actions[i].primary ? 'ps-btn--action' : '' }} {{= actions[i].class || '' }}">
							{{= actions[i].label }}
							{{ if (actions[i].loading) { }}
							<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>"
								class="ps-js-loading" alt="loading" style="padding-left:5px; display:none" />
							{{ } }}
						</button>
						{{ } }}
					</div>
				</div>
				{{ } }}
			</div>
		</div>
	</div>
</div>
