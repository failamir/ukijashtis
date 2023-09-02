<div style="padding-bottom:10px">
    <div class="ps-js-giphy-preview">
        <img src="https://media3.giphy.com/media/YmhdWZbwSjrrsSh1BV/giphy-preview.gif">
        <a href="#" class="ps-js-giphy-change"><?php echo __('Change image', 'peepso-giphy'); ?></a>
    </div>
    <div class="ps-js-giphy-selector">
        <div class="ps-giphy ps-giphy--slider">
            <div class="ps-giphy__search">
                <input type="text" class="ps-input ps-input--small ps-js-giphy-query"
                placeholder="<?php echo __('Search...', 'peepso-giphy'); ?>" style="display:none" />
            </div>

            <div class="ps-loading ps-js-giphy-loading">
                <span class="ps-icon-spinner"></span>
            </div>

            <div class="ps-giphy__slider">
                <div class="ps-giphy__slides ps-js-giphy-list"></div>

                <script type="text/template" class="ps-js-giphy-list-item">
                    <span class="ps-giphy__slides-item">
                        <img src="{{= data.preview }}" data-id="{{= data.id }}" data-url="{{= data.src }}" />
                    </span>
                </script>

                <div class="ps-giphy__nav ps-giphy__nav--left ps-js-giphy-nav-left"><i class="ps-icon-caret-left"></i></div>
                <div class="ps-giphy__nav ps-giphy__nav--right ps-js-giphy-nav-right"><i class="ps-icon-caret-right"></i></div>
            </div>

            <div class="ps-giphy__powered ps-giphy__powered--slider">
                <a href="https://giphy.com/" target="_blank"></a>
            </div>
        </div>
    </div>
</div>
