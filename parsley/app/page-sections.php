<?php

namespace App;

function _parsley_render_styles ( $style, $padding_type='p' ) {
	$classes = '';

	if ( is_array($style) ) {
		if ( ! empty($style['text_colour']) ) {
			$classes .= ' text-' . $style['text_colour'];
		}
		if ( ! empty($style['background_colour']) ) {
			$classes .= ' bg-' . $style['background_colour'];
		}
		if ( ! empty($style['border_colour']) ) {
			$classes .= ' border border-' . $style['border_colour'];
		}
		if ( ! empty($style['padding']) ) {
			$classes .= ' ' . $padding_type . '-' . $style['padding'];
		}
		if ( ! empty($style['additional_classes']) ) {
			$classes .= ' ' . $style['additional_classes'];
		}
	}

	return $classes;
}

function _parsley_render_heading ( $heading_level, $heading_tag ) {
	$classes = '';

	if ( is_array($heading_level) ) {
		if ( $heading_level['visual'] ) {
			if ( $heading_level['visual'] != $heading_tag ) {
				$classes .= ' ' . $heading_level['visual'];
			}
		}
		if ( ! empty($heading_level['padding']) ) {
			$classes .= ' p-0 my-' . $heading_level['padding'];
		}
		if ( ! empty($heading_level['additional_classes']) ) {
			$classes .= ' ' . $heading_level['additional_classes'];
		}
	}

	return $classes;
}

function parsley_render_col_html ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {

	$opts = get_sub_field('options');
	$col_wpautop = ! $opts['exact_html'];
	$col_classes =   $opts['classes'];

	$col_content = get_sub_field('content', false, false);
	if ( $col_wpautop ) {
		$col_classes .= ' column-wpautop';
		$col_content  = wpautop( $col_content );
	}

	if ( $heading_in_column ) {
		$classes .= ' section-with-heading-in-column';
		$col_classes .= ' column-containing-the-heading';
		if ( $heading_tag != 'none' ) {
			$heading_classes .= ' heading-in-column';
			$col_content = sprintf( '<%s class="%s"><span>%s</span></%s>', $heading_tag, $heading_classes, $heading, $heading_tag ) . $col_content;
			$heading_tag = 'none'; // prevent duplicate heading
		}
	}

	return sprintf( '<div class="col-type-html %s">%s</div>', $col_classes, do_shortcode($col_content) );
}

function parsley_render_col_image ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {
	$opts = get_sub_field('options');
	$col_classes = $opts['classes'];

	$iatts = [ 'loading' => 'lazy', 'class' => '' ];
	if ( get_sub_field('img_class') ) {
		$iatts['class'] = get_sub_field('img_class');
	}
	if ( get_sub_field('img_id') ) {
		$iatts['id'] = get_sub_field('img_id');
	}
	if ( get_sub_field('rounded') ) {
		$iatts['class'] .= ' rounded';
	}
	if ( get_sub_field('shadow') ) {
		$iatts['class'] .= ' shadow';
	}
	$col_content = wp_get_attachment_image( get_sub_field('image'), 'large', false, $iatts );

	return sprintf( '<div class="col-type-image %s">%s</div>', $col_classes, $col_content);
}

