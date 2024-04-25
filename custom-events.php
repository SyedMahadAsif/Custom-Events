<?php
/*
Plugin Name: Custom Events
Description: Adds a custom post type for Events.
Version: 1.0
Author: Syed Mahad
*/

// Register the custom post type
function custom_events_post_type() {
    $labels = array(
        'name'               => _x( 'Events', 'post type general name', 'textdomain' ),
        'singular_name'      => _x( 'Event', 'post type singular name', 'textdomain' ),
        'menu_name'          => _x( 'Events', 'admin menu', 'textdomain' ),
        'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'textdomain' ),
        'add_new'            => _x( 'Add New', 'event', 'textdomain' ),
        'add_new_item'       => __( 'Add New Event', 'textdomain' ),
        'new_item'           => __( 'New Event', 'textdomain' ),
        'edit_item'          => __( 'Edit Event', 'textdomain' ),
        'view_item'          => __( 'View Event', 'textdomain' ),
        'all_items'          => __( 'All Events', 'textdomain' ),
        'search_items'       => __( 'Search Events', 'textdomain' ),
        'parent_item_colon'  => __( 'Parent Events:', 'textdomain' ),
        'not_found'          => __( 'No events found.', 'textdomain' ),
        'not_found_in_trash' => __( 'No events found in Trash.', 'textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'textdomain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'event' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
        'taxonomies'         => array( 'category', 'post_tag' )
    );

    register_post_type( 'event', $args );
}
add_action( 'init', 'custom_events_post_type' );

// Add custom meta boxes for event details
function custom_events_meta_boxes() {
    add_meta_box(
        'event_details',
        __( 'Event Details', 'textdomain' ),
        'custom_events_meta_box_callback',
        'event',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'custom_events_meta_boxes' );

// Meta box callback function
function custom_events_meta_box_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'custom_events_save_meta_box_data', 'custom_events_meta_box_nonce' );

    // Get saved meta values
    $event_date = get_post_meta( $post->ID, '_event_date', true );
    $location = get_post_meta( $post->ID, '_location', true );

    // Output fields
    ?>
    <p>
        <label for="event_date"><?php _e( 'Event Date:', 'textdomain' ); ?></label>
        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $event_date ); ?>">
    </p>
    <p>
        <label for="location"><?php _e( 'Location:', 'textdomain' ); ?></label>
        <input type="text" id="location" name="location" value="<?php echo esc_attr( $location ); ?>">
    </p>
    <?php
}

// Save custom meta box data
function custom_events_save_meta_box_data( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['custom_events_meta_box_nonce'] ) ) {
        return;
    }

    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['custom_events_meta_box_nonce'], 'custom_events_save_meta_box_data' ) ) {
        return;
    }

    // Check if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check if user has permissions to save data
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save meta data
    if ( isset( $_POST['event_date'] ) ) {
        update_post_meta( $post_id, '_event_date', sanitize_text_field( $_POST['event_date'] ) );
    }

    if ( isset( $_POST['location'] ) ) {
        update_post_meta( $post_id, '_location', sanitize_text_field( $_POST['location'] ) );
    }
}
add_action( 'save_post', 'custom_events_save_meta_box_data' );
