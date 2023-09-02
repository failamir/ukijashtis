<i class="ps-giphy__trigger ps-icon-giphy ps-js-giphy-trigger"></i>

<div class="ps-giphy__popover ps-js-giphy-container">
	<div class="ps-giphy__search ps-giphy__search--popover">
		<input type="text" class="ps-input ps-input--small ps-js-giphy-query" placeholder="<?php echo __('Search...', 'peepso-giphy'); ?>">
	</div>

	<span class="ps-loading ps-icon-spinner ps-js-giphy-loading" style="display:none"></span>

	<div class="ps-giphy__list ps-js-giphy-list"></div>

	<script type="text/template" class="ps-js-giphy-list-item">
		<div class="ps-giphy__list-item">
			<img src="{{= data.preview }}" data-id="{{= data.id }}" data-url="{{= data.src }}" />
		</div>
	</script>

	<div class="ps-giphy__powered ps-giphy__powered--chat">
		<a href="https://giphy.com/" target="_blank"></a>
	</div>
</div>
