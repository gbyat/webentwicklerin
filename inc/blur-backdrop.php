<?php

/**
 * Blur backdrop block style helpers (featured image, image, gallery).
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

add_filter( 'register_block_type_args', 'webentwicklerin_blur_backdrop_enable_image_padding', 10, 2 );
add_action( 'enqueue_block_editor_assets', 'webentwicklerin_blur_backdrop_editor_assets' );
add_filter( 'render_block_core/post-featured-image', 'webentwicklerin_blur_backdrop_render_block', 10, 2 );
add_filter( 'render_block_core/image', 'webentwicklerin_blur_backdrop_render_block', 10, 2 );
add_filter( 'render_block_core/gallery', 'webentwicklerin_blur_backdrop_render_block', 10, 2 );

/**
 * Enable padding on Image and Gallery blocks.
 *
 * Core omits padding on core/image; with blur-backdrop, padding insets the sharp image.
 *
 * @since 2.0.0
 *
 * @param array  $args       Block type registration arguments.
 * @param string $block_type Block name including namespace.
 * @return array
 */
function webentwicklerin_blur_backdrop_enable_image_padding( $args, $block_type ) {
	if ( ! in_array( $block_type, array( 'core/image', 'core/gallery' ), true ) ) {
		return $args;
	}

	if ( empty( $args['supports']['spacing'] ) || ! is_array( $args['supports']['spacing'] ) ) {
		$args['supports']['spacing'] = array();
	}

	$args['supports']['spacing']['padding'] = true;

	return $args;
}

/**
 * Enqueue editor script for blur-backdrop block style preview.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_blur_backdrop_editor_assets() {
	wp_enqueue_script(
		'webentwicklerin-blur-backdrop-editor',
		get_theme_file_uri( 'assets/js/blur-backdrop-editor.min.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

/**
 * Apply blur-backdrop markup to supported image blocks.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function webentwicklerin_blur_backdrop_render_block( $block_content, $block ) {
	$block_name = $block['blockName'] ?? '';

	if ( 'core/gallery' === $block_name ) {
		return webentwicklerin_blur_backdrop_render_gallery( $block_content );
	}

	if ( false === strpos( $block_content, 'is-style-blur-backdrop' ) ) {
		return $block_content;
	}

	if ( ! preg_match( '/<figure\b/i', $block_content ) ) {
		return $block_content;
	}

	return preg_replace_callback(
		'/<figure\b[^>]*>[\s\S]*?<\/figure>/',
		static function ( $matches ) {
			return webentwicklerin_blur_backdrop_process_figure( $matches[0] );
		},
		$block_content,
		1
	);
}

/**
 * Inject blur layer, move padding to the image, and fit the sharp image inside the frame.
 *
 * @since 2.0.0
 *
 * @param string $figure_html Single figure element HTML.
 * @return string
 */
function webentwicklerin_blur_backdrop_process_figure( $figure_html ) {
	if ( ! preg_match( '/<img\b[^>]*\bsrc=(["\'])([^"\']+)\1/', $figure_html, $img_matches ) ) {
		return $figure_html;
	}

	$custom_property = sprintf( "--featured-image-url: url('%s');", esc_url( $img_matches[2] ) );

	$figure_html = preg_replace_callback(
		'/<figure\b([^>]*)>/',
		static function ( $figure_matches ) use ( $custom_property ) {
			$attributes = $figure_matches[1];

			if ( preg_match( '/\sstyle=(["\'])([^"\']*)\1/', $attributes, $style_matches ) ) {
				$quote          = $style_matches[1];
				$padding_parsed = webentwicklerin_blur_backdrop_parse_padding( $style_matches[2] );
				$inset_property = webentwicklerin_blur_backdrop_inset_properties( $padding_parsed['insets'] );
				$new_styles     = trim( $padding_parsed['styles'], '; ' );
				$new_styles     = '' !== $new_styles ? $new_styles . ';' : '';
				$new_styles    .= $inset_property . $custom_property;
				$attributes     = preg_replace(
					'/\sstyle=(["\'])[^"\']*\1/',
					' style=' . $quote . esc_attr( $new_styles ) . $quote,
					$attributes,
					1
				);
			} else {
				$inset_property = webentwicklerin_blur_backdrop_inset_properties(
					array(
						'top'    => '0',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
					)
				);
				$attributes    .= ' style="' . esc_attr( $inset_property . $custom_property ) . '"';
			}

			return '<figure' . $attributes . '>';
		},
		$figure_html,
		1
	);

	$figure_html = preg_replace(
		'/(<figure\b[^>]*>)/',
		'$1<span class="featured-image-blur-backdrop" aria-hidden="true"></span>',
		$figure_html,
		1
	);

	return webentwicklerin_blur_backdrop_fit_image( $figure_html );
}

/**
 * Apply blur-backdrop to gallery items (whole gallery or single images in the gallery).
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered gallery HTML.
 * @return string
 */
function webentwicklerin_blur_backdrop_render_gallery( $block_content ) {
	if ( false === strpos( $block_content, 'is-style-blur-backdrop' ) ) {
		return $block_content;
	}

	$gallery_has_style = (bool) preg_match(
		'/<figure\b[^>]*\bwp-block-gallery\b[^>]*\bis-style-blur-backdrop\b/',
		$block_content
	);

	return preg_replace_callback(
		'/<figure\b(?![^>]*\bwp-block-gallery\b)[^>]*>[\s\S]*?<\/figure>/',
		static function ( $matches ) use ( $gallery_has_style ) {
			if ( ! preg_match( '/<img\b/', $matches[0] ) ) {
				return $matches[0];
			}

			if ( ! $gallery_has_style && false === strpos( $matches[0], 'is-style-blur-backdrop' ) ) {
				return $matches[0];
			}

			return webentwicklerin_blur_backdrop_process_figure( $matches[0] );
		},
		$block_content
	);
}

