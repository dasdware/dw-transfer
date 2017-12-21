<?php
require_once 'class-dw-wp-transfer-foreignkey.php';
require_once 'class-dw-wp-transfer-descriptor.php';

/**
 * A registry for managing transfer descriptors.
 * 
 * @link       http://www.dasdware.de
 * @since      1.0.0
 * 
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 */
class DW_Transfer_Descriptors {

    /**
     * The list of registered descriptors.
     * 
     * @var array $descriptors
     */
    public $descriptors = array();

    /**
     * Create a new instance of this registry. On initialization, it
     * executes the dw_transfer_add_descriptors action.
     */
    private function __construct() {
        do_action('dw_wp_transfer_add_descriptors', array($this, 'add_descriptor'));
    }

    /**
     * Add a new descriptor to this registry. This method should not be
     * called directly. Rather you should use the
     * dw_transfer_add_descriptors action.
     * 
     * @param DW_Transfer_Descriptor $descriptor The descriptor that 
     *          should be added to the registry.
     */
    function add_descriptor($descriptor) {
        $this->descriptors[] = $descriptor;
    }

    /**
     * Get the transfer descriptor with the given name.
     * 
     * @param string $name The name of the descriptor that should be
     *                     retrieved.
     * @return DW_Transfer_Descriptor|boolean The transfer descriptor
     *                     with the given name or false if there is
     *                     none.
     */
    function get_descriptor($name) {
        foreach ($this->descriptors as $descriptor) {
            if ($descriptor->name === $name) {
                return $descriptor;
            }
        }
        return false;
    }

    /**
     * The static singleton instance of this class. Will be created when
     * needed.
     * 
     * @var DW_Transfer_Descriptors $instance
     */
    private static $instance = false;

    /**
     * Get the singleton instance of this class.
     * 
     * @return DW_Transfer_Descriptors The singleton instance of this 
     *           class.
     */
    public static function get_instance() {
        if (self::$instance === false) {
            self::$instance = new DW_Transfer_Descriptors;
        }
        return self::$instance;
    }
}
?>