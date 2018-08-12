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

function rddgbc_options() {
	$config = array(
		'opening_tag'		=> '<nav class="rddgbc" aria-label="breadcrumb"><ol class="rddgbc__list">',
		'closing_tag'		=> '</ol></nav>',
		'list_opening'	=> '<li class="rddgbc__list">',
		'list_current'	=> '<li class="rddgbc__item rddgbc__active" aria-current="page">',
		'list_closing'	=> '</li>'
	);
	return $config;
}

function rddgbc() {
	extract( rddgbc_options() );
	if( !is_front_page() ) {
		echo $opening_tag;
		rddgbc_get_home();
		if( is_404() ) {
			rddgbc_404();
		} elseif( is_search() ) {
			rddgbc_search();
		} elseif( is_archive() ) {
			rddgbc_archive();
		} elseif( is_singular() ) {
			rddgbc_singular();
		}
		echo $closing_tag;
	}
}

function rddgbc_get_home() {
	extract( rddgbc_options() );
	$url		= esc_url( home_url( '/' ) );
	$title	= esc_html__( 'Strona główna', 'rddgbc' );
	$html		= "{$list_opening}<a href=\"{$url}\">{$title}</a>{$list_closing}";
	echo $html;
}

function rddgbc_404() {
	extract( rddgbc_options() );
	$title	= esc_html__( 'Błąd 404', 'rddgbc' );
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

function rddgbc_search() {
	extract( rddgbc_options() );
	$title	= esc_html__( 'Wyniki wyszukiwania: ' . get_search_query(), 'rddgbc' );
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

function rddgbc_archive() {
	extract( rddgbc_options() );
	$title	= single_cat_title( '', false );
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}

function rddgbc_singular() {
	extract( rddgbc_options() );
	if( is_page() ) {
		$ancestors = array_reverse( get_ancestors( get_the_ID(), 'page' ) );
		if( $ancestors ) {
			foreach( $ancestors as $ancestor_id ) {
				$page_title = get_the_title( $ancestor_id );
				$page_url = get_page_link( $ancestor_id );
				$page_html = "{$list_opening}<a href=\"{$page_url}\">{$page_title}</a>{$list_closing}";
				echo $page_html;
			}
		}
	}
	$title	= get_the_title();
	$html		= "{$list_current}{$title}{$list_closing}";
	echo $html;
}
