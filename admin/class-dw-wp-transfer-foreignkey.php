<?php

/**
 * Class defining a foreign key from between post types. Both post types
 * can be the same.
 * 
 * A foreign key connects two fields of posts. In most cases, it connects
 * some custom field to the ID of another post. This class keeps metadata
 * about the two participating fields and knows how to read values from 
 * or write values to them.
 * 
 * @link       http://www.dasdware.de
 * @since      1.0.0
 * 
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 */
class DW_WP_Transfer_ForeignKey {
    
    /**
     * Post type of the origin post. 
     * @var string $origin_post_type
     */
    private $origin_post_type;

    /**
     * Field of the origin post.
     * @var string $origin_field
     */
    private $origin_field;

    /**
     * Post type of the target post.
     * @var string $target_post_type
     */
    private $target_post_type;

    /**
     * Field of the target post.
     * @var string $target_field
     */
    private $target_field;

    /**
     * Flag indicating whether or not the field of the origin post is a 
     * custom field.
     * @var boolean $origin_field_is_custom
     */
    private $origin_field_is_custom;

    /**
     * Flag indicating whether or not the field of the target post is a
     * custom field.
     * @var boolean $target_field_is_custom
     */
    private $target_field_is_custom;

    /**
     * Create a new foreign key descriptor.
     * 
     * @param string  origin_post_type       Post type of the origin post. 
     * @param string  origin_field           Field of the origin post.
     * @param string  target_post_type       Post type of the target post.
     * @param string  target_field           Field of the target post.
     * @param boolean origin_field_is_custom Flag indicating whether or not the 
     *                                       field of the origin post is a custom
     *                                       field. Default value is false.
     * @param boolean target_field_is_custom Flag indicating whether or not the 
     *                                       field of the target post is a custom
     *                                       field. Default value is false.
     */
    function __construct($origin_post_type, $origin_field, $target_post_type, $target_field,
            $origin_field_is_custom = false, $target_field_is_custom = false) {
        $this->origin_post_type = $origin_post_type;
        $this->origin_field = $origin_field;
        $this->origin_field_is_custom = $origin_field_is_custom;
        $this->target_post_type = $target_post_type;
        $this->target_field = $target_field;
        $this->target_field_is_custom = $target_field_is_custom;
    }

    /**
     * Get the name of this foreign key. It uniquely identifies it in the
     * set of possible foreign keys and can be used as array key to cache
     * values during import and export.
     * 
     * @return string name of this foreign key.
     */
    function get_name() {
        return $this->origin_post_type . '.' . $this->origin_field 
            . '->' . $this->target_post_type . '.' . $this->target_field;
    }

    /**
     * Test if the given post can be an origin post for this foreign key.
     * That is the case if the @link $origin_post_type equals the type of
     * the given post.
     * 
     * @param WP_Post $post The post that should be tested.
     * 
     * @return boolean Whether or not the given post is an origin post.
     */
    function is_origin_post($post) {
        return $post['post_type'] === $this->origin_post_type;
    }

    /**
     * Test if the given post can be a target post for this foreign key.
     * That is the case if the @link $target_post_type equals the type of
     * the given post.
     * 
     * @param WP_Post $post The post that should be tested.
     * 
     * @return boolean Whether or not the given post is an target post.
     */
    function is_target_post($post) {
        return $post['post_type'] === $this->target_post_type;
    }

    /**
     * Internal function for getting the value from the data of a post. 
     * Use @link get_origin_value() or @link get_target_value() to call 
     * this. Fails if the post is not of the correct type.
     * 
     * @param string  $post_type       Type of the post.
     * @param string  $field           Field of the post.
     * @param boolean $field_is_custom Flag indicating whether or not the
     *                                 post field is a custom field.
     * @param WP_Post $post            The post from which to retrieve the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return string|boolean The requested value or false if it cannot be
     *                        retrieved.
     * 
     * @see get_origin_value()
     * @see get_target_value()
     */
    private function get_value($post_type, $field, $field_is_custom, $post, $custom_fields) {
        if ($post['post_type'] == $post_type) {
            if (!$field_is_custom) {
                return $post[$field];
            } else {
                return $custom_fields[$field];
            }
        } else {
            return false;
        }
    }

    /**
     * Internal function for setting the value from the data of a post.
     * Use @link set_origin_value() or @link set_target_value() to call 
     * this. Fails if the post is not of the correct type.
     * 
     * @param string  $post_type       Type of the post.
     * @param string  $field           Field of the post.
     * @param boolean $field_is_custom Flag indicating whether or not the
     *                                 post field is a custom field.
     * @param WP_Post $post            The post of which to update the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return boolean Flag indicating whether or not the field could be
     *                 sucessfully set.
     * 
     * @see set_origin_value()
     * @see set_target_value()
     */
    private function set_value($post_type, $field, $field_is_custom, &$post, &$custom_fields, $value) {
        if ($post['post_type'] == $post_type) {
            if (!$field_is_custom) {
                $post[$field] = $value;
            } else {
                $custom_fields[$field] = $value;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the origin field value of the given post with respect to this 
     * foreign key.
     * 
     * @param WP_Post $post            The post from which to retrieve the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return string|boolean The requested value or false if it cannot be
     *                        retrieved.
     */
    function get_origin_value($post, $custom_fields) {
        return $this->get_value($this->origin_post_type, $this->origin_field, $this->origin_field_is_custom, 
            $post, $custom_fields);
    }

    /**
     * Set the target field value of the given post with respect to this
     * foreign key.
     * 
     * @param WP_Post $post            The post of which to update the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return boolean Flag indicating whether or not the field could be
     *                 sucessfully set.
     */
    function set_origin_value(&$post, &$custom_fields, $value) {
        return $this->set_value($this->origin_post_type, $this->origin_field, $this->origin_field_is_custom,
            $post, $custom_fields, $value);
    }

    /**
     * Get the target field value of the given post with respect to this 
     * foreign key.
     * 
     * @param WP_Post $post            The post from which to retrieve the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return string|boolean The requested value or false if it cannot be
     *                        retrieved.
     */
    function get_target_value($post, $custom_fields) {
        return $this->get_value($this->target_post_type, $this->target_field, $this->target_field_is_custom, 
            $post, $custom_fields);
    }

    /**
     * Set the target field value of the given post with respect to this
     * foreign key.
     * 
     * @param WP_Post $post            The post of which to update the
     *                                 data.
     * @param array   $custom_fields   The custom fields fo the post.
     * 
     * @return boolean Flag indicating whether or not the field could be
     *                 sucessfully set.
     */
    function set_target_value(&$post, &$custom_fields, $value) {
        return $this->set_value($this->target_post_type, $this->target_field, $this->target_field_is_custom,
            $post, $custom_fields, $value);
    }
}
?>