<?php
/**
 * WP Book Plugin setup
 *
 * @package wp-book
 * @since   1.0.0
 */
defined('ABSPATH') || exit;
/**
 * Main WPBook Plugin Class.
 *
 * @class WPBook_Custom_post_type
 */
class WPBook_Custom_post_type
{

    // Post type Name
    public $post_type_name;

    /**
     * Constructor.
     */
    public function __construct($name)
    {
        $this->post_type_name = strtolower(str_replace(' ', '_', $name));

        if (!post_type_exists($this->post_type_name)) {
            add_action('init', array($this, 'register_post_type'));
        }

        $this->save();

        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }


    /**
     * @return mixed
     * Function for add scripts
     */
    public function admin_scripts()
    {
        global $pagenow;

        if ($pagenow == "post-new.php" && $_GET["post_type"] == "book") {
        }
    }

    /**
     * Create post type
     */
    public function register_post_type()
    {
        //Capitilize the words and make it plural 
        $name = ucwords(str_replace('_', ' ', $this->post_type_name));
        $plural = $name . 's';

        // We set the default labels based on the post type name and plural. We overwrite them with the given labels. 
        $labels = array_merge(

            // Default 
            array(
                'name' => _x($plural, 'post type general name'),
                'singular_name' => _x($name, 'post type singular name'),
                'add_new' => _x('Add New', strtolower($name)),
                'add_new_item' => __('Add New ' . $name),
                'edit_item' => __('Edit ' . $name),
                'new_item' => __('New ' . $name),
                'all_items' => __('All ' . $plural),
                'view_item' => __('View ' . $name),
                'search_items' => __('Search ' . $plural),
                'not_found' => __('No ' . strtolower($plural) . ' found'),
                'not_found_in_trash' => __('No ' . strtolower($plural) . ' found in Trash'),
                'parent_item_colon' => '',
                'menu_name' => $plural
            )
        );

        // Same principle as the labels. We set some defaults and overwrite them with the given arguments. 
        $args = array_merge(

            // Default 
            array(
                'label' => $plural,
                'labels' => $labels,
                'public' => true,
                'show_ui' => true,
                'supports' => array('title', 'editor'),
                'show_in_nav_menus' => true,
                '_builtin' => false,
            )
        );

        // Register the post type 
        register_post_type($this->post_type_name, $args);
    }

    /**
     * Register taxonomy
     */
    public function add_taxonomy($name, $labels = array(), $args = array())
    {
        if (!empty($name)) {
            // We need to know the post type name, so the new taxonomy can be attached to it. 
            $post_type_name = $this->post_type_name;
            // Taxonomy properties 
            $taxonomy_name = strtolower(str_replace(' ', '_', $name));
            $taxonomy_labels = $labels;
            $taxonomy_args = $args;

            if (!taxonomy_exists($taxonomy_name)) {
                $name = ucwords(str_replace('_', ' ', $name));
                $plural = $name . 's';

                // Default labels, overwrite them with the given labels. 
                $labels = array_merge(

                    // Default 
                    array(
                        'name' => _x($plural, 'taxonomy general name'),
                        'singular_name' => _x($name, 'taxonomy singular name'),
                        'search_items' => __('Search ' . $plural),
                        'all_items' => __('All ' . $plural),
                        'parent_item' => __('Parent ' . $name),
                        'parent_item_colon' => __('Parent ' . $name . ':'),
                        'edit_item' => __('Edit ' . $name),
                        'update_item' => __('Update ' . $name),
                        'add_new_item' => __('Add New ' . $name),
                        'new_item_name' => __('New ' . $name . ' Name'),
                        'menu_name' => __($name),
                    ),
                    // Given labels 
                    $taxonomy_labels
                );
                // Default arguments, overwritten with the given arguments 
                $args = array_merge(
                    // Default 
                    array(
                        'label' => $plural,
                        'labels' => $labels,
                        'public' => true,
                        'show_ui' => true,
                        'show_in_nav_menus' => true,
                        '_builtin' => false,
                    ),
                    // Given 
                    $taxonomy_args
                );

                // Add the taxonomy to the post type 
                add_action(
                    'init',
                    function () use ($taxonomy_name, $post_type_name, $args) {
                        register_taxonomy($taxonomy_name, $post_type_name, $args);
                    }
                );
            } else {
                add_action(
                    'init',
                    function () use ($taxonomy_name, $post_type_name) {
                        register_taxonomy_for_object_type($taxonomy_name, $post_type_name);
                    }
                );
            }
        }
    }

