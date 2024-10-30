<?php

$blasterSliderPosts = array();
$blasterSliderPosts[''] = '';

$blasterSliderList = get_posts(array(
    'post_type' => 'blaster_slider',
    'nopaging' => true
));

foreach($blasterSliderList as $blasterSliderPost){
    $blasterSliderPosts[$blasterSliderPost->ID] = $blasterSliderPost->post_title;
}

function blaster_slider_select($id, $options) {
    echo '<select id="' . $id . '">';
    foreach($options as $value => $label){
        echo '<option value="' . $value . '">' . $label . '</option>';
    }
    echo '</select>';
}
?>
<div class="blaster-slider-editor">
    <table class="form-table">
        <?php if (count($blasterSliderPosts) > 1): ?>
            <tr>
                <th>
                    <label for="field_id"><?php _e('Blaster Slider Item', 'blaster_slider'); ?></label>
                </th>
                <td>
                    <?php blaster_slider_select('field_id', $blasterSliderPosts); ?>
                    <div class="field-description">
                        <?php _e('Select Slider to insert:', 'blaster_slider'); ?>
                    </div>
                </td>
            </tr>
        <?php endif; ?>

       <tr>
           <th colspan="2">
               <input id="field_submit" class="button button-primary" type="button" value="<?php _e('Generate Shortcode', 'blaster_slider'); ?>" />
           </th>
       </tr>
    </table>
</div>
<script>
    blasterSliderEditor.init();
</script>
