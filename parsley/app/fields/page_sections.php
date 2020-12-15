<?php

namespace App;

use StoutLogic\AcfBuilder\FieldsBuilder;

function parsley_acf_section_definition ( $builder, $opts=array(), $callback=false ) {
	
	if ( ! array_key_exists( 'vary_width', $opts ) ) {
		$opts['vary_width'] = true;
	}
	
	if ( ! array_key_exists( 'styling', $opts ) ) {
		$opts['styling'] = true;
	}
	
	if ( ! array_key_exists( 'heading', $opts ) ) {
		$opts['heading'] = true;
	}
	
	$ACF_foregrounds = array(
		'primary' => 'primary',
		'secondary' => 'secondary',
		'tertiary' => 'tertiary',
		'quaternary' => 'quaternary',
		'light' => 'light',
		'dark' => 'dark',
		'white' => 'white',
	);

	$ACF_backgrounds = array(
		'primary' => 'primary',
		'secondary' => 'secondary',
		'tertiary' => 'tertiary',
		'quaternary' => 'quaternary',
		'light' => 'light',
		'dark' => 'dark',
		'white' => 'white',
		'gradient-primary' => 'gradient-primary',
		'gradient-secondary' => 'gradient-secondary',
		'gradient-tertiary' => 'gradient-tertiary',
		'gradient-quaternary' => 'gradient-quaternary',
		'gradient-light' => 'gradient-light',
		'gradient-dark' => 'gradient-dark',
	);
	
	$ACF_containment = array(
		'container'       => 'Contained',
		'container-sm'    => 'Contained from S up',
		'container-md'    => 'Contained from M up',
		'container-lg'    => 'Contained from L up',
		'container-xl'    => 'Contained from XL up',
		'container-fluid' => 'Fluid',
		'wide'            => 'Full width',
	);

	$ACF_containment_fw = array(
		'wide'            => 'Full width',
	);

	$ACF_heading_tags = array(
		'h1' => 'h1',
		'h2' => 'h2',
		'h3' => 'h3',
		'h4' => 'h4',
		'h5' => 'h5',
		'h6' => 'h6',
		'div' => 'div',
		'none' => 'none',
	);

	$ACF_heading_classes = array(
		'h1' => 'h1',
		'h2' => 'h2',
		'h3' => 'h3',
		'h4' => 'h4',
		'h5' => 'h5',
		'h6' => 'h6',
		'd-none' => 'd-none',
	);

	if ( $callback ) {
		call_user_func( $callback, $builder );
	}

	$builder->addTab( 'Section Options' );

	$builder->addText( 'id', [
		'label'         => 'ID',
		'instructions'  => 'HTML `id` attribute for styling and scripting',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
	] );

	$builder->addSelect( 'full_width', [
		'label'         => 'Width',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
		'allow_null'    => 0,
		'required'      => 1,
		'choices'       => ( $opts['vary_width'] ? $ACF_containment : $ACF_containment_fw ),
		'default_value' => ( $opts['vary_width'] ? 'container'      : 'wide' ),
		'return_format' => 'value',
	] );

	$builder->addTrueFalse( 'hidden', [
		'label'         => 'Hidden',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
		'default_value' => 0,
		'ui'            => 1,
		'ui_on_text'    => 'Hidden',
		'ui_off_text'   => 'Shown',
	] );

	$builder->addTrueFalse( 'exact_html', [
		'label'         => 'Exact HTML',
		'instructions'  => 'If exact HTML, will avoid Wordpress paragraph munging.',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
		'default_value' => 0,
		'ui'            => 1,
		'ui_on_text'    => 'Exact',
		'ui_off_text'   => 'Munge',
	] );

	if ( $opts['styling'] ) {

		$g = $builder->addGroup( 'style', [
			'label'         => 'Style',
			'layout'        => 'table',
		] );

		$g->addSelect( 'text_colour', [
			'label'         => 'Text colour',
			'allow_null'    => 1,
			'choices'       => $ACF_foregrounds,
			'default_value' => false,
			'return_format' => 'value',
		] );

		$g->addSelect( 'background_colour', [
			'label'         => 'Text colour',
			'allow_null'    => 1,
			'choices'       => $ACF_backgrounds,
			'default_value' => false,
			'return_format' => 'value',
		] );

		$g->addNumber( 'vertical_padding', [
			'label'         => 'Vertical padding',
			'instructions'  => 'A value from 0 (none) to 5 (most)',
			'required'      => 0,
			'allow_null'    => 1,
			'default_value' => 3,
			'min'           => 0,
			'max'           => 5,
			'step'          => 1,
		] );

		$g->addText( 'additional_classes', [
			'label'         => 'Additional classes',
		] );

		$g->endGroup();
	}

	if ( $opts['heading'] ) {

		$builder->addTab( 'Heading' );

		$builder->addText( 'heading', [
			'label'         => 'Heading',
		] );

		$g = $builder->addGroup( 'heading_level', [
			'label'  => 'Heading Level',
			'layout' => 'table',
		] )->conditional( 'heading', '!=empty', '' );

		$g->addSelect( 'real', [
			'label'         => 'Real tag',
			'choices'       => $ACF_heading_tags,
			'default_value' => 'h2',
			'allow_null'    => 0,
			'return_format' => 'value',
		] );

		$g->addSelect( 'visual', [
			'label'         => 'Displayed as',
			'choices'       => $ACF_heading_classes,
			'default_value' => false,
			'allow_null'    => 1,
			'return_format' => 'value',
		] )->conditional( 'real', '!=', 'none' );

		$g->endGroup();
	}

	return $builder;
}

