<div class="w2dc-content">
	<div class="w2dc-page-header-widget" <?php if ($featured_image) echo 'style="background-image: url(' . $featured_image . ');"' ?>>
		<h1 class="w2dc-page-title-widget"><?php echo $page_title; ?></h1>
		<?php
		$breadcrumbs = w2dc_breadcrumb_trail(array(
			'show_browse' => false,
			'show_on_front' => false,
			'echo' => false
		)); ?>
		<?php if ($breadcrumbs): ?>
		<?php echo $breadcrumbs; ?> 
		<?php endif; ?>
	</div>
</div>