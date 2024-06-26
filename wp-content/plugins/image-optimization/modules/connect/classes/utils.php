<?php

namespace ImageOptimization\Modules\Connect\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Utils
 */
class Utils {
	/**
	 * get_clients_url
	 * @return string
	 */
	public static function get_clients_url(): string {
		return Config::BASE_URL . '/api/v1/clients';
	}

	/**
	 * get_redirect_uri
	 * @return string
	 */
	public static function get_redirect_uri(): string {
		if ( false !== strpos( Config::ADMIN_PAGE, '?page=' ) ) {
			return admin_url( Config::ADMIN_PAGE );
		}
		return admin_url( 'options-general.php?page=' . Config::ADMIN_PAGE );
	}

	public static function get_auth_url(): string {
		return Config::BASE_URL . '/v1/oauth2/auth';
	}

	/**
	 * Get full authorization URL with all required parameters
	 *
	 * @param string $client_id
	 *
	 * @return string
	 */
	public static function get_authorize_url( string $client_id ): string {
		return add_query_arg( [
			'client_id' => $client_id,
			'redirect_uri' => rawurlencode( self::get_redirect_uri() ),
			'response_type' => 'code',
			'scope' => Config::SCOPES,
			'state' => wp_create_nonce( Config::STATE_NONCE ),
		], self::get_auth_url() );
	}

	/**
	 * get_deactivation_url
	 * @param string $client_id
	 *
	 * @return string
	 */
	public static function get_deactivation_url( string $client_id ): string {
		return Config::BASE_URL . "/api/v1/clients/{$client_id}/activation";
	}

	public static function get_jwks_url(): string {
		return Config::BASE_URL . '/v1/.well-known/jwks.json';
	}

	/**
	 * get_sessions_url
	 * @return string
	 */
	public static function get_sessions_url(): string {
		return Config::BASE_URL . '/api/v1/session';
	}

	public static function get_token_url(): string {
		return Config::BASE_URL . '/api/v1/oauth2/token';
	}
}
