<?php
/*
Plugin Name: Blaster Slider
Plugin URI: https://www.ampae.com/wp/blaster-slider/
Description: Create unlimited Responsive Image Sliders and Carousels from your photos with this easy to use WordPress plugin.
Author: AMPAE Software
Version: 1.3
Author URI: https://www.ampae.com/wp/
*/

/**
 * Plugin version
 */
define('BLASTER_SLIDER_VERSION', '1.3' . time());
define('BLASTER_SLIDER_DIR', __DIR__);

/**
 * Class blasterSlider
 */
class blasterSlider {

    /**
     * Dispatches the class instance
     */
    public static function dispatch() {
        new blasterSlider;
    }

    /**
     * Initiates hooks and template tags
     */
    public function __construct() {

        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'editor_button'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        add_action('wp_ajax_blaster_slider_editor', array($this, 'editor'));

//        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

//        add_action('vc_before_init', array($this, 'visual_composer'));

        if (is_admin()) {
            require_once 'assets/plugins/cmb2/init.php';
            add_action('cmb2_init', array($this, 'meta_boxes'));
        }
        $this->shortcodes();
    }

    public function editor() {
        require BLASTER_SLIDER_DIR . '/assets/admin/dialog.php';
        exit;
    }
/*
    public function admin_menu() {

        add_submenu_page(
            'options-general.php',
            __('Blaster Slider Settings', 'blaster_slider'),
            __('Blaster Slider', 'blaster_slider'),
            'manage_options',
            'blaster_slider',
            array($this, 'admin_page')
        );

    }
*/

    public function shortcodes() {
        require_once BLASTER_SLIDER_DIR . '/assets/runtime/shortcodes.php';
        $shortcodes = new blasterSliderShortcodes();
        add_shortcode('blaster_slider', array($shortcodes, 'blaster_slider'));
    }

    public function get_posts() {
        $blasterSliderList = array();
        $blasterSliderList[''] = '';
        $blasterSliderPosts = get_posts(array(
            'post_type' => 'blaster_slider',
            'nopaging' => true
        ));
        foreach($blasterSliderPosts as $blasterSliderPost){
            $blasterSliderList[$blasterSliderPost->post_title] = $blasterSliderPost->ID;
        }
        return $blasterSliderList;
    }

