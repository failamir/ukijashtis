<div class="ps-classifieds ps-classifieds--manage">
    <?php if( $loop->have_posts()): ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php global $post ?>
    
    <div class="ps-classified__item-wrapper advert-manage-item">
        <div class="ps-classified__item ps-classified__item--manage">
            <div class="ps-classified__item-body">
                <?php $image = adverts_get_main_image( get_the_ID() ) ?>

                <?php if($image): ?>
                <a class="ps-classified__item-image" href="<?php the_permalink() ?>">
                    <img src="<?php echo esc_attr($image) ?>" alt="">
                </a>
                <?php endif; ?>

                <h3 class="ps-classified__item-title">
                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                </h3>
                
                <div class="ps-classified__item-details">
                    <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
                    <?php if( $price ): ?>
                        <a class="ps-classified__item-price" href="<?php the_permalink() ?>"><?php echo adverts_price( $price ) ?></a>
                    <?php endif; ?>
                </div>

                <div class="ps-classified__item-desc">
                    <?php if($post->post_status == "pending"): ?>
                    <span class="ps-text--danger"><?php echo __("Inactive — This Ad is in moderation.", "peepso-wpadverts") ?></span>
                    <?php endif; ?>

                    <?php if($post->post_status == "expired"): ?>
                    <span class="ps-text--danger"><?php echo __("Inactive — This Ad expired.", "peepso-wpadverts") ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ps-classified__item-footer ps-text--muted">
                <?php do_action("adverts_sh_manage_list_status", $post) ?>

                <?php $expires = get_post_meta( $post->ID, "_expiration_date", true ) ?>
                <?php if( $expires ): ?>
                    <span><i class="ps-icon-clock"></i> <?php echo esc_html( sprintf( __( "Expires: %s", "peepso-wpadverts" ), date_i18n( get_option("date_format"), $expires ) ) ) ?></span>
                <?php endif; ?>

                <div class="ps-classified__item-actions">
                    <!--<a href="<?php echo __(get_the_permalink()) ?>" title="<?php echo __("View", "peepso-wpadverts") ?>"><i class="ps-icon-eye"></i> <?php echo __("View", "peepso-wpadverts") ?></a>-->
                    <a href="<?php echo __($baseurl . str_replace("%#%", get_the_ID(), $edit_format)) ?>" title="<?php echo __("Edit", "peepso-wpadverts") ?>"><i class="ps-icon-edit"></i> <?php echo __("Edit", "peepso-wpadverts") ?></a>
                    <a href="<?php echo __(admin_url("admin-ajax.php")) ?>?action=adverts_delete&id=<?php echo get_the_ID() ?>&redirect_to=<?php echo __( urlencode( $baseurl ) ) ?>&_ajax_nonce=<?php echo wp_create_nonce('adverts-delete') ?>" title="<?php echo __("Delete", "peepso-wpadverts") ?>" class="adverts-manage-action-delete" data-id="<?php echo get_the_ID() ?>" data-nonce="<?php echo wp_create_nonce('adverts-delete') ?>">
                        <i class="ps-icon-trash"></i><?php echo __("Delete", "peepso-wpadverts") ?>
                    </a>
                    
                    <div class="adverts-manage-delete-confirm">
                        <i class="ps-icon-trash"></i>
                        <?php echo __( "Are you sure?", "adverts" ) ?>
                        <span class="animate-spin adverts-icon-spinner adverts-manage-action-spinner" style="display:none"></span>
                        <a href="#" class="adverts-manage-action-delete-yes"><?php echo __( "Yes", "adverts" ) ?></a>
                        <a href="#" class="adverts-manage-action-delete-no"><?php echo __( "Cancel", "adverts" ) ?></a>
                    </div>

                    <a href="#" class="adverts-manage-action-more"><span class="adverts-icon-menu"></span><?php echo __("More", "adverts") ?></a>
                    
                    <?php do_action( "adverts_sh_manage_actions_right", $post->ID ) ?>
                    <?php do_action( "adverts_sh_manage_actions_left", $post->ID ) ?>
                    <?php do_action( "adverts_sh_manage_actions_more", $post->ID ) ?>
                </div>
                <?php do_action( "adverts_sh_manage_actions_after", $post->ID ) ?>
            </div>
        </div>
    </div>
    
    <?php endwhile; ?>
    <?php else: ?>
    <div class="ps-classified__item-wrapper">
        <?php echo __("You do not have any Ads posted yet.", "adverts") ?>
    </div>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

<div class="adverts-pagination">
    <?php echo paginate_links( array(
        'base' => $paginate_base,
    'format' => $paginate_format,
    'current' => max( 1, $paged ),
    'total' => $loop->max_num_pages,
        'prev_next' => false
    ) ); ?>
</div>