/**
 * Parse padding from inline styles and remove it from the figure element.
 *
 * @since 2.0.0
 *
 * @param string $styles Inline style attribute value.
 * @return array {
 *     @type array  $insets Side values for top, right, bottom, left.
 *     @type string $styles Remaining inline styles without padding.
 * }
 */
function webentwicklerin_blur_backdrop_parse_padding( $styles ) {
	$insets = array(
		'top'    => '0',
		'right'  => '0',
		'bottom' => '0',
		'left'   => '0',
	);

	foreach ( array_keys( $insets ) as $side ) {
		$pattern = '/\bpadding-' . $side . '\s*:\s*([^;]+)/i';

		if ( preg_match( $pattern, $styles, $matches ) ) {
			$insets[ $side ] = trim( $matches[1] );
			$styles          = preg_replace( $pattern, '', $styles );
		}
	}

	if ( preg_match( '/\bpadding\s*:\s*([^;]+)/i', $styles, $matches ) ) {
		$parts = preg_split( '/\s+/', trim( $matches[1] ) );
		$count = count( $parts );

		if ( 1 === $count ) {
			$insets = array(
				'top'    => $parts[0],
				'right'  => $parts[0],
				'bottom' => $parts[0],
				'left'   => $parts[0],
			);
		} elseif ( 2 === $count ) {
			$insets = array(
				'top'    => $parts[0],
				'right'  => $parts[1],
				'bottom' => $parts[0],
				'left'   => $parts[1],
			);
		} elseif ( 3 === $count ) {
			$insets = array(
				'top'    => $parts[0],
				'right'  => $parts[1],
				'bottom' => $parts[2],
				'left'   => $parts[1],
			);
		} elseif ( 4 === $count ) {
			$insets = array(
				'top'    => $parts[0],
				'right'  => $parts[1],
				'bottom' => $parts[2],
				'left'   => $parts[3],
			);
		}

		$styles = preg_replace( '/\bpadding\s*:[^;]+;?/i', '', $styles );
	}

	$styles = trim( preg_replace( '/;+/', ';', $styles ), '; ' );

	return array(
		'insets' => $insets,
		'styles' => $styles,
	);
}

/**
 * Build CSS custom properties for image inset from block padding values.
 *
 * @since 2.0.0
 *
 * @param array $insets Padding values keyed by side.
 * @return string
 */
function webentwicklerin_blur_backdrop_inset_properties( $insets ) {
	return sprintf(
		'--featured-image-inset-top:%1$s;--featured-image-inset-right:%2$s;--featured-image-inset-bottom:%3$s;--featured-image-inset-left:%4$s;',
		$insets['top'],
		$insets['right'],
		$insets['bottom'],
		$insets['left']
	);
}

/**
 * Whether the figure uses an aspect-ratio frame (inline style on figure).
 *
 * @since 2.0.0
 *
 * @param string $figure_html Figure HTML.
 * @return bool
 */
function webentwicklerin_blur_backdrop_figure_has_aspect_ratio( $figure_html ) {
	return (bool) preg_match( '/<figure\b[^>]*\sstyle=["\'][^"\']*aspect-ratio/i', $figure_html );
}

/**
 * Fit the sharp image inside the figure frame at its natural proportions.
 *
 * Only applies when the figure has an aspect ratio. Otherwise WordPress size
 * and alignment controls are left intact.
 *
 * @since 2.0.0
 *
 * @param string $figure_html Figure HTML including the image.
 * @return string
 */
function webentwicklerin_blur_backdrop_fit_image( $figure_html ) {
	if ( ! webentwicklerin_blur_backdrop_figure_has_aspect_ratio( $figure_html ) ) {
		return $figure_html;
	}

	$fit_styles = 'max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain';

	return preg_replace_callback(
		'/<img\b([^>]*)\/?>/',
		static function ( $matches ) use ( $fit_styles ) {
			$attributes = $matches[1];

			if ( preg_match( '/\sstyle=(["\'])([^"\']*)\1/', $attributes, $style_matches ) ) {
				$styles = $style_matches[2];

				$styles = preg_replace( '/\bwidth\s*:\s*[^;]+;?/i', '', $styles );
				$styles = preg_replace( '/\bheight\s*:\s*[^;]+;?/i', '', $styles );
				$styles = preg_replace( '/\bobject-fit\s*:[^;]+;?/i', '', $styles );
				$styles = preg_replace( '/\baspect-ratio\s*:[^;]+;?/i', '', $styles );
				$styles = trim( $styles, '; ' );

				if ( '' !== $styles ) {
					$fit_styles = $styles . ';' . $fit_styles;
				}

				$attributes = preg_replace(
					'/\sstyle=(["\'])[^"\']*\1/',
					' style="' . esc_attr( $fit_styles ) . '"',
					$attributes,
					1
				);
			} else {
				$attributes .= ' style="' . esc_attr( $fit_styles ) . '"';
			}

			return '<img' . $attributes . '>';
		},
		$figure_html,
		1
	);
}
