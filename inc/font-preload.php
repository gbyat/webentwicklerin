<?php
/**
 * Preload bundled theme fonts to shorten the critical request chain.
 *
 * Uses the same theme.json font resolution as wp_print_font_faces(), but only
 * preloads local theme files (file:./), not Font Library or external sources.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'webentwicklerin_preload_theme_fonts', 1 );

/**
 * Output preload links for theme-bundled WOFF2 files WordPress will print.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_preload_theme_fonts() {
	$font_urls = webentwicklerin_get_theme_font_preload_urls();

	foreach ( $font_urls as $font_url ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="%s" crossorigin>' . "\n",
			esc_url( $font_url ),
			esc_attr( webentwicklerin_get_font_preload_mime_type( $font_url ) )
		);
	}
}

/**
 * Collect deduplicated theme font URLs from merged theme.json settings.
 *
 * @since 2.0.0
 *
 * @return string[]
 */
function webentwicklerin_get_theme_font_preload_urls() {
	if ( ! class_exists( 'WP_Font_Face_Resolver' ) ) {
		return array();
	}

	$font_groups = WP_Font_Face_Resolver::get_fonts_from_theme_json();

	if ( empty( $font_groups ) || ! is_array( $font_groups ) ) {
		return array();
	}

	$theme_uri_prefix = trailingslashit( get_theme_file_uri( '' ) );
	$urls             = array();

	foreach ( $font_groups as $font_faces ) {
		if ( ! is_array( $font_faces ) ) {
			continue;
		}

		foreach ( $font_faces as $font_face ) {
			if ( empty( $font_face['src'] ) ) {
				continue;
			}

			foreach ( (array) $font_face['src'] as $src ) {
				if ( ! is_string( $src ) || '' === $src ) {
					continue;
				}

				if ( ! str_starts_with( $src, $theme_uri_prefix ) ) {
					continue;
				}

				$urls[ $src ] = $src;
			}
		}
	}

	return array_values( $urls );
}

/**
 * Map a font file URL to its preload MIME type.
 *
 * @since 2.0.0
 *
 * @param string $font_url Font file URL.
 * @return string
 */
function webentwicklerin_get_font_preload_mime_type( $font_url ) {
	$path = wp_parse_url( $font_url, PHP_URL_PATH );

	if ( ! is_string( $path ) ) {
		return 'font/woff2';
	}

	$extension = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

	switch ( $extension ) {
		case 'woff':
			return 'font/woff';
		case 'ttf':
			return 'font/ttf';
		case 'otf':
			return 'font/otf';
		default:
			return 'font/woff2';
	}
}
