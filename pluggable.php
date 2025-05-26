<?php

if (!defined('ABSPATH')){
	die('-1');
};

if ( ! function_exists( 'wp_check_password' ) ) :
	/**
	 * Checks the plaintext password against the encrypted Password.
	 *
	 * Maintains compatibility between old version and the new cookie authentication
	 * protocol using PHPass library. The $hash parameter is the encrypted password
	 * and the function compares the plain text password when encrypted similarly
	 * against the already encrypted password to see if they match.
	 *
	 * For integration with other applications, this function can be overwritten to
	 * instead use another package password checking algorithm.
	 *
	 * @since CP-2.2.0
	 *
	 * @global PasswordHash $wp_hasher PHPass object used for checking the password
	 *                                 against the $hash + $password.
	 * @uses PasswordHash::CheckPassword
	 *
	 * @param string     $password Plaintext user's password.
	 * @param string     $hash     Hash of the user's password to check against.
	 * @param string|int $user_id  Optional. User ID.
	 * @return bool False, if the $password does not match the hashed password.
	 */
	function wp_check_password( $password, $hash, $user_id = '' ) {
		global $wp_hasher;

		if ( ! empty( $wp_hasher ) ) {
			// Check the password using the overridden hasher.
			return $wp_hasher->CheckPassword( $password, $hash );
		}

		$check = false;

		if ( strlen( $password ) > 4096 ) {
			return $check;
		}

		/*
		 * Function cp_hash_password_options() is documented in wp-includes/user.php
		 */
		$options = cp_hash_password_options();

		/**
		 * Filter used to pepper the password.
		 *
		 * For maximum security, pepper should be stored in a file and not in the database.
		 *
		 * @since CP-2.2.0
		 *
		 * @param  string  String to be used as pepper.
		 */
		$pepper = apply_filters( 'cp_pepper_password', '' );
		if ( ! empty( $pepper ) ) {
			$maybe_peppered_password = hash_hmac( 'sha256', $password, $pepper );
		} else {
			$maybe_peppered_password = $password;
		}

		if ( password_verify( $maybe_peppered_password, $hash ) ) {
			// Handle password verification using PHP's PASSWORD_DEFAULT hashing algorithm.
			$check = true;
		} elseif ( md5( $maybe_peppered_password ) === $hash ) {
			// Handle password verification when a temporary password has been set via Adminer or phpMyAdmin.
			$check = true;
		} elseif ( str_starts_with( $hash, '$wp' ) ) {
			// Handle password from WordPress 6.8 and above
			$password_to_verify = base64_encode( hash_hmac( 'sha384', $maybe_peppered_password, 'wp-sha384', true ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			$check              = password_verify( $password_to_verify, substr( $hash, 3 ) );
		} elseif ( str_starts_with( $hash, '$P$' ) ) {
			// Handle password verification by the traditional WordPress method.
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$check = ( new PasswordHash( 8, true ) )->CheckPassword( $maybe_peppered_password, $hash );
		} else {
			// Check the password using compat support for any non-prefixed hash.
			$check = password_verify( $maybe_peppered_password, $hash );
		}

		/**
		 * Filters whether the plaintext password matches the hashed password.
		 *
		 * @since 2.5.0
		 * @since 6.8.0 Passwords are now hashed with bcrypt by default.
		 *              Old passwords may still be hashed with phpass or md5.
		 *
		 * @param bool       $check    Whether the passwords match.
		 * @param string     $password The plaintext password.
		 * @param string     $hash     The hashed password.
		 * @param string|int $user_id  Optional ID of a user associated with the password.
		 *                             Can be empty.
		 */
		return apply_filters( 'check_password', $check, $password, $hash, $user_id );
	}
endif;

if ( ! function_exists( 'wp_password_needs_rehash' ) ) :
	/**
	 * Checks whether a password hash needs to be rehashed.
	 *
	 * Passwords are hashed with bcrypt using the default cost. A password hashed in a prior version
	 * of WordPress may still be hashed with phpass and will need to be rehashed. If the default cost
	 * or algorithm is changed in PHP or WordPress then a password hashed in a previous version will
	 * need to be rehashed.
	 *
	 * @since CP-2.3.0
	 *
	 * @global PasswordHash $wp_hasher phpass object.
	 *
	 * @param string $hash Hash of a password to check.
	 * @return bool Whether the hash needs to be rehashed.
	 */
	function wp_password_needs_rehash( $hash ) {
		global $wp_hasher;

		if ( ! empty( $wp_hasher ) ) {
			return false;
		}

		/** This filter is documented in wp-includes/pluggable.php */
		$algorithm = apply_filters( 'cp_password_algorithm', PASSWORD_DEFAULT );

		/*
		 * Function cp_hash_password_options() is documented in wp-includes/user.php
		 */
		$options = cp_hash_password_options();

		$needs_rehash  = password_needs_rehash( $hash, $algorithm, $options );

		/**
		 * Filters whether the password hash needs to be rehashed.
		 *
		 * @since 6.8.0
		 *
		 * @param bool       $needs_rehash Whether the password hash needs to be rehashed.
		 * @param string     $hash         The password hash.
		 */
		return apply_filters( 'password_needs_rehash', $needs_rehash, $hash );
	}
endif;


