<?php
/*
Plugin Name: RDDG Breadcrumbs
Plugin URI: https://rddg.pl/pluginy/rddg-breadcrumbs
Description: Simple and lightweight plugin for theme developers that provide easy to use function for displaying breadcrumbs.
Version: 0.1
Author: Przemek Bąchorek
Author URI: https://reddog.systems
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  rddgbc
Domain Path:  /languages

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {URI to Plugin License}.
*/

/**
 * This method returns an array with tag templates
 * @return array
 */
function rddgbc_get_options() {
	$config = array(
		'opening_tag'		=> '<nav class="rddgbc" aria-label="breadcrumb"><ol class="rddgbc__list">',
		'closing_tag'		=> '</ol></nav>',
		'list_opening'	=> '<li class="rddgbc__list">',
		'list_current'	=> '<li class="rddgbc__item rddgbc__active" aria-current="page">',
		'list_closing'	=> '</li>'
	);
	return $config;
}

/**
 * This is main method of the plugin
 * @return void|null
 */
function rddgbc() {
	extract( rddgbc_get_options() );

	if( !is_front_page() ) {
		echo $opening_tag;
		rddgbc_the_home();

		if( is_singular() )
			rddgbc_the_singular();
		elseif( is_archive() )
			rddgbc_the_archive();
		elseif( is_search() )
			rddgbc_the_search();
		elseif( is_404() )
			rddgbc_the_404();

		echo $closing_tag;
	}
}

/**
 * This method prints link to the home page.
 * @return void
 */
function rddgbc_the_home() {
	extract( rddgbc_get_options() );
	$url		= esc_url( home_url( '/' ) );
	$title	= esc_html__( 'Strona główna', 'rddgbc' );
	$html		= "{$list_opening}<a href=\"{$url}\">{$title}</a>{$list_closing}";
	echo $html;
}

/**
 * This method prints crumb with title of 404 error page.
 * @return void
 */
function rddgbc_the_404() {
	extract( rddgbc_get_options() );
	$title	= esc_html__( 'Błąd 404', 'rddgbc' );
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

/**
 * This method prints crumb with title of search page
 * @return void
 */
function rddgbc_the_search() {
	extract( rddgbc_get_options() );
	$title	= esc_html__( 'Wyniki wyszukiwania: ' . get_search_query(), 'rddgbc' );
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

/**
 * This method prints current category and its ancestors.
 * @return void
 */
function rddgbc_the_archive() {
	extract( rddgbc_get_options() );
	$current_category_id= get_query_var('cat');

	$category_ancestors = array_reverse( get_ancestors( $current_category_id, 'category' ) );
	if( $category_ancestors ) {
		foreach( $category_ancestors as $category_ancestor_id ) {
			$category_ancestor_url = get_category_link( $category_ancestor_id );
			$category_ancestor_title = get_cat_name( $category_ancestor_id );
			$category_ancestor_html = "{$list_opening}<a href=\"{$category_ancestor_url}\">{$category_ancestor_title}</a>{$list_closing}";
			echo $category_ancestor_html;
		}
	}

	$current_category_title	= get_cat_name( $current_category_id );
	$current_category_html 	= "{$list_current}{$current_category_title}{$list_closing}";
	echo $current_category_html;
}

/**
 * This method prints page ancestors or post category hierarchy depending of
 * type of the singular and the title of current post or page.
 * @return void
 */
function rddgbc_the_singular() {
	extract( rddgbc_get_options() );

	if( is_page() )
		rddgbc_the_page_ancestors();
	elseif ( is_single() )
		rddgbc_the_categories();

	$title	= get_the_title();
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

/**
 * This method prints all the ancestors for the current page.
 * @return void
 */
function rddgbc_the_page_ancestors() {
	extract( rddgbc_get_options() );
	$ancestors = array_reverse( get_ancestors( get_the_ID(), 'page' ) );

	if( $ancestors ) {
		foreach( $ancestors as $ancestor_id ) {
			$ancestor_url		= get_page_link( $ancestor_id );
			$ancestor_title	= get_the_title( $ancestor_id );
			$ancestor_html	= "{$list_opening}<a href=\"{$ancestor_url}\">{$ancestor_title}</a>{$list_closing}";
			echo $ancestor_html;
		}
	}
}

/**
 * This method prints main category and its ancestors for the current post.
 * @return void
 */
function rddgbc_the_categories() {
	extract( rddgbc_get_options() );
	$categories = wp_get_post_categories( get_the_ID() );
	$main_category_id	= $categories[0];

	$category_ancestors = get_ancestors( $main_category_id, 'category' );
	if( $category_ancestors ) {
		foreach( $category_ancestors as $category_ancestor_id ) {
			$category_ancestor_url = get_category_link( $category_ancestor_id );
			$category_ancestor_title = get_cat_name( $category_ancestor_id );
			$category_ancestor_html = "{$list_opening}<a href=\"{$category_ancestor_url}\">{$category_ancestor_title}</a>{$list_closing}";
			echo $category_ancestor_html;
		}
	}

	$main_category_url		= get_category_link( $main_category_id );
	$main_category_title	= get_cat_name( $main_category_id );
	$main_category_html 	= "{$list_opening}<a href=\"{$main_category_url}\">{$main_category_title}</a>{$list_closing}";
	echo $main_category_html;
}
