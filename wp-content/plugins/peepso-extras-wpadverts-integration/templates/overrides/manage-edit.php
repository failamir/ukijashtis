<?php adverts_flash( $adverts_flash ) ?>

<div class="ps-page">
    <form action="" method="post" class="ps-form ps-form--classifieds">
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php foreach($form->get_fields( array( "exclude" => array( "account" ) ) ) as $field): ?>

            <?php if($field["type"] == "adverts_field_header"): ?>
                <div class="ps-form__separator">
                    <?php echo esc_html($field["label"]) ?>
                </div>
                <?php if( isset( $field["description"] ) ): ?>
                    <div class="ps-form__desc"><?php echo esc_html( $field["description"] ) ?></div>
                <?php endif; ?>
            <?php else: ?>
            
            <div class="ps-form__row <?php echo "ps-form__row-" . esc_attr( str_replace("_", "-", $field["name"] )) ?> <?php if(adverts_field_has_errors($field)): ?>ps-form__row--error<?php endif; ?>">
                
                <label class="ps-form__label" for="<?php echo __($field["name"]) ?>">
                    <?php esc_html_e($field["label"]) ?>
                    <?php if(adverts_field_has_validator($field, "is_required")): ?>
                        <span class="ps-text--danger">*</span>
                    <?php endif; ?>
                </label>
                
                <div class="ps-form__field">
                    <?php call_user_func( adverts_field_get_renderer($field), $field) ?>

                    <?php if(adverts_field_has_errors($field)): ?>
                        <div class="ps-form__helper ps-text--danger">
                        <?php foreach($field["error"] as $k => $v): ?>
                            <div class="ps-form__error"><?php echo esc_html($v) ?></div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php endforeach; ?>
        <div class="ps-form__actions">
            <span class="ps-left">
                <a href="<?php echo __($baseurl) ?>" class="ps-btn"><?php echo __("Go back", "peepso-wpadverts") ?></a>
            </span>
            <span class="ps-right">
                <a href="<?php echo __(get_post_permalink( $post_id )) ?>" class="ps-btn"><?php echo __("Preview", "peepso-wpadverts") ?></a>
                <input type="submit" name="submit" value="<?php echo __("Update", "peepso-wpadverts") ?>" class="ps-btn ps-btn-primary" />
            </span>
        </div>
    </form>
</div>