$SEC = [];

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'primary_content', [
		'label'   => 'Primary Content',
		'display' => 'block',
	] )
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'html_content', [
		'label'   => 'HTML Content',
		'display' => 'block',
	] ),
	[],
	function ( $builder ) {

		$builder->addTab( 'Content' );

		$builder->addWysiwyg( 'content', [
			'label'         => 'Content',
			'tabs'          => 'all',
			'toolbar'       => 'full',
			'media_upload'  => 1,
		] );

	}
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'columns', [
		'label'   => 'Columns',
		'display' => 'block',
	] ),
	[],
	function ( $builder ) {

		$builder->addTab( 'Columns' );

		$builder->addTrueFalse( 'heading_in_column', [
			'label'         => 'Heading in First Column',
			'instructions'  => 'Inserts the heading into the first column instead of before the columns.',
			'ui'            => 1,
			'ui_on_text'    => 'Yes',
			'ui_off_text'   => 'No',
		] )->conditional( 'heading', '!=empty', '' );

		$r = $builder->addRepeater( 'columns', [
			'label'         => 'Columns',
			'required'      => 1,
			'min'           => 1,
			'max'           => 0,
			'layout'        => 'table',
			'button_label'  => 'Add Column',
		] );

		$r->addWysiwyg( 'content', [
			'label'         => 'Content',
			'tabs'          => 'all',
			'toolbar'       => 'full',
			'media_upload'  => 1,
			'wrapper'       => [ 'width' => '75', 'class' => '', 'id' => '' ],
		]);

		$g = $r->addGroup( 'options', [
			'label'         => 'Options',
			'layout'        => 'block',
		] );

		$g->addText( 'classes', [
			'label'         => 'Classes',
			'instructions'  => 'Classes to apply to the column; requires knowledge of the Bootstrap grid system.',
			'default_value' => 'col',
		] );

		$g->addTrueFalse( 'exact_html', [
			'label'         => 'Exact HTML',
			'instructions'  => 'If exact HTML, will avoid Wordpress paragraph munging.',
			'default_value' => 0,
			'ui'            => 1,
			'ui_on_text'    => 'Exact',
			'ui_off_text'   => 'Munge',
		] );

		$g->endGroup();

		$r->endRepeater();

		$builder->addTab( 'Extras' );

		$builder->addWysiwyg( 'before_columns', [
			'label'         => 'Before Columns',
			'tabs'          => 'all',
			'toolbar'       => 'full',
			'media_upload'  => 1,
		] );

		$builder->addWysiwyg( 'after_columns', [
			'label'         => 'After Columns',
			'tabs'          => 'all',
			'toolbar'       => 'full',
			'media_upload'  => 1,
		] );
	}
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'post', [
		'label'   => 'Post',
		'display' => 'block',
	] ),
	[],
	function ( $builder ) {

		$builder->addTab( 'Post' );

		$builder->addPostObject( 'post_id', [
			'label'         => 'Post',
			'required'      => 1,
			'return_format' => 'id',
			'ui'            => 1,
		] );

	}
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'foogallery', [
		'label'   => 'FooGallery',
		'display' => 'block',
	] ),
	[],
	function ( $builder ) {

		$builder->addTab( 'Gallery' );

		$builder->addNumber( 'gallery_id', [
			'label' => 'Gallery Id',
			'instructions' => 'Numeric identifier for gallery, found in FooGallery as part of the shortcode.',
			'required' => 1,
		] );

	}
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'image', [
		'label'   => 'Image (Full-Width)',
		'display' => 'block',
	] ),
	[
		'vary_width' => false,
		'styling'    => false,
		'heading'    => false,
	],
	function ( $builder ) {

		$builder->addTab( 'Image' );

		$builder->addImage( 'image', [
			'label'         => 'Image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'required'      => 1,
		] );

		$builder->addText( 'img_id', [
			'label'         => 'Image ID',
			'instructions'  => 'HTML `id` attribute for styling and scripting (img tag)',
			'wrapper'       => [ 'width' => '50', 'class' => '', 'id' => '' ],
		] );

		$builder->addText( 'img_class', [
			'label'         => 'Image Classes',
			'instructions'  => 'HTML `class` attribute for styling and scripting (img tag)',
			'wrapper'       => [ 'width' => '50', 'class' => '', 'id' => '' ],
			'default_value' => 'w-100 h-auto',
		] );
	}
);


$TOP = new FieldsBuilder( 'page_sections', [
	'position'      => 'normal',
	'style'         => 'seamless',
	'menu_order'    => 40,
	'title'         => 'Advanced Page Layout',
] );

$TOP->setLocation('post_type', '==', 'page');

$l = $TOP->addFlexibleContent( 'design_sections', [
	'title'         => 'Design Sections',
	'instructions'  => 'Sections of content to display instead of main page content.',
	'button_label'  => 'Add Section',
	'min'           => 0,
	'max'           => 50,
] );
foreach ( $SEC as $s ) {
	$l->addLayout($s);
}
$l->endFlexibleContent();

acf_add_local_field_group( $TOP->build() );
