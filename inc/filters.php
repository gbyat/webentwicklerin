<?php

/**
 * Filters
 *
 * @package webethm
 * @since 1.0.0
 */

/**
 * Show '(No title)' if a post has no title.
 *
 * @since 1.0.0
 */
add_filter(
	'the_title',
	function ($title) {
		if (! is_admin() && empty($title)) {
			$title = _x('(No title)', 'Used if post or pages has no title', THEME_NAME);
		}

		return $title;
	}
);

/**
 * Replace the default [...] excerpt more with an elipsis.
 *
 * @since 1.0.0
 */
add_filter(
	'excerpt_more',
	function ($more) {
		return '&hellip;';
	}
);


add_filter('do_redirect_guess_404_permalink', '__return_false');


function remove_wp_block_menu()
{
	remove_menu_page('edit.php?post_type=wp_block');
}
add_action('admin_menu', 'remove_wp_block_menu', 999);


/** Navi mobile icon ersetzen */
add_filter('render_block_core/navigation', function ($content, $parsed_block, $block) {
	$atts = $block->attributes;
	$icons = webethm_icon_library();
	$icon = strtolower($atts['icon']);
	if (!is_array($icons)) return $content;
	if (is_array($icons) && !array_key_exists($icon, $icons)) return $content;
	if ($icon  == 'menu') {
		//	$default ='<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z" /></svg>';
		return preg_replace('/\<svg width(.*?)\<\/svg\>/', htmlspecialchars_decode($icons[$icon]), $content);
	}
	return $content;
}, 10, 3);


add_filter('query_loop_block_query_vars', 'related_posts_filter_query');
function related_posts_filter_query($query)
{
	if (!is_singular('post')) return $query;
	global $post;

	if ('post' !== $query['post_type']) {
		return $query;
	}

	$taxonomies = get_object_taxonomies($post, 'names');
	if ($taxonomies) {
		foreach ($taxonomies as $taxonomy) {
			$terms = get_the_terms($post->ID, $taxonomy);
			if (empty($terms)) {
				continue;
			}
			$term_list  = wp_list_pluck($terms, 'slug');
			$query['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_list
			);
		}
	}

	if (count($query['tax_query']) > 1) {
		$query['tax_query']['relation'] = 'OR';
	}

	$query['orderby'] = 'rand';
	$query['post__not_in'] = [$post->ID];

	return $query;
}


add_filter('wp_mail_from', function ($mail) {
	return 'deine@webentwicklerin.at';
});
add_filter('wp_mail_from_name', function ($sender) {
	return 'webentwicklerin WordPress';
});

add_filter('site_status_tests', function ($tests) {
	unset($tests['direct']['persistent_object_cache']);
	unset($tests['direct']['perfopsone_objectcache']);
	return $tests;
}, 14);

