<div class="ps-page">
    <?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/single.php' ); ?>

    <div class="ps-form__actions">
        <span class="ps-left">
            <form action="" method="post" style="display:inline">
                <input type="hidden" name="_adverts_action" value="" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo esc_attr($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr( isset($post_id_nonce) ? $post_id_nonce : "" ) ?>" />
                <input type="submit" value="<?php echo __("Edit Listing", "peepso-wpadverts") ?>" class="ps-btn adverts-cancel-unload" />
            </form>
        </span>
        <span class="ps-right">
            <form action="" method="post" style="display:inline">
                <input type="hidden" name="_adverts_action" value="save" />
                <input type="hidden" name="_post_id" id="_post_id" value="<?php echo __($post_id) ?>" />
                <input type="hidden" name="_post_id_nonce" id="_post_id_nonce" value="<?php echo esc_attr( isset($post_id_nonce) ? $post_id_nonce : "" ) ?>" />
                <input type="submit" value="<?php echo __("Publish Listing", "peepso-wpadverts") ?>" class="ps-btn ps-btn-primary adverts-cancel-unload" />
            </form>
        </span>
    </div>
</div>
