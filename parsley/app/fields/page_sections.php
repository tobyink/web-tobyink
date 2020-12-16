<?php

namespace App;

use StoutLogic\AcfBuilder\FieldsBuilder;

function _parsley_acf_style ( $builder, $group_name='style', $group_label='Style', $paddingsize=false ) {
	
	$g = $builder->addGroup( $group_name, [
		'label'         => $group_label,
		'layout'        => 'table',
	] );

	$g->addSelect( 'text_colour', [
		'label'         => 'Text colour',
		'allow_null'    => 1,
		'choices'       => array(
			'primary' => 'primary',
			'secondary' => 'secondary',
			'tertiary' => 'tertiary',
			'quaternary' => 'quaternary',
			'light' => 'light',
			'dark' => 'dark',
			'success' => 'success',
			'danger' => 'danger',
			'warning' => 'warning',
			'info' => 'info',
			'white' => 'white',
		),
		'default_value' => false,
		'return_format' => 'value',
	] );

	$g->addSelect( 'background_colour', [
		'label'         => 'Background colour',
		'allow_null'    => 1,
		'choices'       => array(
			'primary' => 'primary',
			'secondary' => 'secondary',
			'tertiary' => 'tertiary',
			'quaternary' => 'quaternary',
			'light' => 'light',
			'dark' => 'dark',
			'gradient-primary' => 'gradient-primary',
			'gradient-secondary' => 'gradient-secondary',
			'gradient-tertiary' => 'gradient-tertiary',
			'gradient-quaternary' => 'gradient-quaternary',
			'gradient-light' => 'gradient-light',
			'gradient-dark' => 'gradient-dark',
			'success' => 'success',
			'danger' => 'danger',
			'warning' => 'warning',
			'info' => 'info',
			'white' => 'white',
		),
		'default_value' => false,
		'return_format' => 'value',
	] );

	$g->addSelect( 'border_colour', [
		'label'         => 'Border colour',
		'allow_null'    => 1,
		'choices'       => array(
			'primary' => 'primary',
			'secondary' => 'secondary',
			'tertiary' => 'tertiary',
			'quaternary' => 'quaternary',
			'light' => 'light',
			'dark' => 'dark',
			'success' => 'success',
			'danger' => 'danger',
			'warning' => 'warning',
			'info' => 'info',
			'white' => 'white',
		),
		'default_value' => false,
		'return_format' => 'value',
	] );

	$g->addNumber( 'padding', [
		'label'         => 'Padding',
		'instructions'  => 'A value from 0 (none) to 5 (most)',
		'required'      => 0,
		'allow_null'    => 1,
		'default_value' => $paddingsize,
		'min'           => 0,
		'max'           => 5,
		'step'          => 1,
	] );

	$g->addText( 'additional_classes', [
		'label'         => 'Additional classes',
	] );

	$g->endGroup();
}

function _parsley_acf_heading ( $builder, $group_name='heading', $group_label='Heading', $default_level='h2' ) {

	$builder->addText( $group_name, [
		'label'         => $group_label,
	] );
	
	$g = $builder->addGroup( $group_name . '_level', [
		'label'  => $group_label . ' Style',
		'layout' => 'table',
	] )->conditional( $group_name, '!=empty', '' );

	$g->addSelect( 'real', [
		'label'         => 'Real tag',
		'choices'       => array(
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'div' => 'div',
			'none' => 'none',
		),
		'default_value' => $default_level,
		'allow_null'    => 0,
		'return_format' => 'value',
	] );

	$g->addSelect( 'visual', [
		'label'         => 'Displayed as',
		'choices'       => array(
			'h1' => 'h1',
			'h2' => 'h2',
			'h3' => 'h3',
			'h4' => 'h4',
			'h5' => 'h5',
			'h6' => 'h6',
			'd-none' => 'd-none',
		),
		'default_value' => false,
		'allow_null'    => 1,
		'return_format' => 'value',
	] )->conditional( 'real', '!=', 'none' );

	$g->addNumber( 'padding', [
		'label'         => 'Padding',
		'instructions'  => 'A value from 0 (none) to 5 (most)',
		'required'      => 0,
		'allow_null'    => 1,
		'default_value' => 3,
		'min'           => 0,
		'max'           => 5,
		'step'          => 1,
	] )->conditional( 'real', '!=', 'none' );
	
	$g->addText( 'additional_classes', [
		'label'         => 'Additional classes',
	] )->conditional( 'real', '!=', 'none' );

	$g->endGroup();
}

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

	$builder->addText('notes');

	if ( $callback ) {
		call_user_func( $callback, $builder );
	}

	if ( $opts['heading'] ) {

		$builder->addTab( 'Heading' );

		_parsley_acf_heading( $builder );

		if ( array_key_exists( 'heading_callback', $opts ) ) {
			call_user_func( $opts['heading_callback'], $builder );
		}
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
		_parsley_acf_style( $builder, 'style', 'Style', 3 );
	}
	
	if ( array_key_exists( 'options_callback', $opts ) ) {
		call_user_func( $opts['options_callback'], $builder );
	}

	return $builder;
}

