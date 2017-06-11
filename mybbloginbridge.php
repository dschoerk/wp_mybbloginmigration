<?php
/*
Plugin Name: Simple MYBB Login Bridge
Plugin URI: 
Description:  Uses MYBB Login data to register new users based on old accounts
Version: 0.0.1
Author: Dominik Schörkhuber
Author URI: schoerkhuber.net
*/

function mybb_salt_password($password, $salt)
{
	return md5(md5($salt).md5($password));
} 

function authenticate ($user, $username, $password) 
{
	global $wpdb;

	// If previous authentication succeeded, respect that
	if ( is_a($user, 'WP_User') ) 
	{ 
		return $user; 
	}

	$mybb_user = $wpdb->get_row( $wpdb->prepare("SELECT password, salt, email FROM mybb.mybb_users u WHERE username = %s", array($username)) );
	echo($username);
	if(!empty($mybb_user))
	{
		$saltedpwd = mybb_salt_password($password, $mybb_user->salt);
		if($saltedpwd == $mybb_user->password)
		{
			// success
			wp_create_user( $username, $password, $mybb_user->email );
		}
		else
		{
			// failed
		}
	}
	else
	{
		// failed
	}


	// https://codex.wordpress.org/Function_Reference/wp_create_user
	// wp_create_user( $username, $password, $email );
}

add_filter('authenticate', 'authenticate', 1, 3);


?>