function _parsley_render_col_listg ( $starting_class='list-group' ) {

	$opts = get_sub_field('options');
	$col_classes =  $opts['classes'];
	
	$S = get_sub_field( 'lg_style' );
	$lg_style = [
		'border_colour'      => $S['border_colour'],
		'additional_classes' => $S['additional_classes'],
	];
	$lg_item_style = [
		'text_colour'        => $S['text_colour'],
		'background_colour'  => $S['background_colour'],
		'padding'            => $S['padding'],
	];
	
	$lg_classes = $starting_class ;
	$lg_classes .= _parsley_render_styles( $lg_style );
	
	$lg_item_style = _parsley_render_styles( $lg_item_style );
	
	$default_item_class = get_sub_field('lg_item_class');
	
	$col_content = "<ul class=\"$lg_classes\">";
	while ( have_rows('item') ) {
		the_row();
		
		$item_classes = get_sub_field('class');
		if ( ! $item_classes ) {
			$item_classes = $default_item_class;
		}
		$item_classes .= $lg_item_style;
		
		$col_content .= sprintf(
			'<li class="list-group-item %s">',
			htmlspecialchars( $item_classes )
		);
		$nugget = get_sub_field('nugget');
		if ( $nugget ) {
			if ( $nugget === 'text' ) {
				$col_content .= sprintf( '<span class="float-right">%s</span>', get_sub_field('nugget_detail') );
			}
			elseif ( $nugget === 'icon' ) {
				$col_content .= sprintf( '<i class="fa %s float-right"></i>', get_sub_field('nugget_detail') );
			}
			else {
				$col_content .= sprintf( '<i class="fa %s float-right"></i>', $nugget );
			}
		}
		$col_content .= get_sub_field('html');
		$col_content .= '</li>';
	}
	$col_content .= "</ul>";
	
	return [ $col_classes, $col_content ];
}

function _parsley_render_col_card ( $chunklist ) {

	$opts = get_sub_field('options');
	$col_wpautop = ! $opts['exact_html'];
	$col_classes =   $opts['classes'];
	if ( $col_wpautop ) {
		$col_classes .= ' column-wpautop';
	}

	$card_classes = 'card';
	if ( get_sub_field('card_border_colour') ) {
		$card_classes .= ' border border-' . get_sub_field('card_border_colour');
	}
	$card_classes .= ' ' . get_sub_field('card_class');

	$col_content = '<div class="' .trim($card_classes) . '"';
	if ( get_sub_field('card_id') ) {
		$col_content .= sprintf( ' id="%s"', htmlspecialchars( get_sub_field('card_id') ) );
	}
	$col_content .= '>';

	foreach ( $chunklist as $chunk ) {

		$content = get_sub_field( $chunk . '_content', false, false );
		if ( $col_wpautop ) {
			$content = wpautop( $content );
		}

		if ( $chunk == 'header' ) {
			$title = get_sub_field( 'header_title' );
			$title_tag = 'h3';
			$title_classes = 'card-title';
			if ( empty($title) ) {
				$title_tag = 'none';
			}
			else {
				$title_level = get_sub_field( 'header_title_level' );
				if ( $title_level['real'] ) {
					$title_tag = $title_level['real'];
				}
				$title_classes .= _parsley_render_heading( $title_level, $title_tag );
			}
			if ( $title_tag != 'none' ) {
				$content = sprintf( '<%s class="%s"><span>%s</span></%s>', $title_tag, $title_classes, $title, $title_tag )
					. $content;
			}
		}
		
		if ( $chunk == 'list-group' ) {
			$col_content .= _parsley_render_col_listg( 'list-group list-group-flush' );
		}
		elseif ( $content ) {
			$chunkclasses = "card-$chunk";
			$chunkclasses .= _parsley_render_styles( get_sub_field( $chunk . '_style' ) );
			$col_content .= sprintf('<div class="%s">%s</div>', $chunkclasses, $content);
		}
	}

	$col_content .= '</div>';

	return [ $col_classes, $col_content ];
}

function parsley_render_col_card ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {
	list ( $col_classes, $col_content ) = _parsley_render_col_card( [ 'header', 'body', 'footer' ] );
	
	return sprintf( '<div class="col-type-card %s">%s</div>', $col_classes, do_shortcode($col_content) );
}

function parsley_render_col_listg ( &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ) {
	list( $col_classes, $col_content ) = _parsley_render_col_listg();
	return sprintf( '<div class="col-type-list-group %s">%s</div>', $col_classes, do_shortcode($col_content) );
}

