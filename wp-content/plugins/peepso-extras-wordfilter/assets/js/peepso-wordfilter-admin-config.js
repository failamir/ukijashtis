/**********************************************************************************
 * Word Filter
 **********************************************************************************/

(function($) {
    $('form.ps-form').on('submit',function(e){
        var $wordfilter = $("textarea[name='wordfilter_keywords']");
        words = $wordfilter.val();
        str_array = words.split(',');
        words = [];

        for(var i = 0; i < str_array.length; i++) {
            // Trim the excess whitespace.
            str_array[i] = str_array[i].replace(/^\s*/, "").replace(/\s*$/, "");
            
            // Add additional code here, such as:
            if(str_array[i]) {
                words.push(str_array[i]);
            }
        }

        $wordfilter.val(words.join(', '));
    });

})(jQuery);
