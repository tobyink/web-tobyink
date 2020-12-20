<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment', 'embed'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    collect(['get_header', 'wp_head'])->each(function ($tag) {
        ob_start();
        do_action($tag);
        $output = ob_get_clean();
        remove_all_actions($tag);
        add_action($tag, function () use ($output) {
            echo $output;
        });
    });
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Render comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );

    $data = collect(get_body_class())->reduce(function ($data, $class) use ($comments_template) {
        return apply_filters("sage/template/{$class}/data", $data, $comments_template);
    }, []);

    $theme_template = locate_template(["views/{$comments_template}", $comments_template]);

    if ($theme_template) {
        echo template($theme_template, $data);
        return get_stylesheet_directory().'/index.php';
    }

    return $comments_template;
}, 100);

add_filter('wp_default_editor', create_function('', 'return "html";'));

remove_filter( 'the_content', 'wpautop' );

add_filter( 'the_content', function ( $content ) {
  if ( get_field( 'disable_wpautop', \App\acf_page_id() ) ) {
    return $content;
  }  
  return wpautop($content);
}, 99 );

add_filter('sage/display_sidebar', function ($display) {
    static $display;

    if ( function_exists('WC') ) {
      isset($display) || $display =
        is_active_sidebar('sidebar-primary')
        && !( is_cart() || is_checkout() || is_wc_endpoint_url() || is_account_page() );
    }
    else {
      isset($display) || $display =
        is_active_sidebar('sidebar-primary');
    }

    return $display;
});

add_filter( 'woocommerce_form_field_args', function ( $args, $key, $value = null ) {

	/* This is not meant to be here, but it serves as a reference
	of what is possible to be changed.

	$defaults = array(
		'type'			  => 'text',
		'label'			 => '',
		'description'	   => '',
		'placeholder'	   => '',
		'maxlength'		 => false,
		'required'		  => false,
		'id'				=> $key,
		'class'			 => array(),
		'label_class'	   => array(),
		'input_class'	   => array(),
		'return'			=> false,
		'options'		   => array(),
		'custom_attributes' => array(),
		'validate'		  => array(),
		'default'		   => '',
	); */

	// Start field type switch case
	switch ( $args['type'] ) {

		case "select" :  /* Targets all select input type elements, except the country and state select input types */
			$args['class'][] = 'form-group'; // Add a class to the field's html element wrapper - woocommerce input types (fields) are often wrapped within a <p></p> tag
			$args['input_class'] = array('form-control'); // Add a class to the form input itself
			//$args['custom_attributes']['data-plugin'] = 'select2';
			$args['label_class'] = array('control-label');
			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  ); // Add custom data attributes to the form input itself
		break;

		case 'country' : /* By default WooCommerce will populate a select with the country names - $args defined for this specific input type targets only the country select element */
			$args['class'][] = 'form-group single-country';
			$args['label_class'] = array('control-label');
		break;

		case "state" : /* By default WooCommerce will populate a select with state names - $args defined for this specific input type targets only the country select element */
			$args['class'][] = 'form-group'; // Add class to the field's html element wrapper
			$args['input_class'] = array('form-control'); // add class to the form input itself
			//$args['custom_attributes']['data-plugin'] = 'select2';
			$args['label_class'] = array('control-label');
			$args['custom_attributes'] = array( 'data-plugin' => 'select2', 'data-allow-clear' => 'true', 'aria-hidden' => 'true',  );
		break;


		case "password" :
		case "text" :
		case "email" :
		case "tel" :
		case "number" :
			$args['class'][] = 'form-group';
			//$args['input_class'][] = 'form-control input-lg'; // will return an array of classes, the same as bellow
			$args['input_class'] = array('form-control');
			$args['label_class'] = array('control-label');
		break;

		case 'textarea' :
			$args['input_class'] = array('form-control');
			$args['label_class'] = array('control-label');
		break;

		case 'checkbox' :
		break;

		case 'radio' :
		break;

		default :
			$args['class'][] = 'form-group';
			$args['input_class'] = array('form-control', 'input-lg');
			$args['label_class'] = array('control-label');
		break;
	}

	return $args;
}, 10, 3);

remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
