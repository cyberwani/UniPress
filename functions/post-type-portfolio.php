<?php
/**
 * The Portfolio custom post type
 *
 * @package   UniPress
 * @author    João Araújo <unispheredesign@gmail.com>
 * @license   GPL-2.0+
 * @link      http://themeforest.net/user/unisphere
 * @copyright 2013 João Araújo
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the "portfolio_cpt" custom post type and "portolio_category" taxonomy
 *
 * @since 1.0.0
 */
function unipress_post_type_portfolio_register() {	

	$settings = UniPressSettings();

	register_post_type( 'portfolio_cpt', 
						array(
							'labels' => array(
								'name' => __( 'Portfolio', 'unipress' ),
								'singular_name' => __( 'Portfolio Item', 'unipress' ),
								'add_new' => __( 'Add New Item', 'unipress' ),
								'add_new_item' => __( 'Add New Portfolio Item', 'unipress' ),
								'edit_item' => __( 'Edit Portfolio Item', 'unipress' ),
								'new_item' => __( 'Add New Portfolio Item', 'unipress' ),
								'view_item' => __( 'View Item', 'unipress' ),
								'search_items' => __( 'Search Portfolio Items', 'unipress' ),
								'not_found' => __( 'No portfolio items found', 'unipress' ),
								'not_found_in_trash' => __( 'No portfolio items found in trash', 'unipress' ) ),
							'public' => true,
							'capability_type' => 'post',
							'has_archive' => true,
							'rewrite' => array( 'slug' => trim( isset( $settings['portfolio_permalink'] ) ? $settings['portfolio_permalink'] : 'portfolio/detail' ), 'with_front' => false ),
							'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions' )
						)
					);

	register_taxonomy(  'portfolio_category', 
						'portfolio_cpt', 
						array(
							'labels' => array(
								'name' => __( 'Portfolio Categories', 'unipress' ),
								'singular_name' => __( 'Portfolio Category', 'unipress' ),
								'search_items' => __( 'Search Portfolio Categories', 'unipress' ),
								'popular_items' => __( 'Popular Portfolio Categories', 'unipress' ),
								'all_items' => __( 'All Portfolio Categories', 'unipress' ),
								'parent_item' => __( 'Parent Portfolio Category', 'unipress' ),
								'parent_item_colon' => __( 'Parent Portfolio Category:', 'unipress' ),
								'edit_item' => __( 'Edit Portfolio Category', 'unipress' ),
								'update_item' => __( 'Update Portfolio Category', 'unipress' ),
								'add_new_item' => __( 'Add New Portfolio Category', 'unipress' ),
								'new_item_name' => __( 'New Portfolio Category Name', 'unipress' ),
								'separate_items_with_commas' => __( 'Separate portfolio categories with commas', 'unipress' ),
								'add_or_remove_items' => __( 'Add or remove portfolio categories', 'unipress' ),
								'choose_from_most_used' => __( 'Choose from the most used portfolio categories', 'unipress' ),
								'menu_name' => __( 'Portfolio Categories', 'unipress' ) ),
							'public' => true,
							'hierarchical' => true,
							'rewrite' => array( 'slug' => 'portfolio-category' )
						)
					);

	function unipress_portfolio_categories_permalink_structure( $post_link, $post, $leavename, $sample ) {
		if ( false !== strpos( $post_link, '%portfolio_category%' ) ) {
			$portfolio_category_type_term = get_the_terms( $post->ID, 'portfolio_category' );
			$post_link = str_replace( '%portfolio_category%', array_pop( $portfolio_category_type_term )->slug, $post_link );
		}
		return $post_link;
	}
	add_filter( 'post_type_link', 'unipress_portfolio_categories_permalink_structure', 10, 4 );


	// Portfolio permalink 404 error fix
	if( get_option('flush_rewrite_rules') == '1' ) {
		flush_rewrite_rules( false );
		update_option( 'flush_rewrite_rules', '0' );
	}


	function unipress_portfolio_cpt_edit_columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'portfolio_thumbnail' => __( 'Image', 'unipress' ),
			'title' => __( 'Title',			 'unipress' ),
			'portfolio_category' => __( 'Category', 'unipress' ),
			"author" => __( 'Author', 'unipress' ),
			"comments" => __( 'Comments', 'unipress' ),
			"date" => __( 'Date', 'unipress' )
		);
		$columns['comments'] = '<div class="vers"><img alt="' . __( 'Comments', 'unipress' ) . '" src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
		return $columns;
	}	
	add_filter( 'manage_edit-portfolio_cpt_columns', 'unipress_portfolio_cpt_edit_columns' );


	function unipress_portfolio_cpt_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'portfolio_thumbnail':
				$width = (int) 35;
				$height = (int) 35;
				// Display the featured image in the column view if possible
				if ( has_post_thumbnail()) {
					the_post_thumbnail( array( $width, $height ) );
				} else {
					echo __( 'None', 'unipress' );
				}
				break;					
			case "portfolio_category":  
				if ( $category_list = get_the_term_list( $post_id, 'portfolio_category', '', ', ', '' ) ) {
					echo $category_list;
				} else {
					echo __( 'None', 'unipress' );
				}
				break; 
		}
	}	
	add_action( 'manage_posts_custom_column', 'unipress_portfolio_cpt_custom_columns', 10, 2 );


	function unipress_portfolio_cpt_icons() { ?>
		<style type="text/css" media="screen">
			#menu-posts-portfolio_cpt .wp-menu-image {
				background: url('<?php echo trailingslashit( UNIPRESS_ADMIN_IMAGES ); ?>portfolio-icon.png') no-repeat 6px 6px!important;
			}
			#menu-posts-portfolio_cpt:hover .wp-menu-image, #menu-posts-portfolio_cpt.wp-has-current-submenu .wp-menu-image {
				background-position: 6px -16px!important;
			}
			#icon-edit.icon32-posts-portfolio_cpt { 
				background: url('<?php echo trailingslashit( UNIPRESS_ADMIN_IMAGES ); ?>/portfolio-32x32.png') no-repeat; 
			}
		</style>
	<?php }	
	add_action( 'admin_head', 'unipress_portfolio_cpt_icons' );


	function unipress_restrict_portfolio_by_portfolio_categories() {
		global $typenow;
		global $wp_query;
		if ( $typenow == 'portfolio_cpt' ) {
			$taxonomy = 'portfolio_category';
			$business_taxonomy = get_taxonomy( $taxonomy );
			wp_dropdown_categories( array(
				'show_option_all' => __( "Show All {$business_taxonomy->label}", 'unipress' ),
				'taxonomy'        => $taxonomy,
				'name'            => 'portfolio_category',
				'orderby'         => 'name',
				'selected'        => isset($wp_query->query['portfolio_category']) ? $wp_query->query['portfolio_category'] : false,
				'hierarchical'    => true,
				'depth'           => 3,
				'show_count'      => true,
				'hide_empty'      => true
			));
		}
	}
	add_action( 'restrict_manage_posts','unipress_restrict_portfolio_by_portfolio_categories' );	


	function unipress_convert_portfolio_category_id_to_taxonomy_term_in_query($query) {
		global $pagenow;
		$qv = &$query->query_vars;
		if ( 'edit.php' == $pagenow && isset( $qv['portfolio_category'] ) && is_numeric( $qv['portfolio_category'] ) && '0' != $qv['portfolio_category'] ) {
			$term = get_term_by( 'id', $qv['portfolio_category'], 'portfolio_category' );
			$qv['portfolio_category'] = $term->slug;
		}
	}
	add_filter( 'parse_query', 'unipress_convert_portfolio_category_id_to_taxonomy_term_in_query' );
}
add_action( 'init', 'unipress_post_type_portfolio_register' );