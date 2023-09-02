<?php if(!empty($terms)): ?>
<div class="adverts-flexbox adverts-categories-top">
<?php foreach($terms as $term): ?>
    <?php $icon = adverts_taxonomy_get("advert_category", $term->term_id, "advert_category_icon", "folder") ?>
    <div class="adverts-flexbox-item <?php echo $columns ?> <?php echo "adverts-category-slug-".$term->slug ?>">
        <span class="adverts-flexbox-wrap">
            <span class="adverts-flexbox-icon <?php echo apply_filters("adverts_category_font_icon", "adverts-icon-".$icon, $term, "big") ?>"></span>
            <?php do_action( "adverts_category_pre_title", $term, "big") ?>
            <span class="adverts-flexbox-title">
                <a class="adverts-category-link" href="<?php echo PeepSo::get_page('wpadverts').'?category='.$term->term_id.'/';?>"></a>
                <?php echo esc_html($term->name) ?>
                <?php if($show_count): ?>
                (<?php echo adverts_category_post_count( $term ) ?>)
                <?php endif; ?>
            </span>
        </span>
    </div>

<?php endforeach; ?>
</div>
<?php else: ?>
<div class="adverts-grid-row">
    <div class="adverts-col-100">
        <span><?php echo __("No categories found.", "peepso-wpadverts") ?></span>
    </div>
</div>
<?php endif; ?> 