function parsley_acf_column_definition ( $builder, $opts=array(), $callback=false ) {
	
	if ( $callback ) {
		call_user_func( $callback, $builder );
	}

	$builder->addTab( 'Column Options' );
	
	$g = $builder->addGroup( 'options', [
		'label'         => 'Basic Options',
		'layout'        => 'row',
	] );

	$g->addText( 'classes', [
		'label'         => 'Classes',
		'instructions'  => 'Classes to apply to the column; requires knowledge of the Bootstrap grid system.',
		'default_value' => 'col',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
	] );

	$g->addTrueFalse( 'exact_html', [
		'label'         => 'Exact HTML',
		'instructions'  => 'If exact HTML, will avoid Wordpress paragraph munging.',
		'default_value' => 0,
		'ui'            => 1,
		'ui_on_text'    => 'Exact',
		'ui_off_text'   => 'Munge',
		'wrapper'       => [ 'width' => '25', 'class' => '', 'id' => '' ],
	] );

	$g->endGroup();

	if ( array_key_exists( 'options_callback', $opts ) ) {
		call_user_func( $opts['options_callback'], $builder );
	}
	
	return $builder;
}

$SEC = [];

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
	new FieldsBuilder( 'primary_content', [
		'label'   => 'Primary Content',
		'display' => 'block',
	] )
);

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'post', [
		'label'   => 'Another Post',
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
	new FieldsBuilder( 'columns2', [
		'label'   => 'Advanced Columns',
		'display' => 'block',
	] ),
	[
		'heading_callback' => function ( $builder ) {
			
			$builder->addTrueFalse( 'heading_in_column', [
				'label'         => 'Heading in First Column',
				'instructions'  => 'Inserts the heading into the first column instead of before the columns.',
				'ui'            => 1,
				'ui_on_text'    => 'Yes',
				'ui_off_text'   => 'No',
			] )->conditional( 'heading', '!=empty', '' );
			
		},
	],
	function ( $builder ) {

		$builder->addTab( 'Columns' );

		$f = $builder->addFlexibleContent( 'columns', [
			'label'         => 'Columns',
			'required'      => 1,
			'min'           => 1,
			'max'           => 120,
			'button_label'  => 'Add Column',
		] );

		$f->addLayout( parsley_acf_column_definition(
			new FieldsBuilder( 'col_html', [
				'label'   => 'HTML',
				'display' => 'block',
			] ),
			[ ],
			function ( $builder ) {
				$builder->addTab( 'Content' );
				$builder->addWysiwyg( 'content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
			}
		) );

		$f->addLayout( parsley_acf_column_definition(
			new FieldsBuilder( 'col_image', [
				'label'   => 'Image',
				'display' => 'block',
			] ),
			[ ],
			function ( $builder ) {
				$builder->addTab( 'Image' );
				$builder->addImage( 'image', [
					'label'         => 'Image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'required'      => 1,
				] );
				$builder->addTrueFalse( 'rounded', [
					'label'         => 'Rounded corners',
					'ui'            => 1,
					'ui_on_text'    => 'Yes',
					'ui_off_text'   => 'No',
				] );
				$builder->addTrueFalse( 'shadow', [
					'label'         => 'Shadow',
					'ui'            => 1,
					'ui_on_text'    => 'Yes',
					'ui_off_text'   => 'No',
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
		) );
		
		$f->addLayout( parsley_acf_column_definition(
			new FieldsBuilder( 'col_card', [
				'label'   => 'Card',
				'display' => 'block',
			] ),
			[
				'options_callback' => function ( $builder ) {
					$builder->addSelect( 'card_border_colour', [
						'label'         => 'Card border colour',
						'allow_null'    => 1,
						'choices'       => array(
							'primary' => 'primary',
							'secondary' => 'secondary',
							'tertiary' => 'tertiary',
							'quaternary' => 'quaternary',
							'light' => 'light',
							'dark' => 'dark',
							'success' => 'success',
							'danger' => 'danger',
							'warning' => 'warning',
							'info' => 'info',
							'white' => 'white',
						),
						'default_value' => false,
						'return_format' => 'value',
					] );
					$builder->addText( 'card_id', [
						'label'         => 'ID',
					] );
					$builder->addText( 'card_class', [
						'label'         => 'Additional classes',
					] );
				}
			],
			function ( $builder ) {
				$builder->addTab( 'Header' );
				_parsley_acf_style( $builder, 'header_style' );
				_parsley_acf_heading( $builder, 'header_title', 'Header Title', 'h3' );
				$builder->addWysiwyg( 'header_content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
				$builder->addTab( 'Body' );
				_parsley_acf_style( $builder, 'body_style' );
				$builder->addWysiwyg( 'body_content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
				$builder->addTab( 'Footer' );
				_parsley_acf_style( $builder, 'footer_style' );
				$builder->addWysiwyg( 'footer_content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
			}
		) );

		$f->addLayout( parsley_acf_column_definition(
			new FieldsBuilder( 'col_listg', [
				'label'   => 'List Group',
				'display' => 'block',
			] ),
			[
				'options_callback' => function ( $builder ) {
					_parsley_acf_style( $builder, 'lg_style' );
					$builder->addText( 'lg_item_class', [
						'label'         => 'Additional item classes',
					] );
				}
			],
			function ( $builder ) {
				$builder->addTab( 'List Items' );
				$r = $builder->addRepeater( 'item', [
					'label'            => 'Items',
					'required'         => 1,
					'min'              => 1,
					'max'              => 0,
					'layout'           => 'table',
					'button_label'     => 'Add Item',					
				] );
				$r->addText('html', [ 'wrapper' => [ 'width' => '50', 'class' => '', 'id' => '' ] ]);
				$r->addSelect( 'nugget', [
					'allow_null'    => 1,
					'choices'       => array(
						'fa-check' => 'tick',
						'fa-times' => 'cross',
						'icon'     => 'icon',
						'text'     => 'text',
					),
					'default_value' => false,
					'return_format' => 'value',					
				]);
				$r->addText('nugget_detail')->conditional('nugget', '==', 'icon')->or('nugget', '==', 'text');
				$r->addText('class', [ 'wrapper' => [ 'width' => '25', 'class' => '', 'id' => '' ] ]);
				$r->endRepeater();
			}
		) );
		
		$f->addLayout( parsley_acf_column_definition(
			new FieldsBuilder( 'col_listgcard', [
				'label'   => 'List Group + Card',
				'display' => 'block',
			] ),
			[
				'options_callback' => function ( $builder ) {
					_parsley_acf_style( $builder, 'lg_style' );
					$builder->addText( 'lg_item_class', [
						'label'         => 'Additional item classes',
					] );
					$builder->addSelect( 'card_border_colour', [
						'label'         => 'Card border colour',
						'allow_null'    => 1,
						'choices'       => array(
							'primary' => 'primary',
							'secondary' => 'secondary',
							'tertiary' => 'tertiary',
							'quaternary' => 'quaternary',
							'light' => 'light',
							'dark' => 'dark',
							'success' => 'success',
							'danger' => 'danger',
							'warning' => 'warning',
							'info' => 'info',
							'white' => 'white',
						),
						'default_value' => false,
						'return_format' => 'value',
					] );
					$builder->addText( 'card_id', [
						'label'         => 'ID',
					] );
					$builder->addText( 'card_class', [
						'label'         => 'Additional classes',
					] );
				}
			],
			function ( $builder ) {
				$builder->addTab( 'Header' );
				_parsley_acf_style( $builder, 'header_style' );
				_parsley_acf_heading( $builder, 'header_title', 'Header Title', 'h3' );
				$builder->addWysiwyg( 'header_content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
				$builder->addTab( 'List Items' );
				$r = $builder->addRepeater( 'item', [
					'label'            => 'Items',
					'required'         => 1,
					'min'              => 1,
					'max'              => 0,
					'layout'           => 'table',
					'button_label'     => 'Add Item',					
				] );
				$r->addText('html', [ 'wrapper' => [ 'width' => '50', 'class' => '', 'id' => '' ] ]);
				$r->addSelect( 'nugget', [
					'allow_null'    => 1,
					'choices'       => array(
						'fa-check' => 'tick',
						'fa-times' => 'cross',
						'icon'     => 'icon',
						'text'     => 'text',
					),
					'default_value' => false,
					'return_format' => 'value',					
				]);
				$r->addText('nugget_detail')->conditional('nugget', '==', 'icon')->or('nugget', '==', 'text');
				$r->addText('class', [ 'wrapper' => [ 'width' => '25', 'class' => '', 'id' => '' ] ]);
				$r->endRepeater();
				$builder->addTab( 'Footer' );
				_parsley_acf_style( $builder, 'footer_style' );
				$builder->addWysiwyg( 'footer_content', [
					'label'         => 'Content',
					'tabs'          => 'all',
					'toolbar'       => 'full',
					'media_upload'  => 1,
				]);
			}
		) );
		
		$f->addLayout( new FieldsBuilder( 'col_break', [
			'label'   => 'Row Break',
			'display' => 'block',
		] );

		$f->endFlexibleContent();

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

$SEC[] = parsley_acf_section_definition(
	new FieldsBuilder( 'columns', [
		'label'   => 'Columns [legacy]',
		'display' => 'block',
	] ),
	[
		'heading_callback' => function ( $builder ) {
			
			$builder->addTrueFalse( 'heading_in_column', [
				'label'         => 'Heading in First Column',
				'instructions'  => 'Inserts the heading into the first column instead of before the columns.',
				'ui'            => 1,
				'ui_on_text'    => 'Yes',
				'ui_off_text'   => 'No',
			] )->conditional( 'heading', '!=empty', '' );
			
		},
	],
	function ( $builder ) {

		$builder->addTab( 'Columns' );

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
