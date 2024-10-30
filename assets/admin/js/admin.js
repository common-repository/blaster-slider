(function($){
    $(document).ready(function(){
        $('.blaster-slider-wrapper').each(function(){
            $('.color-field').wpColorPicker();
            var custom_css = $('input[name="custom_css"]');

 //           var editor = ace.edit("custom_css_editor");
 //           editor.setTheme("ace/theme/twilight");
 //           editor.getSession().setMode("ace/mode/css");

            editor.getSession().on("change", function () {
                custom_css.val(editor.getSession().getValue());
            });
            custom_css.val(editor.getSession().getValue());
        });
    });
})(jQuery);
