<?php
/**
 * Plugin Name: Crumbly
 * Plugin URI: https://github.com/pb-86/reddog-breadcrumbs
 * Description: Simple and lightweight plugin for theme developers that provides and easy-to-use function for displaying breadcrumbs.
 * Version: 2.1
 * Author: Reddog Systems
 * Author URI: https://reddog.systems
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: crumbly
 * Domain Path: /languages
 *
 * Crumbly is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Crumbly is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Crumbly.
 *
 * @package Crumbly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loading the translation files
 */
function crumbly_load_textdomain() {
	load_plugin_textdomain( 'crumbly', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crumbly_load_textdomain' );

/**
 * Position counter for the breadcrumb list.
 *
 * @var int
 */
$position = 1;

/**
 * Main function of the plugin.
 *
 * @return void
 */
function crumbly() {
	// If on the front page or posts index (home), do not display breadcrumbs.
	if ( is_front_page() || is_home() ) {
		return;
	}

	echo '<nav class="crumbly" aria-label="breadcrumb">';
	echo '<ol class="crumbly__list" itemscope itemtype="http://schema.org/BreadcrumbList">';

	// Print link to the home page.
	crumbly_home();

	$conditions = array(
		fn() => is_attachment() ? crumbly_attachment() : false,
		fn() => is_singular() ? crumbly_singular() : false,
		fn() => is_tax() ? crumbly_taxonomy() : false,
		fn() => is_archive() ? crumbly_archive() : false,
		fn() => is_search() ? crumbly_search() : false,
		fn() => is_404() ? crumbly_404() : false,
	);

	foreach ( $conditions as $callback ) {
		if ( $callback() ) {
			break;
		}
	}

	echo '</ol>';
	echo '</nav>';
}

/**
 * Backward compatible wrapper for crumbly().
 *
 * Keeps the legacy rddgbc() function name for themes/plugins that depended on
 * it. Simply calls crumbly() to output the breadcrumb trail.
 *
 * @return void
 * @see crumbly()
 */
function rddgbc() {
	crumbly();
}

/**
 * Prints link to the home page.
 *
 * @return void
 */
function crumbly_home() {
	$url   = esc_url( home_url( '/' ) );
	$title = esc_html__( 'Home page', 'crumbly' );
	crumbly_print( $url, $title );
}

/**
 * Prints crumb with title of 404 error page.
 *
 * @return void
 */
function crumbly_404() {
	$url   = get_permalink();
	$title = esc_html__( 'Error 404 - Page not found', 'crumbly' );
	crumbly_print( $url, $title, 'last' );
}

/**
 * Prints crumb with title of search page
 *
 * @return void
 */
function crumbly_search() {
	$url   = get_search_link();
	$title = esc_html__( 'Search result for: ', 'crumbly' ) . get_search_query();
	crumbly_print( $url, $title, 'last' );
}

/**
 * Prints current category and its ancestors.
 *
 * @return void
 */
function crumbly_archive() {
	if ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$archive_title = post_type_archive_title( '', false );
			$archive_link  = get_post_type_archive_link( get_post_type( get_the_ID() ) );
			crumbly_print( $archive_link, $archive_title, 'last' );
		} elseif ( ! is_category() ) {
			crumbly_taxonomy( 'last' );
		}
	}

	if ( is_category() ) {
		$current_category_id = get_query_var( 'cat' );
		$category_ancestors  = array_reverse( get_ancestors( $current_category_id, 'category' ) );
		if ( $category_ancestors ) {
			foreach ( $category_ancestors as $ancestor_id ) {
				$ancestor_url   = get_category_link( $ancestor_id );
				$ancestor_title = get_cat_name( $ancestor_id );
				crumbly_print( $ancestor_url, $ancestor_title );
			}
		}
		$current_category_url   = get_category_link( $current_category_id );
		$current_category_title = get_cat_name( $current_category_id );
		crumbly_print( $current_category_url, $current_category_title, 'last' );
	}
}

/**
 * Prints page ancestors or post category hierarchy depending on the singular
 * type and the title of the current post or page.
 *
 * @return void
 */
function crumbly_singular() {
	if ( is_page() ) {
		crumbly_page_ancestors();
	}

	if ( is_single() ) {
		if ( get_post_type( get_the_ID() ) === 'post' ) {
			crumbly_category();
		}

		// CPT posts.
		if ( get_post_type( get_the_ID() ) !== 'post' ) {
			if ( get_post_type_object( get_post_type( get_the_ID() ) )->capability_type === 'post' ) {
				crumbly_taxonomy();
			}

			if ( get_post_type_object( get_post_type( get_the_ID() ) )->capability_type === 'page' ) {
				crumbly_page_ancestors();
			}
		}
	}

	$url   = get_permalink();
	$title = get_the_title();
	crumbly_print( $url, $title, 'last' );
}

