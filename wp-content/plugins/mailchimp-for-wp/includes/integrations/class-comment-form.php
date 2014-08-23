<?php

if( ! defined("MC4WP_LITE_VERSION") ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP_Comment_Form_Integration extends MC4WP_Integration {
	
	public function __construct() {
		// hooks for outputting the checkbox
		add_action( 'thesis_hook_after_comment_box', array( $this, 'output_checkbox' ), 10 );
		add_action( 'comment_form', array( $this, 'output_checkbox' ), 10 );

		// hooks for checking if we should subscribe the commenter
		add_action( 'comment_post', array( $this, 'subscribe_from_comment' ), 40, 2 );
	}

	/**
	* Outputs the checkbox for the comment form
	*/
	public function output_checkbox( $hook = '' ) {
		return parent::output_checkbox( 'comment_form' );
	}

	/**
	* Grabs data from WP Comment Form
	*
	* @param int $comment_ID
	* @param string $comment_approved 
	*/
	public function subscribe_from_comment( $comment_ID, $comment_approved = '' ) {

		// was sign-up checkbox checked?
		if ( $this->checkbox_was_checked() === false ) { 
			return false; 
		}

		// is this a spam comment?
		if ( $comment_approved === 'spam' ) { 
			return false; 
		}

		$comment = get_comment( $comment_ID );

		$email = $comment->comment_author_email;
		$merge_vars = array(
			'NAME' => $comment->comment_author,
			'OPTIN_IP' => $comment->comment_author_IP
		);

		return $this->subscribe( $email, $merge_vars, 'comment' );
	}
}