    /**
     * Add Post Meta
     */
    public function add_meta_box($title, $fields = array(), $context = 'normal', $priority = 'default')
    {
        if (!empty($title)) {
            // We need to know the Post Type name again 
            $post_type_name = $this->post_type_name;
            // Meta variables 
            $box_id = strtolower(str_replace(' ', '_', $title));
            $box_title = ucwords(str_replace('_', ' ', $title));
            $box_context = $context;
            $box_priority = $priority;

            // Make the fields global 
            global $custom_fields;
            $custom_fields[$title] = $fields;

            add_action(
                'admin_init',
                function () use ($box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields) {
                    add_meta_box(
                        $box_id,
                        $box_title,
                        function ($post, $data) {
                            global $post;

                            // Nonce field for some validation 
                            wp_nonce_field(plugin_basename(__FILE__), $this->post_type_name);

                            // Get all inputs from $data 
                            $custom_fields = $data['args'][0];

                            // Get the saved values 
                            $meta = get_post_custom($post->ID);

                            // Check the array and loop through it 
                            if (!empty($custom_fields)) {
                                /* Loop through $custom_fields */

                                foreach ($custom_fields as $label => $type) {
                                    $field_id_name = strtolower(str_replace(' ', '_', $data['id'])) . '_' . strtolower(str_replace(' ', '_', $label));

                                    echo '<p class="meta-options"><label for="' . $field_id_name . '">' . $label . '
                                    <input type="' . $type . '" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $meta[$field_id_name][0] . '" />
                                    </label>
                                    </p>';
                                }
                            }

                        },
                        $post_type_name,
                        $box_context,
                        $box_priority,
                        array($fields)
                    );
                }
            );
        }

    }

    /**
     * Save Post Meta
     */
    public function save()
    {
        // Need the post type name again 
        $post_type_name = $this->post_type_name;

        add_action(
            'save_post',
            function () use ($post_type_name) {
                // Deny the WordPress autosave function 
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                    return;
                if (!wp_verify_nonce($_POST[$post_type_name], plugin_basename(__FILE__)))
                    return;

                global $post;

                if (isset($_POST) && isset($post->ID) && get_post_type($post->ID) == $post_type_name) {
                    global $custom_fields;

                    // Loop through each meta box 
                    foreach ($custom_fields as $title => $fields) {
                        // Loop through all fields 
                        foreach ($fields as $label => $type) {
                            $field_id_name = strtolower(str_replace(' ', '_', $title)) . '_' . strtolower(str_replace(' ', '_', $label));

                            update_post_meta($post->ID, $field_id_name, $_POST['custom_meta'][$field_id_name]);

                            $this->save_book_meta($post->ID, strtolower(str_replace(' ', '_', $label)), $_POST['custom_meta'][$field_id_name]);
                        }

                    }
                }
            }
        );
    }

    /**
     * @args book id
     * @return array
     * Function to return array of book meta
     */
    public function book_meta_exists($id) {
        global $wpdb;
        $res = $wpdb->get_col($wpdb->prepare('SELECT id from ' . $wpdb->prefix . 'book_meta WHERE book_id = %d limit %d', $id, 1));
        return $res;
    }

    /**
     * @args book id, field name, field value
     * @return 
     * Function to insert or update the metatable
     */
    public function save_book_meta($id, $key, $value)
    {
        global $wpdb;
        $res = $this->book_meta_exists($id);
        if (!empty($res)) {
            $wpdb->update(
                $wpdb->prefix . 'book_meta',
                array(
                    'book_id' => $id,
                    $key => $value,
                ),
                array('book_id' => $id)
            );
        } else {
            $wpdb->insert(
                $wpdb->prefix . 'book_meta',
                array(
                    'book_id' => $id,
                    $key => $value,
                )
            );
        }
    }
}

$CP = new WPBook_Custom_post_type("Book");
$CP->add_taxonomy('Book Category', array(), array('hierarchical' => true));
$CP->add_taxonomy('Book Tag', array(), array());
$CP->add_meta_box(
    'Book Info',
    array(
        'Author Name' => 'text',
        'Price' => 'number',
        'Publisher' => 'text',
        'Year' => 'text',
        'Edition' => 'text',
        'URL' => 'url',
    )
);