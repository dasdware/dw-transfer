<?php

/**
 * Class defining a description of a transfer.
 * 
 * A transfer descriptor describes how to perform a certain transfer 
 * (import and export).
 * 
 * @link       http://www.dasdware.de
 * @since      1.0.0
 * 
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 */
class DW_WP_Transfer_Descriptor {

    /**
     * The name of the transfer. This key is internally used to 
     * recognize the transfer and therefore should be unique.
     * 
     * @var string $name
     */
    public $name;

    /**
     * The caption of the transfer. This is displayed to the user.
     * 
     * @var string $caption
     */
    public $caption;

    /**
     * The post types that are concerned by this transfer.
     * 
     * @var array $post_types
     */
    public $post_types;

    /**
     * A list of foreign keys concerned by this transfer. This is a list
     * of DW_Transfer_ForeignKey instances.
     * 
     * @var array $foreign_keys
     */
    public $foreign_keys;

    /**
     * Create a new transfer descriptor.
     * 
     * @param string $name        The name of the transfer.
     * @param string $caption     The caption of the transfer  
     * @param array $post_types   The post types that are concerned by 
     *                            this transfer.
     * @param array $foreign_keys A list of foreign keys concerned by this
     *                            transfer.
     */
    function __construct($name, $caption, $post_types, $foreign_keys = array()) {
        $this->name = $name;
        $this->caption = $caption;
        $this->post_types = $post_types;
        $this->foreign_keys = $foreign_keys;
    }
}
?>