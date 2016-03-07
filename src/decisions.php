<?php

namespace JPB\Decisions;

use WP_Error;

/**
 * @internal
 */
function setup() {
	default_constants();
	$parsed = parse_contents( get_contents() );
	if ( is_wp_error( $parsed ) ) {
		do_action( 'jpb_decisions_error', $parsed );
	}
	$options = get_options( (array) $parsed );
	array_walk( $options, 'JPB\Decisions\filter_option' );
	array_walk( $options, 'JPB\Decisions\filter_network_option' );
}

/**
 * Inject our options
 *
 * @param mixed $value
 * @param string $name
 */
function filter_option( $value, $name ) {
	add_filter( "pre_option_$name", function () use ( $value ) {
		return maybe_unserialize( $value );
	} );
}

/**
 * Inject our options
 *
 * @param mixed $value
 * @param string $name
 */
function filter_network_option( $value, $name ) {
	add_filter( "pre_site_option_$name", function () use ( $value ) {
		return maybe_unserialize( $value );
	} );
}

/**
 * @internal
 */
function default_constants() {
	if ( ! defined( 'JPB\Decisions\DECISIONS' ) ) {
		define( 'JPB\Decisions\DECISIONS', \ABSPATH . '.decisions.json' );
	}
}

/**
 * @internal
 *
 * @return string|false
 */
function get_contents() {
	if ( ! file_exists( DECISIONS ) ) {
		return false;
	}

	return file_get_contents( DECISIONS );
}

/**
 * @internal
 *
 * @param string $contents
 *
 * @return mixed|WP_Error
 */
function parse_contents( $contents ) {
	if ( ! $contents ) {
		return new WP_Error();
	}
	$parsed = json_decode( $contents, true );
	if ( ( $code = json_last_error() ) ) {
		return new WP_Error( $code, json_last_error_msg() );
	}

	return $parsed;
}

/**
 * @internal
 *
 * @param array $parsed
 *
 * @return array
 */
function get_options( array $parsed ) {
	$default = get_key( $parsed, 'standard' );
	if ( is_multisite() ) {
		$current_site     = get_current_site();
		$current_site_key = str_replace(
			array( 'https://', 'http://' ),
			'',
			trim( "{$current_site->domain}{$current_site->path}", '/' )
		);

		$site = get_key( $parsed, 'sites.' . $current_site_key );
		if ( is_array( $site ) ) {
			$default = array_merge( $default, $site );
		}
	}

	return (array) $default;
}

/**
 * @internal
 *
 * @param array $parsed
 *
 * @return array
 */
function get_network_options( array $parsed ) {
	if ( ! is_multisite() ) {
		return array();
	}

	return (array) get_key( $parsed, 'network' );
}

/**
 * Get a value from an array
 *
 * @param array $from
 * @param string $key
 *
 * @return mixed
 */
function get_key( array $from, $key ) {
	$key = explode( '.', $key, 2 ) + array( '', '' );
	if ( ! $key[0] || ! key_exists( $key[0], $from ) ) {
		return null;
	}
	if ( ! $key[1] ) {
		return $from[ $key[0] ];
	}

	return get_key( (array) $from[ $key[0] ], $key[1] );
}
