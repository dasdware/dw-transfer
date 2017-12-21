<?php
define ('BATCH_COUNT', 20);

/**
 * Class defining the export portion of a transfer.
 * 
 * @link       http://www.dasdware.de
 * @since      1.0.0
 * 
 * @package    DW_WP_Transfer
 * @subpackage DW_WP_Transfer/admin
 */
class DW_Transfer_Export {

    /**
     * The descriptor that is used for this export.
     * 
     * @var DW_WP_Transfer_Descriptor $descriptor
     */
    private $descriptor;

    /**
     * Create a new export.
     * 
     * @param DW_WP_Transfer_Descriptor $descriptor The descriptor used for
     *          this export.
     */
    public function __construct(DW_WP_Transfer_Descriptor $descriptor) {
        $this->descriptor = $descriptor;
    }

    /**
     * Get the posts of the given post type. These will be retrieved from
     * the wordpress database.
     * 
     * @return array List of wordpress posts.
     */
    private function get_posts_of_type($type) {
        $posts = array();
        $batch = 0;
    
        do {
            $batch_posts = get_posts(
                array('post_type' => $type,
                      'posts_per_page' => BATCH_COUNT,
                      'offset' => $batch * BATCH_COUNT));
            foreach ($batch_posts as $batch_post) {
                $batch_post->__custom_fields = get_post_meta($batch_post->ID);
                $posts[] = $batch_post;
            }
            $batch++;
        } while (!empty($batch_posts));
        
        return $posts;
    }

    /**
     * Run the export.
     * 
     * @param $error_callback Callback function used for reporting errors.
     */
    public function run($error_callback) {
        if ($this->descriptor === false) {
            $error_callback('Unknown transfer descriptor');
        } else {
            // gültiger Deskriptor
            $export = new stdClass;
            $export->type = $this->descriptor->name;
            $export->timestamp = date('Y-m-d H:i:S');
        
            $export->data = new stdClass;
        
            foreach ($this->descriptor->post_types as $post_type) {
                $export->data->{$post_type} = $this->get_posts_of_type($post_type);
            }
        
            $blogname = str_replace(" ", "", get_option('blogname'));
            $date = date("Y-m-d");
            $filename = $blogname . "-" . $this->descriptor->name . '-' . $date . '.json';
            
            $json = json_encode($export, JSON_PRETTY_PRINT);
            
            ob_clean();
            echo $json;
            header("Content-Type: text/json; charset=" . get_option( 'blog_charset'));
            header("Content-Disposition: attachment; filename=$filename");
            exit();
        }
    }
}
?>