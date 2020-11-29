<?php

/**
 * Plugin Name: Contact Form
 * Description: Super simple contact form.
 * Version: 1.1
 * Author: Toby Inkster
 * Author URI: http://toby.ink/
 */

function tobyink_contact_captcha_form_field () {
	$html .= '<div class="form-group">';
	$html .= '<div class="g-recaptcha" data-sitekey="' . get_option('tobyink_contact_recaptcha_key') . '"></div>';
	$html .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	$html .= '</div>';
	return $html;
}

function tobyink_contact_captcha_verify ( $data=null ) {

	if ( empty($data) ) {
		$data = $_POST;
	}
	
	if ( ! empty($data['g-recaptcha-response']) ) {
		$postdata = array(
			'secret'   => get_option('tobyink_contact_recaptcha_secret'),
			'response' => $_POST['g-recaptcha-response'],
		);
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify' );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($postdata) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		
		$captcha_result = json_decode( curl_exec($ch) );
		
		if ( $captcha_result->success ) {
			return true;
		}
	}
	
	return false;
}

function tobyink_contact_addresses () {
	$a_of_a = json_decode( get_option('tobyink_contact_addresses') );
	$rv = [];
	foreach ( $a_of_a as $a ) {
		$rv[ $a[0] ] = $a[1];
	}
	return $rv;
}

add_action( 'admin_menu', function () {
	add_options_page( 'Contact Form', 'Contact Form', 'manage_options', 'tobyink_contact', function () {
		echo '<h1>Contact Form Options</h1>';

		$keys = array(
			'tobyink_contact_recaptcha_key'       => 'ReCAPTCHA Key',
			'tobyink_contact_recaptcha_secret'    => 'ReCAPTCHA Secret',
			'tobyink_contact_addresses'           => 'Addresses (JSON)',
		);
		
		$form_html = '';
		
		foreach ( $keys as $key => $label ) {
			$val  = get_option( $key );
			$hval = htmlspecialchars( $val );
			
			if ( array_key_exists($key, $_POST) && stripslashes($_POST[$key]) != $val ) {
				$val  = stripslashes($_POST[$key]);
				$hval = htmlspecialchars( $val );
				update_option( $key, $val );
				echo "<div class=updated><p>Updated <strong>${label}</strong></p></div>\n";
			}
			
			$form_html .= "<p><label for=${key}>${label}</label><br>\n";
			
			if ( $key === 'tobyink_contact_addresses' ) {
				$form_html .= "<textarea name=${key} id=${key} cols=60 rows=8>${hval}</textarea></p>\n";
			}
			else {
				$form_html .= "<input type=text name=${key} id=${key} value=\"${hval}\" size=40></p>\n";
			}
		}
		
		echo "<form method=post action=''>\n";
		echo $form_html;
		echo "<p><input type=submit></p>\n";
		echo "</form>\n";
	} );
} );

add_shortcode( 'tobyink_contact', function ( $atts, $content=null ) {

	$addresses = tobyink_contact_addresses();

	$user = wp_get_current_user();
	
	if ( $user->ID > 0 ) {
	
		if ( ! array_key_exists( 'contact_name', $_REQUEST ) ) {
			$_REQUEST['contact_name'] = $user->display_name;
		}
		if ( ! array_key_exists( 'contact_email', $_REQUEST ) ) {
			$_REQUEST['contact_email'] = $user->user_email;
		}
	}

	$html = '';
	
	if ( $_REQUEST['tobyink_contact_action'] ) {
		
		$errors = array();
		
		if ( ! tobyink_contact_captcha_verify() ) {
			$errors[] = 'Please verify that you are human.';
		}
		if ( ! strlen(trim($_REQUEST['contact_name'])) > 1 ) {
			$errors[] = 'Please provide your name.';
		}
		if ( ! strlen(trim($_REQUEST['contact_email'])) > 5 ) {
			$errors[] = 'Please provide your email address for a reply.';
		}
		if ( ! strlen(trim($_REQUEST['contact_subject'])) > 0 ) {
			$errors[] = 'Please specify a subject.';
		}

		$destination = $addresses[ $_REQUEST['contact_category'] ];
		if ( empty($destination) ) {
			if ( count($addresses) === 1 ) {
				$vals = array_values($addresses);
				$destination = $vals[0];
			}
			else {
				$errors[] = 'Please choose a message type.';
			}
		}
		
		if ( count($errors) ) {
			foreach ( $errors as $e ) {
				$html .= sprintf( '<p class="alert alert-danger">%s</p>', htmlspecialchars($e) );
			}
		}
		else {
			$success = wp_mail(
				$destination,
				sprintf( '[web] %s',  $_REQUEST['contact_subject'] ),
				sprintf(
					"Name:    %s\r\n"
					."Email:   %s\r\n"
					."Subject: %s\r\n"
					."Message:\r\n"
					."%s\r\n",
					$_REQUEST['contact_name'],
					$_REQUEST['contact_email'],
					$_REQUEST['contact_subject'],
					$_REQUEST['contact_message']
				),
				sprintf( 'Reply-To: %s', $_REQUEST['contact_email'] )
			);
			if ( $success ) {
				$html .= '<p class="alert alert-success">Message sent!</p>';
			}
			else {
				$html .= '<p class="alert alert-danger">Unknown error sending mail.</p>';
			}
		}
	}
	
	$html .= '<form action="" method="post">';
	
	if ( count($addresses) > 1 ) {
		$html .= '<div class="form-group">';
		$html .= '<label for="contact_category">Message type</label>';
		$html .= '<select name="contact_category" id="contact_category" class="form-control">';
		foreach ( $addresses as $label => $adr ) {
			$html .= sprintf( '<option %s>%s</option>', ( $label==$_REQUEST['contact_category'] ? ' selected' : '' ), htmlspecialchars($label) );
		}
		$html .= '</select>';
		$html .= '</div>';
	}
	
	$html .= '<div class="row"><div class="col-md-6">';

	$html .= '<div class="form-group">';
	$html .= '<label for="contact_name">Your name</label>';
	$html .= '<input name="contact_name" id="contact_name" class="form-control" type="text" required value="'.htmlspecialchars($_REQUEST['contact_name']).'">';
	$html .= '</div>';

	$html .= '</div><div class="col-md-6">';
	
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_email">Your email</label>';
	$html .= '<input name="contact_email" id="contact_email" class="form-control" type="email" required value="'.htmlspecialchars($_REQUEST['contact_email']).'">';
	$html .= '</div>';
	
	$html .= '</div></div>';

	$html .= '<div class="form-group">';
	$html .= '<label for="contact_subject">Message subject</label>';
	$html .= '<input name="contact_subject" id="contact_subject" class="form-control" type="text" required value="'.htmlspecialchars($_REQUEST['contact_subject']).'">';
	$html .= '</div>';
	
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_message">Message</label>';
	$html .= '<textarea name="contact_message" id="contact_message" class="form-control" rows="8" cols="40">'.htmlspecialchars($_REQUEST['contact_message']).'</textarea>';
	$html .= '</div>';
	
	$html .= tobyink_contact_captcha_form_field();
	
	$html .= '<div class="form-group">';
	$html .= '<input type="submit" value="Send message" class="btn btn-primary">';
	$html .= '<input name="tobyink_contact_action" value=1 type=hidden>';
	$html .= '</div>';
	
	$html .= '</form>';
	
	return $html;

} );

