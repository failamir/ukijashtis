<div class="pa-page pa-page--addons pa-addons">
	<div class="pa-addons__header">
		<div class="pa-addons__license">
			<!-- License name -->
			<div class="pa-addons__license-header ps-js-license-name">
				<?php echo __('Your license', 'peepso-core'); ?>
			</div>
			<div class="pa-addons__license-form">
				<div class="pa-addons__license-key">
					<div class="pa-addons__license-input-wrapper">
						<i class="gcis gci-key"></i>
						<input type="text" placeholder="<?php echo __('License key...', 'peepso-core'); ?>" value="<?php echo $license; ?>" class="pa-input pa-addons__license-input peepso-license-key <?php echo ($license != '') ? '' : 'empty-license'; ?>" />
					</div>
					<button data-running-text="<?php echo __('Checking...', 'peepso-core'); ?>" class="pa-btn pa-addons_license-button">
						<i class="gcis gci-sync-alt"></i>
						<span><?php echo __('Check', 'peepso-core'); ?></span>
					</button>
				</div>
				<div class="pa-addons__license-notice">

				</div>
			</div>

			<div class="pa-addons__license-message ps-js-addons-message"></div>
		</div>

		<!-- Top bulk action buttons. -->
		<div class="pa-addons__bulk-actions ps-js-bulk-actions">
			<button class="pa-btn pa-addons__bulk-action-show ps-js-bulk-show"><i class="gcis gci-cog"></i><?php echo __('Show bulk actions', 'peepso-core'); ?></button>
			<button class="pa-btn pa-addons__bulk-action-install ps-js-bulk-install" data-running-text="<?php echo __('Installing ...','peepso-core'); ?>" data-tooltip="<?php echo __('Please select one or more products', 'peepso-core'); ?>" style="display:none"><i class="gcis gci-plus"></i><span><?php echo __('Install', 'peepso-core'); ?></span></button>
			<button class="pa-btn pa-addons__bulk-action-activate ps-js-bulk-activate" data-running-text="<?php echo __('Activating ...','peepso-core'); ?>" data-tooltip="<?php echo __('Please select one or more products', 'peepso-core'); ?>" style="display:none"><i class="gcis gci-check"></i><span><?php echo __('Activate', 'peepso-core'); ?></span></button>
			<button class="pa-btn pa-addons__bulk-action-hide ps-js-bulk-hide" style="display:none"><i class="gcis gci-cog"></i><?php echo __('Hide bulk actions', 'peepso-core'); ?></button>
		</div>
	</div>

	<div class="pa-addons__actions">
		<!-- <div class="pa-addons__actions-inner">
			<div class="pa-addons__license-name ps-js-bundle-name-wrapper">&nbsp;</div>
		</div> -->

		<div class="pa-addons__actions-select-all ps-js-bulk-checkall-wrapper" style="display:none">
			<input type="checkbox" class="ps-js-bulk-checkall" id="bulk-check-all" />
			<label for="bulk-check-all"><?php echo __('Select all', 'peepso-core'); ?></label>
		</div>

		<div class="pa-addons__disabler ps-js-action-disabler"></div>
	</div>

	<div style="position:relative">
		<div class="pa-addons__list ps-js-list"></div>
		<div class="pa-addons__disabler ps-js-action-disabler"></div>
	</div>

	<div class="pa-addons__actions pa-addons__actions--bottom">
		<div class="pa-addons__actions-inner">
			<div class="pa-addons__actions-select-all ps-js-bulk-checkall-wrapper" style="display:none">
				<input type="checkbox" class="ps-js-bulk-checkall" id="bulk-check-all" />
				<label for="bulk-check-all"><?php echo __('Select all', 'peepso-core'); ?></label>
			</div>

			<!-- Top bulk action buttons. -->
			<div class="pa-addons__bulk-actions ps-js-bulk-actions">
				<button class="pa-btn pa-addons__bulk-action-show ps-js-bulk-show"><i class="gcis gci-cog"></i><?php echo __('Show bulk actions', 'peepso-core'); ?></button>
				<button class="pa-btn pa-addons__bulk-action-install ps-js-bulk-install" data-running-text="<?php echo __('Installing ...','peepso-core'); ?>" data-tooltip="<?php echo __('Please select one or more products', 'peepso-core'); ?>" style="display:none"><i class="gcis gci-plus"></i><span><?php echo __('Install', 'peepso-core'); ?></span></button>
				<button class="pa-btn pa-addons__bulk-action-activate ps-js-bulk-activate" data-running-text="<?php echo __('Activating ...','peepso-core'); ?>" data-tooltip="<?php echo __('Please select one or more products', 'peepso-core'); ?>" style="display:none"><i class="gcis gci-check"></i><span><?php echo __('Activate', 'peepso-core'); ?></span></button>
				<button class="pa-btn pa-addons__bulk-action-hide ps-js-bulk-hide" style="display:none"><i class="gcis gci-cog"></i><?php echo __('Hide bulk actions', 'peepso-core'); ?></button>
			</div>
		</div>

		<div class="pa-addons__disabler ps-js-action-disabler"></div>
	</div>

	<div class="pa-addons__disabler ps-js-disabler"></div>
</div>
