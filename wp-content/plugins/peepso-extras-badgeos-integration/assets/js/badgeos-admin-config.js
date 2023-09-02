/**********************************************************************************		 
 * BadgeOS Admin Config JS
 **********************************************************************************/

(function($) {
    var $display_badges_on_cover = $("input[name='badgeos_display_recent_badges_on_cover']");
    var hide_animation_speed = 500;

    if ($display_badges_on_cover.size() > 0) {

        $display_badges_on_cover.on("change", function() {
            $selector = $(this).closest('.inside').find('[id="field_badgeos_limit_recent_badges_on_cover"]');
            if ($(this).is(":checked")) {
                $selector.fadeIn(hide_animation_speed);
            } else {
                $selector.fadeOut(hide_animation_speed);
            }
        }).trigger('change');
    }

    var $display_badges_on_profile_widget = $("input[name='badgeos_display_recent_badges_on_profile_widget']");
    if ($display_badges_on_profile_widget.size() > 0) {

        $display_badges_on_profile_widget.on("change", function() {
            $selector = $(this).closest('.inside').find('[id="field_badgeos_limit_recent_badges_on_profile_widget"]');
            if ($(this).is(":checked")) {
                $selector.fadeIn(hide_animation_speed);
            } else {
                $selector.fadeOut(hide_animation_speed);
            }
        }).trigger('change');
    }
})(jQuery);
