<div class="ps-posts__filters">
  <div class="ps-posts__filters-group ps-posts__filters-group--primary">
    <a href="javascript:" class="ps-posts__filters-toggle ps-js-activitystream-filters-toggle"><i class="gcis gci-bars-staggered"></i><?php echo __('Filter Stream', 'peepso-core'); ?></a>
    <div class="ps-posts__filters-wrapper ps-js-activitystream-filters-wrapper" style="display:none">
    <?php

    /** STREAM ID **/

    $stream_id = $user_stream_filters['stream_id'];
    if(count($stream_id_list)) {

        $default = PeepSo::get_option('stream_id_default');

        if(!isset($stream_id_list[$stream_id]) && !array_key_exists($stream_id, $stream_id_list)) {
            $stream_id = $default;
        }

        if(!isset($stream_id_list[$stream_id]) && !array_key_exists($stream_id, $stream_id_list)) {
            reset($stream_id_list);
            $stream_id = key($stream_id_list);
        }

        $selected = $stream_id_list[$stream_id];

        ?>
        <input type="hidden" id="peepso_stream_id" value="<?php echo $stream_id; ?>"/>
        <?php if (count($stream_id_list) > 1) { ?>

        <div class="ps-posts__filter ps-posts__filter--type ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_stream_id">
          <a href="javascript:" class="ps-posts__filter-toggle ps-js-dropdown-toggle" aria-haspopup="true">
            <i class="<?php echo $selected['icon']; ?>"></i>
            <span><?php echo $selected['label']; ?></span>
          </a>
          <div class="ps-posts__filter-box ps-posts__filter-box--type ps-js-dropdown-menu" role="menu">
            <?php foreach ($stream_id_list as $key => $value) { ?>
            <a class="ps-posts__filter-select" data-option-value="<?php echo $key; ?>" data-option-label-warning="<?php echo $value['label_warning'];?>" role="menuitem">
              <div class="ps-checkbox">
                <input type="radio" name="peepso_stream_id" id="peepso_stream_id_opt_<?php echo $key ?>"
                  value="<?php echo $key ?>" <?php if ($key == $stream_id) echo "checked"; ?> />
                <label for="peepso_stream_id_opt_<?php echo $key ?>">
                  <span><?php echo $value['label']; ?></span>
                  <div class="ps-posts__filter-select-desc"><?php echo $value['desc']; ?></div>
                </label>
                <i class="<?php echo $value['icon']; ?>"></i>
              </div>
            </a>
            <?php } ?>
            <div class="ps-posts__filter-actions">
              <button class="ps-posts__filter-action ps-btn ps-btn--sm ps-js-cancel"><?php echo __('Cancel', 'peepso-core'); ?></button>
              <button class="ps-posts__filter-action ps-btn ps-btn--sm ps-btn--action ps-js-apply" ><?php echo __('Apply', 'peepso-core'); ?></button>
            </div>
          </div>
        </div>

        <?php } ?>
      <?php } ?>

      <?php

      /** HIDE MY POSTS **/

      $show_my_posts_list = array(
      	'1' => array('label' => __('Show my posts', 'peepso-core')),
      	'0' => array('label' => __('Hide my posts', 'peepso-core')),
      );

      $show_my_posts = $user_stream_filters['show_my_posts'];
      $selected = $show_my_posts_list[$show_my_posts];

      ?>

      <input type="hidden" id="peepso_stream_filter_show_my_posts" value="<?php echo $show_my_posts; ?>" />
      <div class="ps-posts__filter ps-posts__filter--myposts ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_stream_filter_show_my_posts">
      	<a href="javascript:" class="ps-posts__filter-toggle ps-js-dropdown-toggle" aria-haspopup="true">
      		<span><?php echo $show_my_posts ? __('Show my posts', 'peepso-core') : __('Hide my posts', 'peepso-core'); ?></span>
      	</a>
      	<div class="ps-posts__filter-box ps-posts__filter-box--myposts ps-js-dropdown-menu" role="menu">
      		<?php foreach ($show_my_posts_list as $key => $value) { ?>
      		<a class="ps-posts__filter-select" data-option-value="<?php echo $key; ?>" role="menuitem">
            <div class="ps-checkbox">
              <input type="radio" name="peepso_stream_filter_show_my_posts" id="peepso_stream_filter_show_my_posts_opt_<?php echo $key ?>"
                value="<?php echo $key ?>" <?php if($key == $show_my_posts) echo "checked"; ?> />
        			<label for="peepso_stream_filter_show_my_posts_opt_<?php echo $key ?>">
                <span><?php echo $value['label']; ?></span>
        			</label>
            </div>
      		</a>
      		<?php } ?>
      		<div class="ps-posts__filter-actions">
      			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-js-cancel"><?php echo __('Cancel', 'peepso-core'); ?></button>
      			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-btn--action ps-js-apply"><?php echo __('Apply', 'peepso-core'); ?></button>
      		</div>
      	</div>
      </div>

      <?php

      $sort_posts = array(
        '0' => array('label' => __('Latest posts', 'peepso-core')),
        '1' => array('label' => __('Recently commented', 'peepso-core')),
      );

      $sort_by = 0;
      if(PeepSo::is_dev_mode('bump')) {
          $sort_by = intval($user_stream_filters['sort_by']);
          $selected = $sort_posts[$sort_by];
      }

      ?>

      <input type="hidden" id="peepso_stream_filter_sort_by" value="<?php echo $sort_by; ?>" />

        <?php if(PeepSo::is_dev_mode('bump')) { ?>
      <div class="ps-posts__filter ps-posts__filter--sort_by ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_stream_filter_sort_by">
      	<a href="javascript:" class="ps-posts__filter-toggle ps-js-dropdown-toggle" aria-haspopup="true">
      		<span><?php echo $sort_by === 0 ? __('Latest posts', 'peepso-core') : __('Recently commented', 'peepso-core'); ?></span>
      	</a>
      	<div class="ps-posts__filter-box ps-posts__filter-box--myposts ps-js-dropdown-menu" role="menu">
      		<?php foreach ($sort_posts as $key => $value) { ?>
      		<a class="ps-posts__filter-select" data-option-value="<?php echo $key; ?>" role="menuitem">
            <div class="ps-checkbox">
              <input type="radio" name="peepso_stream_filter_sort_by" id="peepso_stream_filter_sort_by_opt_<?php echo $key ?>"
                value="<?php echo $key ?>" <?php if($key == $sort_by) echo "checked"; ?> />
        			<label for="peepso_stream_filter_sort_by_opt_<?php echo $key ?>">
                <span><?php echo $value['label']; ?></span>
        			</label>
            </div>
      		</a>
      		<?php } ?>
      		<div class="ps-posts__filter-actions">
      			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-js-cancel"><?php echo __('Cancel', 'peepso-core'); ?></button>
      			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-btn--action ps-js-apply"><?php echo __('Apply', 'peepso-core'); ?></button>
      		</div>
      	</div>
      </div>
      <?php } ?>
    </div>
  </div>

  <div class="ps-posts__filters-group ps-posts__filters-group--secondary">
  <?php

  /** SEARCH POSTS **/
  $search = FALSE;
  $PeepSoUrlSegments = PeepSoUrlSegments::get_instance();

  #4158 ?search/querystring does not work with special chars
  if('search' == $PeepSoUrlSegments->get(1)) {
      $search = $PeepSoUrlSegments->get(2);
  }

  #4158 ?search/querystring does not work with special chars
  if(isset($_GET['filter'])) {
      $PeepSoInput = new PeepSoInput();
      $search = $PeepSoInput->value('filter', '', FALSE);
  }
  ?>
  <input type="hidden" id="peepso_search" value="<?php echo $show_my_posts; ?>" />
  <div class="ps-posts__filter ps-posts__filter--search ps-js-dropdown ps-js-activitystream-filter" data-id="peepso_search">
  	<a class="ps-posts__filter-toggle ps-js-dropdown-toggle" aria-haspopup="true" aria-label="<?php echo __('Search', 'peepso-core'); ?>">
  		<i class="gcis gci-search"></i>
  		<span data-empty="<?php //echo __('Search', 'peepso-core'); ?>"
  			data-keyword="<?php echo __('Search: ', 'peepso-core'); ?>"></span>
  	</a>
  	<div class="ps-posts__filter-box ps-posts__filter-box--search ps-js-dropdown-menu" role="menu">
  		<div class="ps-posts__filter-search">
  			<i class="gcis gci-search"></i><input type="text" id="ps-activitystream-search" class="ps-input ps-input--sm"
  				placeholder="<?php echo __('Type to search', 'peepso-core'); ?>" value="<?php echo $search;?>" />
  		</div>

  		<a role="menuitem" class="ps-posts__filter-select" data-option-value="exact">
  			<div class="ps-checkbox">
  				<input type="radio" name="peepso_search" id="peepso_search_opt_exact" value="exact" checked />
  				<label for="peepso_search_opt_exact">
  					<span><?php echo __('Exact phrase', 'peepso-core'); ?></span>
  				</label>
  			</div>
  		</a>
  		<a role="menuitem" class="ps-posts__filter-select" data-option-value="any">
  			<div class="ps-checkbox">
  				<input type="radio" name="peepso_search" id="peepso_search_opt_any" value="any" />
  				<label for="peepso_search_opt_any">
  					<span><?php echo __('Any of the words', 'peepso-core'); ?></span>
  				</label>
  			</div>
  		</a>
  		<div class="ps-posts__filter-actions">
  			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-js-cancel"><?php echo __('Cancel', 'peepso-core'); ?></button>
  			<button class="ps-posts__filter-action ps-btn ps-btn--sm ps-btn--action ps-js-search"><?php echo __('Search', 'peepso-core'); ?></button>
  		</div>
  	</div>
  </div>

  <?php

  /** ADDITIONAL FILTERS - HOOKABLE **/

  do_action('peepso_action_render_stream_filters');
  ?>
  </div>
</div>
<div id="ps-stream__filters-warning" class="ps-posts__filters-warning">
  <i class="gcis gci-info-circle"></i> <?php echo __('You are currently only viewing %s content.','peepso-core'); ?>
</div>
