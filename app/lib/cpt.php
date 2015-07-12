<?php
  
// Register Custom Post Type
function recast_podcast_type() {

	$labels = array(
		'name'                => _x( 'Podcasts', 'Post Type General Name', 'recast' ),
		'singular_name'       => _x( 'Podcast', 'Post Type Singular Name', 'recast' ),
		'menu_name'           => __( 'Podcasts', 'recast' ),
		'name_admin_bar'      => __( 'Podcasts', 'recast' ),
		'parent_item_colon'   => __( 'Parent Podcast:', 'recast' ),
		'all_items'           => __( 'All Podcasts', 'recast' ),
		'add_new_item'        => __( 'Add New Podcast', 'recast' ),
		'add_new'             => __( 'Add New', 'recast' ),
		'new_item'            => __( 'New Podcast', 'recast' ),
		'edit_item'           => __( 'Edit Podcast', 'recast' ),
		'update_item'         => __( 'Update Podcast', 'recast' ),
		'view_item'           => __( 'View Podcast', 'recast' ),
		'search_items'        => __( 'Search Podcasts', 'recast' ),
		'not_found'           => __( 'Not found', 'recast' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'recast' ),
	);
	$args = array(
		'label'               => __( 'podcast', 'recast' ),
		'labels'              => $labels,
		'supports'            => false,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-groups',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'podcast', $args );

}

// Hook into the 'init' action
add_action( 'init', 'recast_podcast_type', 0 );

// Register Custom Post Type
function recast_episode_cpt() {

	$labels = array(
		'name'                => _x( 'Episode', 'Post Type General Name', 'recast' ),
		'singular_name'       => _x( 'Episode', 'Post Type Singular Name', 'recast' ),
		'menu_name'           => __( 'Episodes', 'recast' ),
		'name_admin_bar'      => __( 'Episodes', 'recast' ),
		'parent_item_colon'   => __( 'Parent Episode:', 'recast' ),
		'all_items'           => __( 'All Episodes', 'recast' ),
		'add_new_item'        => __( 'Add New Episode', 'recast' ),
		'add_new'             => __( 'Add New', 'recast' ),
		'new_item'            => __( 'New Episode', 'recast' ),
		'edit_item'           => __( 'Edit Episode', 'recast' ),
		'update_item'         => __( 'Update Episode', 'recast' ),
		'view_item'           => __( 'View Episode', 'recast' ),
		'search_items'        => __( 'Search Episode', 'recast' ),
		'not_found'           => __( 'Not found', 'recast' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'recast' ),
	);
	$args = array(
		'label'               => __( 'episode', 'recast' ),
		'description'         => __( 'Podcast episode', 'recast' ),
		'labels'              => $labels,
		'supports'            => false,
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => false,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-chat',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,		
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'episode', $args );

}

// Hook into the 'init' action
add_action( 'init', 'recast_episode_cpt', 0 );