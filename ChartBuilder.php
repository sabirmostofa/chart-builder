<?php

/*
  Plugin Name: WP-Chart-Builder
  Plugin URI: http://top-wpp.com
  Description: Add Beautiful Charts in your WordPress Site 
  Version: 1.0
  Author: Sabirul Mostofa
  
 */

$wpChartBuilder = new wpChartBuilder();

class wpChartBuilder {

    public $table = '';
    public $image_dir = '';
    public $prefix = 'wpchart';
    public $meta_box = array();

    function __construct() {
        global $wpdb;
        //$this->set_meta();
       // $this->table = $wpdb->prefix . 'wb_country_list';
        $this->image_dir = plugins_url('/', __FILE__) . 'images/';
        $this->xml_file = plugins_url('/', __FILE__) . 'countries.xml';
        //add_action('init', array($this, 'add_post_type'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
        add_action('wp_print_styles', array($this, 'front_css'));
        add_action('admin_menu', array($this, 'CreateMenu'), 50);
        //add_action('wp_mem_dues_cron', array($this, 'start_cron'));
        add_filter('the_content', array($this, 'generate_content') );
/*
		add_action('wp_ajax_membership_remove', array($this, 'ajax_remove_membership'));
		add_action('wp_ajax_get_dues', array($this, 'ajax_return_data'));
		add_action('wp_ajax_nopriv_get_dues', array($this, 'ajax_return_data'));
*/
        //register_activation_hook(__FILE__, array($this, 'create_table'));
       // register_activation_hook(__FILE__, array($this, 'init_cron'));
        //register_activation_hook(__FILE__, array($this, 'create_page'));
        //register_activation_hook(__FILE__, array($this, 'set_memberships'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation_tasks'));
    }

    function CreateMenu() {
        add_submenu_page('options-general.php', 'Chart Settings', 'Chart Settings', 'activate_plugins', 'wpChartBuilder', array($this, 'OptionsPage'));
    }
    
    function create_page(){		
		$page = array(
		'post_type' => 'page',
		'post_content' => '',
		'post_title' => 'Odds Comparison',
		'post_author' => 1,
		'post_status' => 'publish'
		
		);
		
		if(!get_option('wp_odd_com_page')){
			$page_no = wp_insert_post($page);
			update_option('wp_odd_com_page', $page_no);
		}
	}
	
	function generate_content($content){
	global $post, $wpdb;
	$mem_page = get_option('wp_odd_com_page');
	if($post->ID != $mem_page)
		return $content;
		
		return $extra;
	

		
	
	}
	
	function set_memberships(){
		if(get_option('wp_wb_memberships'))
			return;
	    $names = array('Full', 'Emeritus', 'Early-Career', 'Student');	    
	    $membership_array = array();
	    
	    foreach($names as $key => $value):
			$membership_array[sanitize_title_with_dashes($value)] = array(
					'name' => $value,
					'low_fee' => 0,
					'low_early' => 0,
					'medium_fee' => 0,
					'medium_early' => 0,
					'high_fee' => 0,
					'high_early' => 0
			);
	    endforeach; 
	    
		update_option('wp_wb_memberships', $membership_array);
		
		
		}
	function ajax_remove_membership(){
	$mems = get_option('wp_wb_memberships');
	$key = $_POST['id'];
	unset($mems[$key]);
	echo update_option('wp_wb_memberships', $mems);
	exit;
	}

	
    function OptionsPage() {
        include 'options-page.php';
    }



    
    

    function admin_scripts() {
		wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('datepicker', plugins_url('css/ui-lightness/jquery-ui-1.8.16.custom.css', __FILE__));
        wp_enqueue_script('wbdues_admin_script', plugins_url('/', __FILE__) . 'js/script_admin.js');
        wp_register_style('wbdues_admin_css', plugins_url('/', __FILE__) . 'css/style_admin.css', false, '1.0.0');
        wp_enqueue_style('wbdues_admin_css');

    }

    function front_scripts() {
        global $post;
        if (is_page() || is_single()) {
            wp_enqueue_script('jquery');
            if (!(is_admin())) {
                // wp_enqueue_script('wpvr_boxy_script', plugins_url('/' , __FILE__).'js/boxy/src/javascripts/jquery.boxy.js');
                wp_enqueue_script('wbdues_front_script', plugins_url('/', __FILE__) . 'js/script_front.js');
                wp_localize_script('wbdues_front_script', 'wpvrSettings', array(
                    'ajaxurl' => home_url('/').'wp-admin/admin-ajax.php',
                    'pluginurl' => plugins_url('/', __FILE__),
                 
                ));
            }
        }
    }

    function front_css() {
        if (!(is_admin())):
            wp_enqueue_style('wbdues_front_css', plugins_url('/', __FILE__) . 'css/style_front.css');
        endif;
    }



    function not_in_table($country_id) {
        global $wpdb;
        $var = $wpdb->get_var("select country_id from $this->table where country_id='$country_id'");
        if ($var == null)
            return true;
    }

    function create_table() {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS $this->table  (
		`id` int(4) unsigned NOT NULL AUTO_INCREMENT,
		`country_id` varchar(4) NOT NULL,		
		`country` varchar(60)  NOT NULL,	
		`income_level` varchar(6)  NOT NULL,	
		`income_text` varchar(60)  NOT NULL,	
		 PRIMARY KEY (`id`),				 	
		 key `country_id`(`country_id`)		 	
		)";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

    }

// end of create_table






 

    function deactivation_tasks() {

       // wp_clear_scheduled_hook('wp_rental_cron');
    }

}
