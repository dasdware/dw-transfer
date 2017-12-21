<?php

/**
 * Class defining the import portion of a transfer.
 * 
 * @link       http://www.dasdware.de
 * @since      1.0.0
 * 
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 */
class DW_Transfer_Import {

    /**
     * The descriptor that is used to import data.
     *
     * @var DW_Transfer_Descriptor
     */
    private $descriptor;

    /**
     * The array of the uploaded import file.
     *
     * @var array
     */
    private $file;

    /**
     * The callback that is used to display error messages.
     *
     * @var callback
     */
    private $error_callback;

    /**
     * Mappings of the targets of foreign keys during import.
     *
     * @var array
     */
    private $foreign_key_target_mappings = array();
    
    /**
     * Create a new import runner.
     *
     * @param DW_Transfer_Descriptor $descriptor The transfer descriptor that 
     *          should be used for the import.
     * @param array $file The array describing the uploaded file that should
     *          be imported.
     */
    public function __construct(DW_Transfer_Descriptor $descriptor, array $file) {
        $this->descriptor = $descriptor;
        $this->file = $file;
    }

    /**
     * Display an error message to the user. Uses the callback method provided
     * when running the import.
     *
     * @param string $message The error message that should be displayed.
     * @return void
     */
    private function error(string $message) {
        call_user_func($this->error_callback, $message);
    }

    /**
     * Display an error concerning the given post.
     *
     * @param string $message Error message to show.
     * @param array $fields Fields of the post for which to display the error.
     * @return void
     */
    private function post_error(string $message, array $fields) {
        $this->error("$message: $fields[post_type] \"$fields[post_title]\" (Id:  $fields[ID]) wird ignoriert.");
    }

    /**
     * Import the given post.
     *
     * @param array $fields Fields of the post to import.
     * @param array $custom_fields Custom fields of the post to import.
     * @return void
     */
    private function import_post(array $fields, array $custom_fields) {
        $this->error("$fields[post_type]:  $fields[ID] $fields[post_title]");

        $old_target_values = array();
        foreach ($this->descriptor->foreign_keys as $key) {
            $key_name = $key->get_name();
            if ($key->is_origin_post($fields) && isset($this->foreign_key_target_mappings[$key_name])) {
                $old_value = $key->get_origin_value($fields, $custom_fields);
                if (isset($this->foreign_key_target_mappings[$key_name][$old_value])) {
                    $new_value = $this->foreign_key_target_mappings[$key_name][$old_value];
                    if (!$key->set_origin_value($fields, $custom_fields, $new_value)) {
                        $this->post_error('Kann neuen Fremdschlüssel nicht setzen');
                        return;
                    }
                }
            } else if ($key->is_target_post($fields)) {
                $old_target_values[$key_name] = $key->get_target_value($fields, $custom_fields);
            }
        }

        // import the post itself, take the new id
        unset($fields['ID']);
        $insert_id = wp_insert_post($fields);
        if ($insert_id === false) {
            $this->post_error('Konnte Eintrag nicht der Datenbank hinzufügen');
            return;
        }
        $fields['ID'] = $insert_id;

        // import the custom fields of the post
        foreach ($custom_fields as $field => $value) {
            add_post_meta($insert_id, $field, $value, true);
        }

        foreach ($this->descriptor->foreign_keys as $key) {
            $key_name = $key->get_name();
            if ($key->is_target_post($fields) && isset($old_target_values[$key_name])) {
                $old_value = $old_target_values[$key_name];
                $new_value = $key->get_target_value($fields, $custom_fields);
                if ($old_value !== $new_value) {
                    if (!isset($this->foreign_key_target_mappings[$key_name])) {
                        $this->foreign_key_target_mappings[$key_name] = array();                    
                    }
                    $this->foreign_key_target_mappings[$key_name][$old_value] = $new_value;
                }
            }
        }
    }
    
    /**
     * Import the posts from the given import data.
     *
     * @param array $import The import data from which to import posts.
     * @return void
     */
    private function import_posts(array $import) {
        foreach ($this->descriptor->post_types as $post_type) {
            foreach ($import['data'][$post_type] as $post) {
                if (!empty($post['__custom_fields'])) {
                    $custom_fields = $post['__custom_fields'];
                    unset($post['__custom_fields']);

                    foreach ($custom_fields as $field => $value) {
                        if (is_array($value) && (count($value) == 1)) {
                            $custom_fields[$field] = $value[0];
                        }
                    }
                } else {
                    $custom_fields = false;
                }
                $this->import_post($post, $custom_fields);
            }
        }
    }

    /**
     * Run the import.
     *
     * @param $error_callback The callback that is used to display error 
     *          messages.
     * @return void
     */
    public function run($error_callback) {
        $this->error_callback = $error_callback;

        if ($this->descriptor === false) {
            $this->error('Unknown transfer descriptor');
            return;
        }

        if (!empty($this->file)) {
            if ($this->file['error'] > 0) {
                $this->error('Could not Upload file: ' . $this->file['error']);
            } else {
                $file_name = $this->file['name'];
                $file_size = $this->file['size'];
                if ((substr($file_name, -5) === ".json") && ($file_size < 500000000)) {
                    $import = json_decode(file_get_contents($this->file['tmp_name']), true);
                    if ($import['type'] !== $this->descriptor->name) {
                        $this->error('File does not contain data for chosen transfer type');
                    } else {
                        $this->import_posts($import);
                        $this->error(json_encode($this->descriptor));
                        $this->error(substr(json_encode($import, JSON_PRETTY_PRINT), 0, 500));
                    }
                } else {
                    $this->error('Invalid file or file size too big.');
                }
            }
        } else {
            $this->error('Transfer file not defined');
        }
    }
}
?>