//comments for extension
add_action('comment_form_before', function () {
	printf('<p class="before-comment" style="margin-top:35px;padding:15px;border:1px solid #ccc;line-height:1.4em;"><small>%s</small></p>', 'Bitte Kommentarfunktion nicht für Supportanfragen nutzen. Dem kann hier nicht entsprochen werden.
Die Angabe einer E-Mail-Adresse und eines Namens ist nicht erforderlich. Einen (Spitz)-Namen zu nennen wäre aber doch nett. ');
});

add_filter('comment_form_default_fields', function ($fields) {

	$commenter = wp_get_current_commenter();
	unset($fields['url']);
	$fields['consent'] = '<p class="comment-form-consent">' .
		'<input id="consent" name="consent" type="checkbox" size="30" aria-required="true" value="Einverständnis zur Speicherung persönlicher Daten gegeben" />* ' .
		'<label for="consent">' . 'Hiermit erteile ich mein Einverständnis zur Speicherung der übermittelten Daten bis auf Widerruf' . '</label></p>';

	return $fields;
});

add_action('comment_post', function ($comment_id) {
	if ((isset($_POST['consent'])) && ($_POST['consent'] != ''))
		$consent = wp_filter_nohtml_kses($_POST['consent']);
	add_comment_meta($comment_id, 'consent', $consent);
});

add_filter('preprocess_comment', function ($commentdata) {
	if (! isset($_POST['consent']))
		wp_die(__('Bitte stimmen Sie der Speicherung der von Ihnen angegebenen Daten zu.'));
	return $commentdata;
});

add_action('comment_form_after_fields', function () {
	printf('<p class="after-comment"><strong>%s</strong> %s</p>', 'Hinweis:', 'Sowohl angegebener Name als auch E-Mail-Adresse (beides ist optional, dafür werden alle Kommentare vor Veröffentlichung geprüft) werden dauerhaft gespeichert. Du kannst jeder Zeit die Löschung Deiner Daten oder / und Kommentare einfordern, direkt über dieses Formular (wird nicht veröffentlicht, und im Anschluss gelöscht), und ich werde das umgehend erledigen. - Mit hinterlassenen Kommentaren hinterlegte IP-Adressen werden nach zwei Monaten automatisch gelöscht');
});


add_filter('pre_comment_user_ip', function ($comment_author_ip) {
	return '127.0.0.1';
});

// mark thumbnails
function flxo_mark_used_as_thumbnail()
{

	global $wpdb;

	/* 
	 * get all image-IDS that are post-thumbnails 
	 */

	$request = $wpdb->get_results("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_thumbnail_id'");

	/**
	 * the .media-icon element is a span with thumbnail inside in the list view
	 * the .attachment element is a li with thumbnail inside in the grid view
	 */

	$css = '.media-icon {
		position:relative;
	}
	li.attachment::before,
	span.media-icon::before {
		position:absolute;
		display:block;
		height:20px;
		width:20px;
		background:#95c11e;
		color:#fff;
		content:"0";
		text-align:center;
		font-size:13px;
		font-weight:700;
		border-radius:50%;
		z-index:1;
	}
	li.attachment::before {
		right:0;
		top:0;
	}
	span.media-icon::before {
		right:-7px;
		top:-7px;
	}';

	$ids = array();

	/**
	 * prepare: image-id => image-count pairs
	 */
	foreach ($request as $id) {
		if (!isset($ids[$id->meta_value])) {
			$ids[$id->meta_value] = 1;
		} else {
			$ids[$id->meta_value] = $ids[$id->meta_value] + 1;
		}
	}

	foreach ($ids as $class => $num) {
		$css .= 'li[data-id="' . absint($class) . '"]::before,
		#post-' . absint($class) . ' .media-icon::before {
			background:#7d004d;
			content:"' . absint($num) . '";
		}';
	}

	printf('<style>%1$s</style>', $css);
}

add_action('admin_head', 'flxo_mark_used_as_thumbnail');


function wpdocs_addTitleToThumbnail($html)
{
	if (!is_home() && !is_archive() && !is_category() & !is_tag()) return $html;
	$id = get_post_thumbnail_id();
	$post = get_post($id);
	$source = $post->post_excerpt;
	if (empty($source)) return $html;
	// $h = is_singular() ? 'h2' : 'h3';
	$h = 'strong';
	$after = sprintf('<figcaption class="image-source"><%s>%s</%s></figcaption>', $h, wp_kses_post($source), $h);
	return $html . $after;
}
add_filter('post_thumbnail_html', 'wpdocs_addTitleToThumbnail');

function disable_lazy_load_single_featured($attr, $attachment, $size)
{
	if (!is_single()) return $attr;

	$attr['loading'] = 'eager';

	return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'disable_lazy_load_single_featured', 10, 3);

function GetLastPostId()
{
	global $wpdb;

	$query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

	$result = $wpdb->get_results($query);
	$row = $result[0];
	$id = $row->ID;

	return $id;
}

function do_not_load_lazy_last($attr, $attachment, $size)
{
	if (!is_home()) return $attr;
	$post_id = GetLastPostId();
	$img_id = get_post_thumbnail_id($post_id);
	if ($img_id == $attachment) {
		$attr['loading'] = 'eager';
	}
	return $attr;
}
// add_filter( 'wp_get_attachment_image_attributes', 'do_not_load_lazy_last', 10, 3 );

function my_plugin_posts_columns($columns)
{
	$first = array('cb' => $columns['cb']);
	$second = array('show_thumbnail' => esc_html__('Thumbnail'));
	unset($columns['cb']);
	return $first + $second + $columns;
}
add_filter('manage_posts_columns', 'my_plugin_posts_columns');

function snippetpress_show_post_thumbnail_column($columns)
{
	// echo '<style>#show_thumbnail{width:90px;}</style>';
	switch ($columns) {
		case 'show_thumbnail':
			echo the_post_thumbnail(array('80', '80'));
			break;
	}
}
add_action('manage_posts_custom_column', 'snippetpress_show_post_thumbnail_column', 12, 3);
