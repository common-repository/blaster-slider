<?php

class blasterSliderShortcodes {

    private function emtly_cmb2_checkbox ($in) {
    $ret = 'false';
        if ($in=='on' || $in=='yes' || $in=='1' || $in==1) {
            $ret = 'true';
        }
    return $ret;
    }

    /**
     * Gets the post attributes
     *
     * @param $id
     * @return array
     */
    public function get_post_attributes($id) {

        if (!is_numeric($id)) {

            $blasterSliderPost = get_page_by_path($id, OBJECT, 'blaster_slider');

            if ($blasterSliderPost instanceof WP_Post) {
                $id = $blasterSliderPost->ID;
            }

        } else {
            $blasterSliderPost = get_post($id);
        }

        if (!($blasterSliderPost instanceof WP_Post)) {
            $this->error(__('Blaster Slider could not be found...', 'blaster_slider'));
            return null;
        }


        $attributes = array();

        $attributes['timeout'] = get_post_meta($id, 'blaster_slider_speed', true);
        $attributes['id'] = get_post_meta($id, 'blaster_slider_id', true);
        $attributes['height'] = get_post_meta($id, 'blaster_slider_height', true);
        $attributes['speed'] = get_post_meta($id, 'blaster_slider_speed', true);
        $attributes['animation'] = get_post_meta($id, 'blaster_slider_animation', true);
        $attributes['nav'] = get_post_meta($id, 'blaster_slider_nav', true);
        $attributes['arrows'] = get_post_meta($id, 'blaster_slider_arrows', true);

        $attributes['caption'] = get_post_meta($id, 'blaster_slider_caption', true);

        $attributes['hover_pause'] = get_post_meta($id, 'blaster_slider_hover_pause', true);
        $attributes['caption_shadow'] = get_post_meta($id, 'blaster_slider_caption_shadow', true);
        $attributes['title_color'] = get_post_meta($id, 'blaster_slider_title_color', true);
        $attributes['desc_color'] = get_post_meta($id, 'blaster_slider_desc_color', true);

//        $attributes['label_positions'] = get_post_meta($id, 'blaster_slider_label_positions', true);

        $sources = array();
        $titles = array();
        $descriptions = array();
        $links = array();

        $extras = get_post_meta($id, 'blaster_slider_extra', true);
        $images = get_post_meta($id, 'blaster_slider_images_group', true);

//print_r($images);

        if (is_array($extras)) {

            foreach ($extras as $extra) {
                $attributes[$extra] = 'yes';
            }

        }

        if (is_array($images)) {

            foreach ($images as $image) {
                $sources[] = $image['image'];
                $titles[] = $image['title'];
                $descriptions[] = $image['description'];
                if (isset($image['link'])) {
                    $links[] = $image['link'];
                } else {
                    $links[] = '';
                }
            }

        }

        $attributes['sources'] = join(',', $sources);
        $attributes['titles'] = join(',', $titles);
        $attributes['descriptions'] = join(',', $descriptions);
        $attributes['links'] = join(',', $links);

        return $this->attributes($attributes);

    }

    /**
     * Formats the attributes
     *
     * @param $attributes
     * @return array
     */
    private function attributes($attributes) {

        $settings = BlasterSlider::get_settings();

        $attributes = shortcode_atts(array(
            'id'              => '',
            'speed' => 5000,
            'timeout' => 5000,
            'animation' => 'fade',
            'nav' => false,
            'arrows' => false,
            'caption' => false,
            'height' => 300,
            'hover_pause' => false,
            'caption_shadow' => false,
            'title_color' => '#000',
            'desc_color' => '#000',
            'labels'          => __('BlasterSlider', 'blaster_slider'),
            'links' => '',
            'titles' => '',
            'descriptions' => '',
            'label_positions' => '',
            'sources'         => ''
        ), $attributes, 'blaster_slider');


        return $attributes;

    }


    public function generate_guid() {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    /**
     * Renders the ex slider element
     *
     * @param $args
     * @param null $content
     * @return null
     */
    public function blaster_slider($args, $content = null) {

        $attr = $this->attributes($args);

//        print_r($attr);

        $sources = array();
        $titles = array();
        $descriptions = array();
        $links = array();

        if (!empty($attr['id'])) {

            $attr = $this->get_post_attributes($attr['id']);

            if (empty($attr)) {
                return null;
            }

        }




        $classes = array();
        $classes[] = 'blaster-slider';

//        $attributes = array();

//        $attributes['class'] = join(' ', $classes);


        if (!empty($attr['sources'])) {

            $sources = explode(',', $attr['sources']);

            if(is_numeric(current($sources))){
                $sources = array_map('wp_get_attachment_url', $sources);
            }

        }

        if (!empty($attr['titles'])) {
            $titles = explode(',', $attr['titles']);
        }
        if (!empty($attr['descriptions'])) {
            $descriptions = explode(',', $attr['descriptions']);
        }

        if (!empty($attr['links'])) {
            $links = explode(',', $attr['links']);
        }

        $slides = array();
        $slides[] = array(
            'sources' => $sources,
            'titles' => $titles,
            'descriptions' => $descriptions,
            'links' => $links
        );

        $output = '';

        foreach($slides as $slide){

            $guid = 'blaster-' . $this->generate_guid();

            $output.= "<script>jQuery(function($){";
            $output.= "$('#" . $guid . "').BlasterSlider({nav: " . $this->emtly_cmb2_checkbox($attr['nav']) . ", arrows: " . $this->emtly_cmb2_checkbox($attr['arrows']) . ", hover_pause: " . $this->emtly_cmb2_checkbox($attr['hover_pause']) . ", caption_shadow: " . $this->emtly_cmb2_checkbox($attr['caption_shadow']) . ", caption: " . $this->emtly_cmb2_checkbox($attr['caption']) . ", height: " . intval($attr['height']) . ", time: " . intval($attr['timeout']) . ", animation: '" . $attr['animation'] . "', title_color: '" . $attr['title_color'] . "', desc_color: '" . $attr['desc_color'] . "'});";
            $output.= "});</script>";

            $output.= '<div class="blaster-slider">';

            $output.= '<ul id="' . $guid . '">';

//            if ( get_post_meta( get_the_ID(), 'wiki_test_checkbox', 1 ) )

            foreach ($slide['sources'] as $i => $source) {

                $output.= '<li>';

                if (!empty($slide['links'][$i])) {
//                    $output.= '<a href="'.$slide['links'][$i].'">';
                }

                $output.= '<div class="image-overlay">';

                $tmp_alt = '';

                if (!empty($slide['titles'][$i])) {
                    $tmp_alt = $slide['titles'][$i];
                    $output.= '<h1>' . $slide['titles'][$i] . '</h1>';
                }

                if (!empty($slide['descriptions'][$i])) {
                    $output.= '<h2>' . $slide['descriptions'][$i] . '</h2>';
                }

                $output.= '</div>';

                $output.= '<img src="' . $source . '" alt="' . $tmp_alt . '" />';

                if (!empty($slide['links'][$i])) {
//                    $output.= '</a>';
                }

                $output.= '</li>';

            }

            $output.= '</ul>';
            $output.= '</div>';



        }



        return $output;

    }

    public function error($message) {

        return '<div class="blaster-slider-error">' . $message . '</div>';

    }


}
