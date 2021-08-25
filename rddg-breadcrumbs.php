<?php
/**
 * Plugin Name: RDDG Breadcrumbs
 * Plugin URI: https://pb-86.github.io/RDDG-breadcrumbs/
 * Description: Simple and lightweight plugin for theme developers that provide easy to use function for displaying breadcrumbs.
 * Version: 1.5.5
 * Author: Przemek Bąchorek
 * Author URI: https://reddog.systems
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rddgbc
 * Domain Path: /languages
 *
 * RDDG Breadcrumbs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * RDDG Breadcrumbs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with RDDG Breadcrumbs.
 *
 * @package RDDG Breadcrumbs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loading the translation files
 */
function rddgbc_load_textdomain() {
	load_plugin_textdomain( 'rddgbc', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'rddgbc_load_textdomain' );

/**
 * Variable in which the position of the crumble is stored
 *
 * @var integer
 */
$position = 1;

/**
 * This is main method of the plugin
 *
 * @return void|null
 */
function rddgbc() {
	$container_opened = '<nav class="rddgbc" aria-label="breadcrumb">';
	$container_closed = '</nav>';
	$list_opened      = '<ol class="rddgbc__list" itemscope itemtype="http://schema.org/BreadcrumbList">';
	$list_closed      = '</ol>';
	if ( ! is_front_page() ) {
		echo $container_opened;
		echo $list_opened;
		rddgbc_the_home();
		if ( is_singular() && ! is_attachment() ) {
			rddgbc_the_singular();
		} elseif ( is_archive() ) {
			rddgbc_the_archive();
		} elseif ( is_tax() ) {
			rddgbc_the_taxonomies();
		} elseif ( is_search() ) {
			rddgbc_the_search();
		} elseif ( is_attachment() ) {
			rddgbc_the_attachment();
		} elseif ( is_404() ) {
			rddgbc_the_404();
		}
		echo $list_closed;
		echo $container_closed;
	}
}

/**
 * This method prints link to the home page.
 *
 * @return void|null
 */
function rddgbc_the_home() {
	if ( 'page' === get_option( 'show_on_front' ) && is_single() ) {
		$url   = esc_url( get_permalink( get_option( 'page_for_posts' ) ) );
		$title = esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
	} else {
		$url   = esc_url( home_url( '/' ) );
		$title = esc_html__( 'Home page', 'rddgbc' );
	}
	rddgbc_print( $url, $title );
}

/**
 * This method prints crumb with title of 404 error page.
 *
 * @return void|null
 */
function rddgbc_the_404() {
	$url   = get_permalink();
	$title = esc_html__( 'Error 404 - Page not found', 'rddgbc' );
	rddgbc_print( $url, $title, true );
}

/**
 * This method prints crumb with title of search page
 *
 * @return void|null
 */
function rddgbc_the_search() {
	$url   = get_search_link();
	$title = esc_html__( 'Search result for: ', 'rddgbc' ) . get_search_query();
	rddgbc_print( $url, $title, true );
}

/**
 * This method prints current category and its ancestors.
 *
 * @return void|null
 */
function rddgbc_the_archive() {
	if ( is_archive() ) {
		if ( is_post_type_archive() ) {
			$archive_title = post_type_archive_title( '', false );
			$archive_link  = get_post_type_archive_link( get_post_type( get_the_ID() ) );
			rddgbc_print( $archive_link, $archive_title, true );
		} else {
			rddgbc_the_taxonomies( true );
		}
	}

	if ( is_category() ) {
		$current_category_id = get_query_var( 'cat' );
		$category_ancestors  = array_reverse( get_ancestors( $current_category_id, 'category' ) );
		if ( $category_ancestors ) {
			foreach ( $category_ancestors as $ancestor_id ) {
				$ancestor_url   = get_category_link( $ancestor_id );
				$ancestor_title = get_cat_name( $ancestor_id );
				rddgbc_print( $ancestor_url, $ancestor_title );
			}
		}
		$current_category_url   = get_category_link( $current_category_id );
		$current_category_title = get_cat_name( $current_category_id );
		rddgbc_print( $current_category_url, $current_category_title, true );
	}
}

/**
 * This method prints page ancestors or post category hierarchy depending of
 * type of the singular and the title of current post or page.
 *
 * @return void|null
 */
function rddgbc_the_singular() {
	if ( is_page() ) {
		rddgbc_the_page_ancestors();
	} elseif ( is_single() ) {
		if ( get_post_type( get_the_ID() ) === 'post' ) {
			rddgbc_the_categories();
		} else {
			if ( get_post_type_object( get_post_type( get_the_ID() ) )->capability_type === 'post' ) {
				rddgbc_the_taxonomies();
			} elseif ( get_post_type_object( get_post_type( get_the_ID() ) )->capability_type === 'page' ) {
				rddgbc_the_page_ancestors();
			}
		}
	}
	$url   = get_permalink();
	$title = get_the_title();
	rddgbc_print( $url, $title, true );
}

/**
 * Prints breadcrumbs for attachment page
 *
 * @return void|null
 */
function rddgbc_the_attachment() {
	rddgbc_the_page_ancestors();
	$url   = get_permalink();
	$title = get_the_title();
	rddgbc_print( $url, $title, true );
}

/**
 * This method prints all the ancestors for the current page.
 *
 * @return void|null
 */
function rddgbc_the_page_ancestors() {
	$ancestors = array_reverse( get_ancestors( get_the_ID(), 'page' ) );
	if ( $ancestors ) {
		foreach ( $ancestors as $id ) {
			$url   = get_page_link( $id );
			$title = get_the_title( $id );
			rddgbc_print( $url, $title );
		}
	}
}

/**
 * This method prints main category and its ancestors for the current post.
 *
 * @return void|null
 */
function rddgbc_the_categories() {
	$categories         = wp_get_post_categories( get_the_ID() );
	$category_id        = $categories[0];
	$category_ancestors = get_ancestors( $category_id, 'category' );
	if ( $category_ancestors ) {
		foreach ( $category_ancestors as $ancestor_id ) {
			$ancestor_url   = get_category_link( $ancestor_id );
			$ancestor_title = get_cat_name( $ancestor_id );
			rddgbc_print( $ancestor_url, $ancestor_title );
		}
	}
	$category_url   = get_category_link( $category_id );
	$category_title = get_cat_name( $category_id );
	rddgbc_print( $category_url, $category_title );
}

/**
 * Prints crumbs for CPT first and current post Terms seconds (if exists);
 *
 * @param bool $is_last Is this last elemnt of trail.
 * @return void|null
 */
function rddgbc_the_taxonomies( $is_last = false ) {
	$cpt       = ( get_post_type( get_the_ID() ) );
	$cpt_label = get_post_type_object( $cpt )->label;
	$cpt_link  = get_post_type_archive_link( $cpt );
	rddgbc_print( $cpt_link, $cpt_label );

	$post_taxonomies = get_post_taxonomies( get_the_ID() );
	$post_terms      = get_the_terms( get_the_ID(), $post_taxonomies[0] );
	if ( ! empty( $post_terms ) ) {
		$term_link = get_term_link( $post_terms[0]->term_id, $post_taxonomies[0] );
		$term_name = $post_terms[0]->name;
		rddgbc_print( $term_link, $term_name, $is_last );
	}
}

/**
 * This method gets current position value, returns formated string and
 * increments position value.
 *
 * @return integer $position_html Position in trail.
 */
function rddgbc_get_position() {
	$position_counter = $GLOBALS['position'];
	$position_html    = "<meta itemprop=\"position\" content=\"{$position_counter}\">";
	$GLOBALS['position']++;
	return $position_html;
}

/**
 * This method echoes current list item according to the following pattern.
 *
 * @param  string  $url URL of the current item.
 * @param  string  $title Title for current item.
 * @param  boolean $is_last Flag for last item.
 * @return void|null
 */
function rddgbc_print( $url, $title, $is_last = false ) {
	$li_opened   = '<li class="rddgbc__item" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
	$li_last     = '<li class="rddgbc__item rddgbc__item--active" aria-current="page" itemscope itemprop="itemListElement" itemtype="http://schema.org/ListItem">';
	$a_opened    = "<a class=\"rddgbc__link\" href=\"{$url}\" itemprop=\"item\" itemtype=\"http://schema.org/Thing\">";
	$span_opened = '<span itemprop="name">';
	$span_closed = '</span>';
	$separator   = '<span class="rddgbc__separator">&raquo;</span>';
	$a_closed    = '</a>';
	$position    = rddgbc_get_position();
	$li_closed   = '</li>';
	if ( true === $is_last ) {
		echo "{$li_last}{$a_opened}{$span_opened}{$title}{$span_closed}{$a_closed}{$position}{$li_closed}";
	} else {
		echo "{$li_opened}{$a_opened}{$span_opened}{$title}{$span_closed}{$a_closed}{$separator}{$position}{$li_closed}";
	}
}
