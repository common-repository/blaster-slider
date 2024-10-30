(function () {

    (function($){

        window.blasterSliderEditor = {

            editor: null,
            wrapper: null,
            fields: {},

            init: function(){
                this.wrapper = $('.blaster-slider-editor');
                this.prepareFields();
                this.fields.submit.on('click', this.submit.bind(this));
            },

            prepareFields: function(){
                var fields = this.wrapper.find('select, input');
                fields.each(function(index, element){
                    var field = $(element);
                    var id = String(field.attr('id')).substr(6);
                    this.fields[id] = field;
                }.bind(this));
                this.wrapper.find('.color-field').wpColorPicker();
            },

            getValues: function(){
                var values = {};
                $.each(this.fields, function(name, field){
                    if(field.attr('type') != 'button'){
                        if(field.attr('type') == 'checkbox'){
                            values[name] = field.is(':checked') ? 'yes' : 'no';
                        } else {
                            values[name] = field.val();
                        }
                    }
                });
                return values;
            },
            submit: function(){
                var selected_text = this.editor.selection.getContent();
                var values = this.getValues();
                var output = '[blaster_slider';
                $.each(values, function(attribute, value){
                    if(value !== ''){
                        output += ' ' + attribute + '="' + value + '"';
                    }
                });
                output += ']';
                tb_remove();
                this.editor.execCommand('mceInsertContent', 0, output);
            }
        };
    })(jQuery);
    var clicked = false;

    tinymce.create('tinymce.plugins.blaster_slider', {
        init: function (ed, url) {
            window.blasterSliderEditor.editor = ed;
            ed.addButton('blaster_slider', {
                title: 'Blaster Slider',
                cmd: 'blaster_slider',
                image : url + '/../../img/slider-shortcode-gen.png'
            });
            ed.addCommand('blaster_slider', function () {
                var url = ajaxurl + '?action=blaster_slider_editor&KeepThis=true&width=100%&height=auto';
                if(jQuery('#TB_overlay').length < 1){
                    setTimeout(function(){
                        if(jQuery('#TB_overlay').length < 1) {
                            tb_show("Blaster Slider", url);
                        }
                    }, 300);
                }
            });
        },
        createControl: function (n, cm) {
            return null;
        },
        getInfo: function () {
            return {
                longname: 'Blaster Slider',
                author: 'EMTLY',
                authorurl: 'http://www.emtly.com/',
                infourl: 'http://www.emtly.com/',
                version: "1.0"
            };
        },
        getDoc: function () {
        }
    });
    tinymce.PluginManager.add('blaster_slider', tinymce.plugins.blaster_slider);
})();
