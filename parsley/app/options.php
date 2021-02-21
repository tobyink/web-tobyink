<?php

namespace App;
use StoutLogic\AcfBuilder\FieldsBuilder;

function theme_colours () {
	return [ 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light', 'success', 'danger', 'warning', 'info' ];
}

function theme_get_option ( $id ) {
	$id = str_replace( '-', '_', $id );
	return get_field( 'parsley_' . $id, 'option' );
}

function theme_write_sass () {
	$sass = [];

	$urls = theme_get_option( 'font-imports' );
	foreach ( explode( "\n", $urls ) as $l ) {
		if ( preg_match( '/^http/', $l ) ) {
			$sass []= sprintf( '@import url(%s);', str_replace( ';', '%3B', trim($l) ) );
		}
	}

	foreach ( [ 'serif', 'sans', 'monospace', 'display' ] as $f ) {
		$sass []= sprintf( '$wp-theme-font-%s: %s;', $f, theme_get_option( "font-$f" ) );
	}

	foreach ( [ 'base', 'headings' ] as $f ) {
		$sass []= sprintf('$wp-theme-font-%s: $wp-theme-font-%s;', $f, theme_get_option( "font-$f" ) );
	}

	foreach ( theme_colours() as $c ) {
		$sass [] = sprintf( '$wp-theme-colour-%s: %s;', $c, theme_get_option( "colour-$c" ) );
	}

	foreach ( [
			'page-bg', 'page-text', 'page-link', 'page-muted',
			'header-bg', 'header-text', 'header-hover', 'header-dropdown-bg', 'header-dropdown-text',
			'floating-icons-bg', 'floating-icons-text', 'floating-icons-hover', 'floating-icons-class',
		] as $opt ) {
		$sass [] = sprintf( '$wp-theme-%s: %s;', $opt, theme_get_option( $opt ) );
	}

	$final = '';
	foreach ( $sass as $l ) {
		$final .= "$l\n";
	}

	$file = __DIR__ . '/../resources/assets/styles/common/_wp_theme.scss';
	file_put_contents( $file, $final );
	system( "chmod g+rwX '$file'" );
}

function theme_compile_sass () {
	$olddir = getcwd();
	chdir( __DIR__ . '/..'  );
	system( './yarn build' );
	system( 'chmod -R g+rwX ./dist' );
	chdir( $olddir );
}

add_action( 'acf/save_post', function () {
	$screen = get_current_screen();
	if ( strpos( $screen->id, "parsley-options" ) == true ) {
		theme_write_sass();
		theme_compile_sass();
	}
}, 99 );

add_action( 'acf/init', function () {

	acf_add_options_page( [
		'page_title' => 'Parsley Options',
		'menu_slug'  => 'parsley-options',
		'capability' => 'edit_theme_options',
		'position'   => '61',
		'autoload'   => true,
	] );

	$opts = new FieldsBuilder( 'colours', [ 'order' => 10 ] );
	$opts->setLocation( 'options_page', '==', 'parsley-options' );

	$colour_defaults = [
		'primary'     => '#007bff',
		'secondary'   => '#6c757d',
		'tertiary'    => '#fd7e14',
		'quaternary'  => '#20c997',
		'dark'        => '#343a40',
		'light'       => '#f8f9fa',
		'success'     => '#28a745',
		'danger'      => '#dc3545',
		'warning'     => '#ffc107',
		'info'        => '#17a2b8',
	];

	foreach ( $colour_defaults as $label => $default ) {
		$opts->addColorPicker( "parsley_colour_$label", [
			'label'          => ucfirst($label),
			'default_value'  => $default,
			'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
		] );
	}

	$opts->addText( 'parsley_page_bg', [
		'label'          => 'Body Background Colour',
		'default_value'  => 'white',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addText( 'parsley_page_text', [
		'label'          => 'Text Colour',
		'default_value'  => '$wp-theme-colour-dark',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addText( 'parsley_page_link', [
		'label'          => 'Link Colour',
		'default_value'  => 'lighten($wp-theme-colour-primary, 10%)',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addText( 'parsley_page_muted', [
		'label'          => 'Muted Text Colour',
		'default_value'  => 'lighten($wp-theme-colour-dark, 30%)',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	acf_add_local_field_group( $opts->build() );

	$opts = new FieldsBuilder( 'fonts', [ 'order' => 20 ] );
	$opts->setLocation( 'options_page', '==', 'parsley-options' );

	$font_defaults = [
		'serif'     => "Georgia, 'Times New Roman', Times, serif",
		'sans'      => "'Helvetica Neue', Helvetica, Arial, sans-serif",
		'monospace' => "Menlo, Monaco, Consolas, 'Courier New', monospace",
		'display'   => "Techno, Impact, sans-serif",
	];

	foreach ( $font_defaults as $label => $default ) {
		$opts->addText( "parsley_font_$label", [
			'label'          => ucfirst($label),
			'default_value'  => $default,
			'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
		] );
	}

	$opts->addSelect( 'parsley_font_base', [
		'label'          => 'Base font',
		'default_value'  => 'serif',
		'choices'        => array_keys( $font_defaults ),
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addSelect( 'parsley_font_headings', [
		'label'          => 'Headings font',
		'default_value'  => 'display',
		'choices'        => array_keys( $font_defaults ),
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addTextarea( 'parsley_font_imports', [
		'label'          => 'Import URLs',
	] );

	acf_add_local_field_group( $opts->build() );

	$opts = new FieldsBuilder( 'footer', [ 'order' => 300 ] );
	$opts->setLocation( 'options_page', '==', 'parsley-options' );

	$opts->addTrueFalse( 'parsley_scroll_to_top', [ 'label' => 'Show "Scroll to Top"', 'default_value' => true ] );

	$opts->addText( 'parsley_floating_icons_bg', [
		'label'          => 'Floating Menu Background Colour',
		'default_value'  => '$wp-theme-colour-primary',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addText( 'parsley_floating_icons_text', [
		'label'          => 'Floating Menu Foreground Colour',
		'default_value'  => 'white',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addText( 'parsley_floating_icons_hover', [
		'label'          => 'Floating Menu Hover Background Colour',
		'default_value'  => 'lighten($wp-theme-colour-primary, 10%)',
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	$opts->addSelect( 'parsley_floating_icons_class', [
		'label'          => 'Floating Menu Class',
		'default_value'  => 'floating-icons-left',
		'choices'        => [ 'floating-icons-left', 'floating-icons-right' ],
		'wrapper'        => [ 'width' => '50%', 'id' => '', 'class' => '' ],
	] );

	acf_add_local_field_group( $opts->build() );

	$opts = new FieldsBuilder( 'header', [ 'order' => 100 ] );
	$opts->setLocation( 'options_page', '==', 'parsley-options' );
	$opts->addTextarea( 'parsley_header_html', [ 'label' => 'HTML Above Banner' ] );
	$opts->addImage( 'parsley_header_image', [ 'label' => 'Banner Branding Icon', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'return_format' => 'id', 'instructions' => 'A maximum size of 220 by 60 pixels is recommended.' ] );
	$opts->addText( 'parsley_header_title', [ 'label' => 'Banner Branding Title', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ] ] );
	$opts->addSelect( 'parsley_header_style', [ 'label' => 'Banner Branding Style', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => 'text', 'choices' => [ 'text', 'image', 'none' ] ] );
	$opts->addText( 'parsley_header_bg', [ 'label' => 'Banner Background Colour', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => '$wp-theme-colour-primary' ] );
	$opts->addText( 'parsley_header_text', [ 'label' => 'Banner Foreground Colour', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => '$wp-theme-colour-light' ] );
	$opts->addText( 'parsley_header_hover', [ 'label' => 'Banner Hover/Active Colour', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => 'white' ] );
	$opts->addText( 'parsley_header_menu_class', [ 'label' => 'Banner Menu Extra Classes', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => 'ml-auto', 'instructions' => 'Recommended <code>ml-auto</code>' ] );
	$opts->addText( 'parsley_header_dropdown_bg', [ 'label' => 'Banner Dropdown Background Colour', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => '$wp-theme-colour-secondary' ] );
	$opts->addText( 'parsley_header_dropdown_text', [ 'label' => 'Banner Dropdown Foreground Colour', 'wrapper' => [ 'width' => '50%', 'id' => '', 'class' => '' ], 'default_value' => '$wp-theme-colour-light' ] );
	acf_add_local_field_group( $opts->build() );
} );