/**
 * Prints breadcrumbs for attachment page
 *
 * @return void
 */
function crumbly_attachment() {
	crumbly_page_ancestors();
	$url   = get_permalink();
	$title = get_the_title();
	crumbly_print( $url, $title, 'last' );
}

/**
 * Prints all the ancestors for the current page.
 *
 * @return void
 */
function crumbly_page_ancestors() {
	$ancestors = array_reverse( get_ancestors( get_the_ID(), 'page' ) );
	if ( $ancestors ) {
		foreach ( $ancestors as $id ) {
			$url   = get_page_link( $id );
			$title = get_the_title( $id );
			crumbly_print( $url, $title );
		}
	}
}

/**
 * Prints main category and its ancestors for the current post.
 *
 * @return void
 */
function crumbly_category() {
	$categories = wp_get_post_categories( get_the_ID() );
	if ( ! empty( $categories ) ) {
		$category_id        = $categories[0];
		$category_ancestors = get_ancestors( $category_id, 'category' );
		if ( $category_ancestors ) {
			foreach ( $category_ancestors as $ancestor_id ) {
				$ancestor_url   = get_category_link( $ancestor_id );
				$ancestor_title = get_cat_name( $ancestor_id );
				crumbly_print( $ancestor_url, $ancestor_title );
			}
		}
		$category_url   = get_category_link( $category_id );
		$category_title = get_cat_name( $category_id );
		crumbly_print( $category_url, $category_title );
	}
}

/**
 * Prints the custom post type archive and the current post's term (if it exists).
 *
 * @param string $order Flag indicating whether this item is the last element in the trail.
 * @return void
 */
function crumbly_taxonomy( string $order = '' ) {
	$cpt       = ( get_post_type( get_the_ID() ) );
	$cpt_label = get_post_type_object( $cpt )->label;
	$cpt_link  = get_post_type_archive_link( $cpt );
	if ( empty( $cpt_link ) ) {
		return;
	}
	crumbly_print( $cpt_link, $cpt_label );

	$post_taxonomies = get_post_taxonomies( get_the_ID() );
	$post_terms      = get_the_terms( get_the_ID(), $post_taxonomies[0] );
	if ( ! empty( $post_terms ) ) {
		$term_link = get_term_link( $post_terms[0]->term_id, $post_taxonomies[0] );
		$term_name = $post_terms[0]->name;
		crumbly_print( $term_link, $term_name, $order );
	}
}

/**
 * Gets the current position value, returns a formatted HTML string (meta
 * position) and increments the position counter.
 *
 * @return integer $position_html HTML meta element with the item's position in the trail.
 */
function crumbly_position() {
	$position_counter = $GLOBALS['position'];
	$position_html    = "<meta itemprop=\"position\" content=\"{$position_counter}\">";
	++$GLOBALS['position'];
	return $position_html;
}

/**
 * Echoes current list item according to the following pattern.
 *
 * @param string $url URL of the current item.
 * @param string $title Title for current item.
 * @param string $order When set to 'last', marks the item as the active/last element in the breadcrumb trail.
 * @return void
 */
function crumbly_print( string $url, string $title, string $order = '' ) {
	$li_opened   = '<li class="crumbly__item" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
	$li_last     = '<li class="crumbly__item crumbly__item--active" aria-current="page" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
	$a_opened    = "<a class=\"crumbly__link\" href=\"{$url}\" itemprop=\"item\" itemtype=\"http://schema.org/Thing\">";
	$span_opened = '<span itemprop="name">';
	$span_closed = '</span>';
	$separator   = '<span class="crumbly__separator">/</span>';
	$a_closed    = '</a>';
	$position    = crumbly_position();
	$li_closed   = '</li>';

	if ( 'last' === $order ) {
		echo wp_kses_post( "{$li_last}{$a_opened}{$span_opened}{$title}{$span_closed}{$a_closed}{$position}{$li_closed}" );
	} else {
		echo wp_kses_post( "{$li_opened}{$a_opened}{$span_opened}{$title}{$span_closed}{$a_closed}{$separator}{$position}{$li_closed}" );
	}
}