    public function editor_button() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'register_editor_plugin'));
            add_filter( 'mce_buttons', array($this, 'add_editor_button'));
        }
    }

    public function register_editor_plugin($plugins) {
        $plugins['blaster_slider'] = plugins_url('/assets/admin/js/dialog.js', __FILE__);
        return $plugins;
    }

    public function add_editor_button($buttons) {
        array_push($buttons, "blaster_slider");
        return $buttons;
    }

    /**
     * Register the custom post type
     */
    public function register_post_type() {
        register_post_type('blaster_slider', array(
            'labels'              => array(
                'name'               => __('Blaster Slider', 'blaster_slider'),
                'singular_name'      => __('Blaster Slider', 'blaster_slider'),
                'menu_name'          => __('Blaster Slider', 'blaster_slider'),
                'name_admin_bar'     => __('Blaster Slider', 'blaster_slider'),
                'add_new'            => __('Add New', 'blaster_slider'),
                'add_new_item'       => __('Add New Blaster Slider', 'blaster_slider'),
                'new_item'           => __('New Blaster Slider', 'blaster_slider'),
                'edit_item'          => __('Edit Blaster Slider', 'blaster_slider'),
                'view_item'          => __('View Blaster Slider', 'blaster_slider'),
                'all_items'          => __('All Blaster Slider', 'blaster_slider'),
                'search_items'       => __('Search Blaster Slider', 'blaster_slider'),
                'parent_item_colon'  => __('Parent Blaster Slider:', 'blaster_slider'),
                'not_found'          => __('No Blaster Slider found.', 'blaster_slider'),
                'not_found_in_trash' => __('No Blaster Slider found in Trash.', 'blaster_slider')
            ),
            'public'              => true,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
		'menu_position' => 5.15,
            'menu_icon'         => 'dashicons-art',
            'supports'            => array('title')
        ));

    }

    public static function get_settings() {
        $settings = wp_parse_args(get_option('blaster_slider_settings'), array(
            'color' => '#ffffff',
            'style' => 'default',
            'custom_css' => ''
        ));
        return $settings;
    }

    /**
     * Enqueues the front-end scripts and stylesheets
     */
    public function enqueue_scripts() {
        $settings = self::get_settings();
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-resizable');
        wp_enqueue_script('jquery-effects-core');

        wp_register_script('blaster-slider-hmr-script', plugins_url('assets/plugins/hammer.min.js', __FILE__), array( 'jquery' ));

        wp_register_script('blaster-slider-script', plugins_url('/assets/runtime/js/slider.js', __FILE__), array('jquery'), BLASTER_SLIDER_VERSION);

        $script_data = array(
			'img_path' => plugins_url('assets/img/', __FILE__)
		);
		wp_localize_script(
			'blaster-slider-script',
			'blaster_slider_data',
			$script_data
		);



//        wp_enqueue_script('blaster-slider', plugins_url('/assets/runtime/js/slider.js', __FILE__), array('jquery'), BLASTER_SLIDER_VERSION);

        wp_enqueue_script('blaster-slider-script');
        wp_enqueue_script('blaster-slider-hmr-script');
        wp_enqueue_style('blaster-slider', plugins_url('/assets/runtime/css/slider.css', __FILE__), null, BLASTER_SLIDER_VERSION);
        wp_add_inline_style('blaster-slider', $settings['custom_css']);
    }

    /**
     * Enqueues the back-end scripts and stylesheets
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('blaster-slider', plugins_url('/assets/admin/js/admin.js', __FILE__), array('jquery'), BLASTER_SLIDER_VERSION, true);
        wp_enqueue_style('blaster-slider', plugins_url('/assets/admin/css/admin.css', __FILE__), null, BLASTER_SLIDER_VERSION);
    }


    private function populate_g() {
        global $blaster_slider_x1x_data, $blaster_slider_x1x_options;

        $blaster_slider_options_array_tmp = get_option( $blaster_slider_x1x_data['options_name'] );

        $tmp_fonts = $this->get_json_data( plugin_dir_path( __FILE__ ) . 'cache/google_fonts.json' );
        $tmp_font_arr = array();
        foreach ($tmp_fonts['items'] as $tmp_font_item) {
            $tmp_font_arr[$tmp_font_item] = $tmp_font_item;
        }
        return $tmp_font_arr;
    }
    // !!! move to functions..
    private function get_json_data($fname,$dt='true') {

        $ct_raw_config =  @file_get_contents($fname);

        $ct_raw_config = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $ct_raw_config);
        $ct_raw_config = str_replace('\\', '\\\\', $ct_raw_config);
        $ct_arr_config = json_decode($ct_raw_config, $dt);
        return $ct_arr_config;
    }
    /**
     * Adds the post meta boxes
     */
    public function meta_boxes() {
        $prefix = 'blaster_slider_';
        $meta_box_options = new_cmb2_box(array(
            'id'           => $prefix . 'options',
            'title'        => __('Options', 'blaster_slider'),
            'object_types' => array('blaster_slider'),
            'context'      => 'normal',
            'priority'     => 'high',
            'show_names'   => true,
        ));
/*
        $meta_box_options->add_field(array(
            'name'    => __('Font Title', 'blaster_slider'),
            'desc'    => __('Font Title', 'blaster_slider'),
            'id'      => $prefix . 'font_title',
            'type'    => 'select',
            'options' => $this->populate_g()
        ));
        $meta_box_options->add_field(array(
            'name'    => __('Font Description', 'blaster_slider'),
            'desc'    => __('Font Description', 'blaster_slider'),
            'id'      => $prefix . 'font_desc',
            'type'    => 'select',
            'options' => $this->populate_g()
        ));
        // sizes, positions, shadow color,
*/
        $meta_box_options->add_field(array(
            'name' => __('Height', 'blaster_slider'),
            'id'   => $prefix . 'height',
            'description' => __('in px. Slider Height.', 'blaster_slider'),
            'type' => 'text_small',
            'attributes' => array(
		        'type' => 'number',
		        'min' => '50',
		        'max' => '1000',
		        'step' => '10',
	        ),
        ));
        $meta_box_options->add_field(array(
            'name' => __('Speed', 'blaster_slider'),
            'id'   => $prefix . 'speed',
            'description' => __('in ms. 1000ms. = 1s. Slider Speed.', 'blaster_slider'),
            'type' => 'text_small',
            'attributes' => array(
		        'type' => 'number',
		        'min' => '500',
		        'max' => '50000',
		        'step' => '500',
	        ),
        ));
        $meta_box_options->add_field(array(
            'name'    => __('Animation Type', 'blaster_slider'),
            'desc'    => __('Animation Type', 'blaster_slider'),
            'id'      => $prefix . 'animation',
            'type'    => 'select',
            'options' => array(
                'fade' => __('Fade', 'blaster_slider'),
                'slide'   => __('Slide', 'blaster_slider')
            )
        ));
        $meta_box_options->add_field(array(
            'name' => __('Nav', 'blaster_slider'),
            'id'   => $prefix . 'nav',
            'description' => __('Nav.', 'blaster_slider'),
            'type' => 'checkbox',
        ));
        $meta_box_options->add_field(array(
            'name' => __('Arrows', 'blaster_slider'),
            'id'   => $prefix . 'arrows',
            'description' => __('Arrows.', 'blaster_slider'),
            'type' => 'checkbox',
        ));
        $meta_box_options->add_field(array(
            'name' => __('Caption', 'blaster_slider'),
            'id'   => $prefix . 'caption',
            'description' => __('Caption.', 'blaster_slider'),
            'type' => 'checkbox',
        ));
        $meta_box_options->add_field(array(
            'name' => __('Pause on Mouse Hover', 'blaster_slider'),
            'id'   => $prefix . 'hover_pause',
            'description' => __('Pause on Mouse Hover.', 'blaster_slider'),
            'type' => 'checkbox',
        ));
                $meta_box_options->add_field(array(
            'name' => __('Caption Shadow', 'blaster_slider'),
            'id'   => $prefix . 'caption_shadow',
            'description' => __('Caption Shadow.', 'blaster_slider'),
            'type' => 'checkbox',
        ));
        $meta_box_options->add_field(array(
            'name'    => __('Title Color', 'blaster_slider'),
            'desc'    => __('Title Color.', 'blaster_slider'),
            'id'      => $prefix . 'title_color',
            'type'    => 'colorpicker',
            'default' => '#ffffff',
        ));
        $meta_box_options->add_field(array(
            'name'    => __('Description Color', 'blaster_slider'),
            'desc'    => __('Description Color.', 'blaster_slider'),
            'id'      => $prefix . 'desc_color',
            'type'    => 'colorpicker',
            'default' => '#ffffff',
        ));
/*
if ( get_post_meta( get_the_ID(), 'wiki_test_checkbox', 1 ) )
*/
        $meta_box_images = new_cmb2_box(array(
            'id'           => $prefix . 'images',
            'title'        => __('Images', 'blaster_slider'),
            'object_types' => array('blaster_slider'),
        ));

        $group_field_id = $meta_box_images->add_field(array(
            'id'          => $prefix . 'images_group',
            'type'        => 'group',
            'description' => __('Add one or more images to the Slider.', 'blaster_slider'),
            'options'     => array(
                'group_title'   => __('Image {#}', 'blaster_slider'),
                'add_button'    => __('Add Image', 'blaster_slider'),
                'remove_button' => __('Remove Image', 'blaster_slider'),
                'sortable'      => true
            )
        ));

        $meta_box_images->add_group_field($group_field_id, array(
            'name' => __('Title', 'blaster_slider'),
            'id'   => 'title',
            'description' => __('Title will appear within the label of the current image.', 'blaster_slider'),
            'type' => 'text',
        ));

        $meta_box_images->add_group_field($group_field_id, array(
            'name' => __('Description', 'blaster_slider'),
            'id'   => 'description',
            'description' => __('Description will appear within the label of the current image.', 'blaster_slider'),
            'type' => 'text',
        ));
/*
        $meta_box_images->add_group_field($group_field_id, array(
            'name' => __('Link', 'blaster_slider'),
            'id'   => 'link',
            'description' => __('Here you can optionally add a URL to which the image will link if it is clicked.', 'blaster_slider'),
            'type' => 'text_url',
        ));
*/
        $meta_box_images->add_group_field($group_field_id, array(
            'name' => __('Image', 'blaster_slider'),
            'id'   => 'image',
            'description' => __('Image', 'blaster_slider'),
            'type' => 'file'
        ));
    }
}

blasterSlider::dispatch();

function blaster_slider_meta_box_callback ($blasterSliderPost) {
   echo '[blaster_slider id="' . $blasterSliderPost->ID . '"]';
}
function blaster_slider_meta_box() {
    add_meta_box(
        'blaster-slider-shortcode-box',
        __( 'Blaster Slider ShortCode', 'blaster_slider' ),
        'blaster_slider_meta_box_callback',
        'blaster_slider',
        'side'
    );
}
add_action( 'add_meta_boxes', 'blaster_slider_meta_box' );
function blaster_slider_add_column( $columns ) {
	$columns['blaster_slider_post_id_clmn'] = 'Slider ShortCode';
	return $columns;
}
add_filter('manage_edit-blaster_slider_columns', 'blaster_slider_add_column', 5);
function blaster_slider_column_content( $column, $id ){
	if( $column === 'blaster_slider_post_id_clmn')
		echo '[blaster_slider id="' . $id . '"]';
}
add_action( 'manage_blaster_slider_posts_custom_column', 'blaster_slider_column_content', 5, 2);
