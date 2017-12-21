=== dasdware WordPress Transfer ===

WordPress plugin for transfering structured post data between installations.

== Description ==

This plugin transfers data from WordPress posts via JSON. The process is subdivided in exporting data from one installation and importing it into another installation.

The aim of the plugin is to keep referential integrity between posts of different types that belong together.

The import does not delete any data. That would have to be done manually beforehand. Therefore, multiple imports lead to multiple copies of the same posts.

== Installation ==

The plugin can be installed by copying the plugin folder into the `plugins` subdirectory of a WordPress installation. After activating, it is available in the tools menu of the WordPress Admin area.

By default, the plugin can only handle posts. Via addon plugins, other post types can be added.

== Adding post type descriptors ==

The plugin uses the `dw_wp_transfer_add_descriptors` action to register additional transfer descriptors (of type `DW_WP_Transfer_Descriptor`). Functions registered with this action receive a callback for actually adding information:

    function add_my_descriptor($add_callback) {
      $add_callback(
        new DW_WP_Transfer_Descriptor(
	      'descriptor-name',
		  'Descriptor Title (shown in UI)', 
		  array('post_type_1', 'post_type_2'),
		  array()
		)
      );
    }

The first two parameters to the callback are the internal name of the transfer descriptor and a meaningful title that is displayed to the user. The third parameter is a list of the (custom) post types that should be transfered. They will be exported and imported in the given order.

The last parameter is a list of foreign key constraints between the post types (of type `DW_WP_Transfer_ForeignKey`) and can be omitted. A foreign key is defined as follows:

    new DW_WP_Transfer_ForeignKey(
        'origin_post_type', 
        'origin_post_field', 
        'target_post_type', 
        'target_post_field', 
        origin_field_is_custom, 
        target_field_is_custom
    )

The first four parameters describe the origin and target post types and fields, the last two are flags that should be set to `true` if the corresponding field is a custom field, `false` otherwise.