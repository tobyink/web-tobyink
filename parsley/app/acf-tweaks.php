<?php

if ( ! is_admin() ) {

# Only load on specific pages
#	acf_form_head();

	add_filter( 'acf/validate_form', function ($args) {
		if(!$args['html_before_fields'])
			$args['html_before_fields'] = '<div class="row">';
		if(!$args['html_after_fields'])
			$args['html_after_fields'] = '</div>';
		if($args['html_updated_message'] == '<div id="message" class="updated"><p>%s</p></div>')
			$args['html_updated_message'] = '<div id="message" class="updated alert alert-success">%s</div>';
		if($args['html_submit_button'] == '<input type="submit" class="acf-button button button-primary button-large" value="%s" />')
			$args['html_submit_button'] = '<input type="submit" class="acf-button button button-primary button-large btn btn-primary" value="%s" />';
		return $args;
	} );

	add_filter( 'acf/prepare_field', function ($field) {
		if(is_admin() && !wp_doing_ajax())
			return $field;
		$field['wrapper']['class'] .= ' form-group col-12';
		$field['class'] .= ' form-control';
		return $field;
	} );

	add_filter( 'acf/get_field_label', function ($label) {
		if(is_admin() && !wp_doing_ajax())
			return $label;
		$label = str_replace('<span class="acf-required">*</span>', '<span class="acf-required text-danger">*</span>', $label);
		return $label;
	} );
}