function parsley_render_sections () {
	$count = 0;
	while ( have_rows('design_sections') ) {
		the_row();

		if ( get_sub_field('hidden') ) {
			continue;
		}

		$wpautop = ! get_sub_field( 'exact_html' );

		$contain =   get_sub_field( 'full_width' );
		if ( $contain == 'wide' ) {
			$contain = false;
		}

		$classes = '';
		$id      = get_sub_field( 'id' );
		$content = get_sub_field( 'content', false, false );
		$count++;

		if ( $count % 2 ) {
			$classes = 'section-odd';
			if ( $count === 1 ) {
				$classes .= ' section-first';
			}
		}
		else {
			$classes = 'section-even';
		}

		$classes .= _parsley_render_styles( get_sub_field('style'), 'py' );

		$heading = get_sub_field( 'heading' );
		$heading_tag = 'h2';
		$heading_classes = 'section-title';
		if ( empty($heading) ) {
			$heading_tag = 'none';
		}
		else {
			$heading_level = get_sub_field( 'heading_level' );
			if ( $heading_level['real'] ) {
				$heading_tag = $heading_level['real'];
			}
			$heading_classes .= _parsley_render_heading( $heading_level, $heading_tag );
		}

		if ( empty($id) ) {
			$id = 'section-' . $count;
		}

		$layout  = get_row_layout();
		$content = 'CONTENT';
		if ( $layout == 'html_content' ) {
			$content = get_sub_field( 'content', false, false );
			$classes .= ' section-type-html-content';
			if ( $wpautop ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'columns' || $layout == 'columns2' ) {
			$before = get_sub_field( 'before_columns', false, false );
			$after  = get_sub_field( 'after_columns',  false, false );
			$classes .= ' section-type-columns';
			if ( $wpautop ) {
				$before   = wpautop( $before );
				$after    = wpautop( $after  );
				$classes .= ' section-wpautop';
			}
			$content  = do_shortcode( $before );
			$content .= '<div class="row">';
			$heading_in_column = get_sub_field('heading_in_column');
			while ( have_rows('columns') ) {
				the_row();

				if ( $layout === 'columns' ) {
					$col_type = 'col_html';
				}
				else {
					$col_type = get_row_layout();
				}

				$got = call_user_func_array(
					'App\parsley_render_' . $col_type,
					[ &$classes, &$heading_in_column, &$heading_tag, &$heading_classes, &$heading ] 
				);

				$content .= ( $got === false ) ? '<div class="col">ERROR</div>' : $got;
			}
			$content .= '</div>';
			$content .= do_shortcode( $after );
		}
		elseif ( $layout == 'primary_content' ) {
			$post_id = get_the_ID();
			$content = get_the_content( false, false, $post_id );
			$classes .= ' section-type-primary-content';
			if ( ! get_field( 'disable_wpautop', $post_id ) ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'post' ) {
			$post_id = get_sub_field('post_id');
			$content = get_the_content( false, false, $post_id );
			$classes .= ' section-type-post';
			if ( ! get_field( 'disable_wpautop', $post_id ) ) {
				$content  = wpautop( $content );
				$classes .= ' section-wpautop';
			}
			$content = do_shortcode( $content );
		}
		elseif ( $layout == 'foogallery' ) {
			$content = '<p>Please install FooGallery.</p>';
			$classes .= ' section-type-foogallery';
			if ( function_exists( 'foogallery_render_gallery' ) ) {
				ob_start();
				foogallery_render_gallery( get_sub_field('gallery_id') );
				$content = ob_get_contents();
				ob_end_clean();
			}
		}
		elseif ( $layout == 'image' ) {
			$classes .= ' section-type-image';
			$heading_tag = 'none';
			$contain = false;
			$iatts = [ 'loading' => 'lazy' ];
			if ( get_sub_field('img_class') ) {
				$iatts['class'] = get_sub_field('img_class');
			}
			if ( get_sub_field('img_id') ) {
				$iatts['id'] = get_sub_field('img_id');
			}
			$content = wp_get_attachment_image( get_sub_field('image'), 'full', false, $iatts );
		}

		printf( '<section id="%s" class="page-section %s">', $id, $classes );
		if ( $contain ) {
			echo '<div class="' . $contain . '">';
		}
		if ( $heading_tag != 'none' ) {
			printf( '<%s class="%s"><span>%s</span></%s>', $heading_tag, $heading_classes, $heading, $heading_tag );
		}
		echo $content;
		if ( $contain ) {
			echo '</div>';
		}
		echo "</section>\n";
	}
}
