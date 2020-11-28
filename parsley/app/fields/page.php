<?php

namespace App;

use StoutLogic\AcfBuilder\FieldsBuilder;

$page = new FieldsBuilder( 'page_options', [ 'position' => 'side' ] );

$page
    ->setLocation('post_type', '==', 'post')
    ->or('post_type', '==', 'page');

$page
    ->addTrueFalse('hide_title', [
        'label' => 'Hide Title',
        'ui' => 1,
        'ui_on_text' => 'Hidden',
        'ui_off_text' => 'Normal',
    ]);

$page
    ->addTrueFalse('hide_footer', [
        'label' => 'Hide Footer',
        'ui' => 1,
        'ui_on_text' => 'Hidden',
        'ui_off_text' => 'Normal',
    ]);

$page
    ->addTrueFalse('hide_breadcrumbs', [
        'label' => 'Hide Breadcrumbs',
        'ui' => 1,
        'ui_on_text' => 'Hidden',
        'ui_off_text' => 'Normal',
    ]);

acf_add_local_field_group( $page->build() );

$page = new FieldsBuilder( 'page_customizations', [ 'style' => 'seamless' ] );

$page
    ->setLocation('post_type', '==', 'post')
    ->or('post_type', '==', 'page');

$page
    ->addTextarea('custom_javascript', [
        'label' => 'Custom Javascript',
    ]);

$page
    ->addTextarea('custom_css', [
        'label' => 'Custom CSS',
    ]);

$page
    ->addImage('fullwidth_banner', [
        'label' => 'Banner Image',
        'return_format' => 'array',
    ]);

$page
    ->addTextarea('administrator_notes', [
        'label' => 'Admin Notes',
    ]);

acf_add_local_field_group( $page->build() );
