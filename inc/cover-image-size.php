<?php
/**
 * Cover block image size control for templates and site content.
 *
 * Core Cover supports sizeSlug for featured images but has no editor control and
 * always stores full-size URLs for static media backgrounds.
 *
 * @package webentwicklerin
 * @since   2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/cover', 'webentwicklerin_cover_apply_image_size', 10, 2 );
add_action( 'enqueue_block_editor_assets', 'webentwicklerin_cover_image_size_editor_assets' );

/**
 * Enqueue the Cover image size inspector control in the block editor.
 *
 * @since 2.0.0
 *
 * @return void
 */
function webentwicklerin_cover_image_size_editor_assets() {
	wp_enqueue_script(
		'webentwicklerin-cover-image-size-editor',
		get_theme_file_uri( 'assets/js/cover-image-size-editor.min.js' ),
		array( 'wp-hooks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script(
		'webentwicklerin-cover-image-size-editor',
		'webentwicklerinCoverImageSize',
		array(
			'imageSizes' => webentwicklerin_get_cover_image_size_options(),
		)
	);
}

/**
 * Build image size options for the Cover block editor control.
 *
 * @since 2.0.0
 *
 * @return array<int, array{slug: string, name: string}>
 */
function webentwicklerin_get_cover_image_size_options() {
	$size_labels = apply_filters(
		'image_size_names_choose',
		array(
			'thumbnail' => __( 'Thumbnail', 'webentwicklerin' ),
			'medium'    => __( 'Medium', 'webentwicklerin' ),
			'large'     => __( 'Large', 'webentwicklerin' ),
			'full'      => __( 'Full Size', 'webentwicklerin' ),
		)
	);

	$options = array();

	foreach ( $size_labels as $slug => $name ) {
		$options[] = array(
			'slug' => (string) $slug,
			'name' => (string) $name,
		);
	}

	return $options;
}

/**
 * Resolve the attachment ID for a Cover block background image.
 *
 * @since 2.0.0
 *
 * @param array $block Block data.
 * @return int Attachment ID or 0.
 */
function webentwicklerin_cover_get_attachment_id( $block ) {
	$attributes = $block['attrs'] ?? array();

	if ( ! empty( $attributes['useFeaturedImage'] ) ) {
		$post_id = $block['context']['postId'] ?? get_the_ID();

		if ( $post_id ) {
			return (int) get_post_thumbnail_id( $post_id );
		}

		return 0;
	}

	if ( ! empty( $attributes['id'] ) ) {
		return (int) $attributes['id'];
	}

	return 0;
}

/**
 * Resolve the background image URL for a Cover block.
 *
 * Used by the image-size render filter and blur-backdrop so both share sizeSlug.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string Image URL or empty string.
 */
function webentwicklerin_cover_resolve_background_image_url( $block_content, $block ) {
	$attributes      = $block['attrs'] ?? array();
	$size_slug       = $attributes['sizeSlug'] ?? '';
	$attachment_id   = webentwicklerin_cover_get_attachment_id( $block );
	$has_custom_size = '' !== $size_slug && 'full' !== $size_slug;

	if ( $attachment_id && $has_custom_size ) {
		$image_src = wp_get_attachment_image_src( $attachment_id, $size_slug );

		if ( ! empty( $image_src[0] ) ) {
			return $image_src[0];
		}
	}

	if ( preg_match( '/<img\b[^>]*\bwp-block-cover__image-background\b[^>]*\bsrc=(["\'])([^"\']+)\1/i', $block_content, $img_matches ) ) {
		return $img_matches[2];
	}

	if ( preg_match( '/<img\b[^>]*\bwp-block-cover__image-background\b[^>]*\bsrcset=(["\'])([^"\']+)\1/i', $block_content, $srcset_matches ) ) {
		$first_candidate = trim( explode( ',', $srcset_matches[2] )[0] );
		$parts           = preg_split( '/\s+/', $first_candidate );

		if ( ! empty( $parts[0] ) ) {
			return $parts[0];
		}
	}

	if ( preg_match( '/\bbackground-image\s*:\s*url\(\s*(["\']?)([^"\')]+)\1\s*\)/i', $block_content, $background_matches ) ) {
		return $background_matches[2];
	}

	if ( $attachment_id ) {
		$fallback_size = $has_custom_size ? $size_slug : 'full';
		$image_src     = wp_get_attachment_image_src( $attachment_id, $fallback_size );

		if ( ! empty( $image_src[0] ) ) {
			return $image_src[0];
		}
	}

	if ( ! empty( $attributes['url'] ) && is_string( $attributes['url'] ) ) {
		return $attributes['url'];
	}

	return '';
}

/**
 * Apply the selected image size to Cover block output on the front end.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function webentwicklerin_cover_apply_image_size( $block_content, $block ) {
	$source = webentwicklerin_cover_resolve_image_source( $block );

	if ( null === $source ) {
		return $block_content;
	}

	return webentwicklerin_cover_replace_background_image( $block_content, $source );
}

/**
 * Resolve the attachment and selected size for a Cover block.
 *
 * @since 2.0.0
 *
 * @param array $block Block data.
 * @return array|null Image source data or null when unchanged.
 */
function webentwicklerin_cover_resolve_image_source( $block ) {
	$attributes = $block['attrs'] ?? array();
	$size_slug  = $attributes['sizeSlug'] ?? '';

	if ( '' === $size_slug || 'full' === $size_slug ) {
		return null;
	}

	$attachment_id = webentwicklerin_cover_get_attachment_id( $block );

	if ( ! $attachment_id ) {
		return null;
	}

	$image_src = wp_get_attachment_image_src( $attachment_id, $size_slug );

	if ( empty( $image_src[0] ) ) {
		return null;
	}

	return array(
		'url'    => $image_src[0],
		'width'  => ! empty( $image_src[1] ) ? (int) $image_src[1] : 0,
		'height' => ! empty( $image_src[2] ) ? (int) $image_src[2] : 0,
		'srcset' => (string) wp_get_attachment_image_srcset( $attachment_id, $size_slug ),
		'sizes'  => (string) wp_get_attachment_image_sizes( $attachment_id, $size_slug ),
	);
}

/**
 * Replace Cover background image markup with the chosen image size.
 *
 * @since 2.0.0
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $source        Resolved image source data.
 * @return string
 */
function webentwicklerin_cover_replace_background_image( $block_content, $source ) {
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	$updated   = false;
	$escaped   = esc_url( $source['url'] );

	while ( $processor->next_tag(
		array(
			'tag_name'   => 'IMG',
			'class_name' => 'wp-block-cover__image-background',
		)
	) ) {
		$processor->set_attribute( 'src', $source['url'] );

		if ( ! empty( $source['srcset'] ) ) {
			$processor->set_attribute( 'srcset', $source['srcset'] );
		} else {
			$processor->remove_attribute( 'srcset' );
		}

		if ( ! empty( $source['sizes'] ) ) {
			$processor->set_attribute( 'sizes', $source['sizes'] );
		} else {
			$processor->remove_attribute( 'sizes' );
		}

		if ( ! empty( $source['width'] ) ) {
			$processor->set_attribute( 'width', (string) $source['width'] );
		}

		if ( ! empty( $source['height'] ) ) {
			$processor->set_attribute( 'height', (string) $source['height'] );
		}

		$style = $processor->get_attribute( 'style' );

		if ( is_string( $style ) && false !== stripos( $style, 'background-image' ) ) {
			$style = preg_replace(
				'/background-image\s*:\s*url\(\s*["\']?[^"\')]+["\']?\s*\)/i',
				'background-image:url(' . $escaped . ')',
				$style
			);
			$processor->set_attribute( 'style', $style );
		}

		$updated = true;
	}

	if ( ! $updated ) {
		return $block_content;
	}

	return $processor->get_updated_html();
}
