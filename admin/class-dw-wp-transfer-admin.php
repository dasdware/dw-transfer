<?php

require_once 'class-dw-wp-transfer-descriptors.php';
require_once 'class-dw-wp-transfer-export.php';
require_once 'class-dw-wp-transfer-import.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 * @author     dasd.ware <mail@dasdware.de>
 */
class DW_WP_Transfer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Flag indicating whether there was an error during the last export/import
	 * run.
	 *
	 * @var boolean
	 */
	private $have_error;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Dw_Transfer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Dw_Transfer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/dw-transfer-admin.css', array(), $this->version, 'all' );
	}

	public function add_menu_page() {
		add_management_page( 'DW Transfer', 'DW Transfer', 
			'install_plugins', 'dw-transfer', array( $this, 'display' ));
	}

	public function add_descriptors($add_descriptor) {
        // Add Standard descriptor for posts
        $add_descriptor(
            new DW_WP_Transfer_Descriptor(
                'posts', 
                'Posts', 
                array('post')
            )
        );
	}	

	public function error($message) {
		echo "<div class=\"notice notice-error\"><p>$message</p></div>";
		$this->have_error = true;
	}

	public function display() {
		$transfer_type = (!empty($_POST['transfer_type'])) ? $_POST['transfer_type'] : false;
		$transfer_file = (!empty($_FILES['transfer_file'])) ? $_FILES['transfer_file'] : false;
		$transfer_descriptor = DW_Transfer_Descriptors::get_instance()->get_descriptor($transfer_type);

		$error_callback = function ($message) {
			$this->error($message);
		};		

		if (isset($_POST['dw-transfer-export'])) {
			$this->have_error = false;

			$export = new DW_Transfer_Export($transfer_descriptor);
			$export->run($error_callback);

			if ($this->have_error) {
				require_once 'partials/dw-wp-transfer-admin-display.php';
			}
		} else if (isset($_POST['dw-transfer-import'])) {
			$import = new DW_Transfer_Import($transfer_descriptor, $transfer_file);
			$import->run($error_callback);

			require_once 'partials/dw-wp-transfer-admin-display.php';
		} else {
			require_once 'partials/dw-wp-transfer-admin-display.php';
		}
	}	
}
