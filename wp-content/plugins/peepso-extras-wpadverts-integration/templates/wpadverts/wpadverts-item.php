<div class="ps-classified__item-wrapper ps-js-classifieds-item ps-js-classifieds-item--{{= data.id }}">
	<div class="ps-classified__item{{ if (data.is_featured) { }} ps-classified__item--featured {{ } }}">
		<div class="ps-classified__item-body">
			{{ if ( data.image ) { }}
			<a class="ps-classified__item-image" href="{{= data.permalink }}">
				<img src="{{= data.image }}" alt="">
			</a>
			{{ } }}

			<h3 class="ps-classified__item-title">
				<a href="{{= data.permalink }}">{{= data.title }}</a>
			</h3>

			{{ if (data.price) { }}
			<div class="ps-classified__item-details">
				<a class="ps-classified__item-price" href="{{= data.permalink }}">{{= data.price }}</a>
			</div>
			{{ } }}

			<div class="ps-classified__item-desc">{{= data.content }}</div>
		</div>

		<div class="ps-classified__item-footer ps-text--muted">
			<span><i class="ps-icon-user"></i> {{= data.author }}</span>
			<span><i class="ps-icon-clock"></i> {{= data.date_formated }}</span>
			{{ if ( data.location ) { }}
			<span><i class="ps-icon-map-marker"></i> {{= data.location }}</span>
			{{ } }}
			{{ if ( data.is_owner == true || data.is_admin == true ) { }}
			<span><i class="ps-icon-clock"></i> {{ if (data.is_expired) { }}<?php echo __('expired', 'peepso-wpadverts'); ?>{{ } else { }}<?php echo __('expires', 'peepso-wpadverts'); ?>{{ } }}: {{= data.expires }}</span>
			{{ } }}

			<div class="ps-classified__item-actions">
				<div class="ps-js-action">
                    <?php

                    if( 1 == PeepSo::get_option('wpadverts_chat_enable', 0)) { ?>
					{{ if ( window.ps_messages && ! data.is_owner ) { }}
					<a href="#" class="ps-js-wpadverts-message" data-id="{{= data.user_id }}">
						<i class="ps-icon-envelope-alt"></i>
						<span><?php echo __('Send Message', 'peepso-wpadverts'); ?></span>
						<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" style="display:none" />
					</a>
					{{ } }}
                    <?php } ?>
					<a href="{{= data.permalink }}" class="ps-link--more">
						<i class="ps-icon-info-circled"></i>
						<span><?php echo __('More', 'peepso-wpadverts'); ?></span>
					</a>
					{{ if ( data.is_owner || data.is_admin ) { }}
					<?php $baseurl = apply_filters( "adverts_manage_baseurl", get_the_permalink() ); ?>
					<?php if (PeepSoWPAdverts::isVersion140()) :?>
					<a href="<?php echo __( admin_url( 'admin-ajax.php' ) ) ?>?action=adverts_delete&id={{= data.id }}&redirect_to=<?php echo __( urlencode( $baseurl ) ) ?>&_ajax_nonce={{= data.nonce_delete}}"
							class="ps-link--delete adverts-manage-action-delete ps-js-delete"
							title="<?php echo __('Delete', 'peepso-wpadverts'); ?>"
							data-url="<?php echo __( admin_url('admin-ajax.php') ); ?>"
							data-id="{{= data.id }}"
							data-nonce="{{= data.nonce_delete}}">
						<i class="ps-icon-trash"></i>
						<span><?php echo __('Delete', 'peepso-wpadverts'); ?></span>
					</a>
					<?php else:?>
					<a href="<?php echo __( admin_url( 'admin-ajax.php' ) ) ?>?action=adverts_delete&id={{= data.id }}&redirect_to=<?php echo __( urlencode( $baseurl ) ) ?>&_ajax_nonce=<?php echo wp_create_nonce('adverts-delete') ?>"
							class="ps-link--delete adverts-manage-action-delete ps-js-delete"
							title="<?php echo __('Delete', 'peepso-wpadverts'); ?>"
							data-url="<?php echo __( admin_url('admin-ajax.php') ); ?>"
							data-id="{{= data.id }}"
							data-nonce="<?php echo wp_create_nonce('adverts-delete') ?>">
						<i class="ps-icon-trash"></i>
						<span><?php echo __('Delete', 'peepso-wpadverts'); ?></span>
					</a>
					<?php endif; ?>
					<a href="{{= data.edit_url }}" class="ps-link--edit">
						<i class="ps-icon-pencil"></i>
						<span><?php echo __('Edit', 'peepso-wpadverts'); ?></span>
					</a>
					{{ } }}
				</div>

				{{ if ( data.is_owner || data.is_admin ) { }}
				<div class="ps-js-delete-confirm" style="display:none">
					<i class="ps-icon-trash"></i>
					<i class="ps-icon-spinner" style="display:none"></i>
					<?php echo __('Are you sure?', 'peepso-wpadverts'); ?>
					<a href="#" class="ps-js-delete-yes" onclick="return false;"><?php echo __('Yes', 'peepso-wpadverts'); ?></a>
					<a href="#" class="ps-js-delete-no" onclick="return false;"><?php echo __('Cancel', 'peepso-wpadverts'); ?></a>
				</div>
				{{ } }}
			</div>
		</div>
	</div>
</div>
