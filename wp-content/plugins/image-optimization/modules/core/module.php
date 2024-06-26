<?php
namespace ImageOptimization\Modules\Core;

// TODO: Remove imports that are not used.
use ImageOptimization\Modules\Oauth\{
	Classes\Data,
	Components\Connect,
	Rest\Activate,
	Rest\Connect_Init,
	Rest\Deactivate,
	Rest\Disconnect,
	Rest\Get_Subscriptions,
};

use ImageOptimization\Modules\Optimization\{
	Rest\Cancel_Bulk_Optimization,
	Rest\Optimize_Bulk,
};
use ImageOptimization\Modules\Backups\Rest\{
	Restore_All,
	Remove_Backups,
};
use ImageOptimization\Classes\{
	Module_Base,
	Utils,
};

use ImageOptimization\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Module_Base {
	public function get_name(): string {
		return 'core';
	}

	public static function component_list() : array {
		return [
			'Pointers',
			'Conflicts',
			'User_Feedback',
			'Not_Connected',
		];
	}

	private function render_top_bar() {
		?>
		<div id="image-optimization-top-bar"></div>
		<?php
	}

	private function render_app() {
		?>
		<div class="clear"></div>
		<div id="image-optimization-app"></div>
		<?php
	}

	public function maybe_add_quota_reached_notice() {

		// @var ImageOptimizer/Modules/ConnectManager/Module
		$module = Plugin::instance()->modules_manager->get_modules( 'connect-manager' );

		if ( ! $module->connect_instance->is_activated() || $module->connect_instance->images_left() > 0 ) {
			return;
		}

		?>
		<div class="notice notice-warning notice image-optimizer__notice image-optimizer__notice--warning">
			<p>
				<b>
					<?php esc_html_e(
						'You’ve reached your plan quota.',
						'image-optimization'
					); ?>
				</b>

				<span>
					<?php esc_html_e(
						'You have no images left to optimize in your current plan.',
						'image-optimization'
					); ?>

					<a href="https://go.elementor.com/io-panel-upgrade/">
						<?php esc_html_e(
							'Upgrade plan now',
							'image-optimization'
						); ?>
					</a>
				</span>
			</p>
		</div>
		<?php
	}

	public function add_plugin_links( $links, $plugin_file_name ): array {
		if ( ! str_ends_with( $plugin_file_name, '/image-optimization.php' ) ) {
			return (array) $links;
		}

		$custom_links = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=' . \ImageOptimization\Modules\Settings\Module::SETTING_BASE_SLUG ),
				esc_html__( 'Settings', 'image-optimization' )
			),
			'upgrade' => sprintf(
				'<a href="%s" style="color: #524CFF; font-weight: 700;" target="_blank" rel="noopener noreferrer">%s</a>',
				'https://go.elementor.com/io-panel-upgrade/',
				esc_html__( 'Upgrade', 'image-optimization' )
			),
		];

		return array_merge( $custom_links, $links );
	}

	public function enqueue_global_assets() {
		wp_enqueue_style(
			'image-optimization-admin-fonts',
			'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
			[],
			IMAGE_OPTIMIZATION_VERSION
		);

		wp_enqueue_style(
			'image-optimization-core-style-admin',
			$this->get_css_assets_url( 'style-admin', 'assets/build/' ),
			[],
			IMAGE_OPTIMIZATION_VERSION,
		);
	}

	/**
	 * Enqueue styles and scripts
	 */
	private function enqueue_scripts() {
		$asset_file = require IMAGE_OPTIMIZATION_ASSETS_PATH . 'build/admin.asset.php';

		foreach ( $asset_file['dependencies'] as $style ) {
			wp_enqueue_style( $style );
		}

		wp_enqueue_script(
			'image-optimization-admin',
			$this->get_js_assets_url( 'admin' ),
			array_merge( $asset_file['dependencies'], [ 'wp-util' ] ),
			$asset_file['version'],
			true
		);

		wp_localize_script(
			'image-optimization-admin',
			'imageOptimizerAppSettings',
			[
				'siteUrl' => wp_parse_url( get_site_url(), PHP_URL_HOST ),
				'thumbnailSizes' => wp_get_registered_image_subsizes(),
				'isDevelopment' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			]
		);

		/**
		 * @var ImageOptimizer\Modules\ConnectManager\Module $module
		 */
		$module = Plugin::instance()->modules_manager->get_modules( 'connect-manager' );
		$is_connect_on_fly = $module->connect_instance->get_is_connect_on_fly();
		$connect_email = $module->connect_instance->get_connect_data()['user']['email'] ?? null;

		wp_localize_script(
			'image-optimization-admin',
			'imageOptimizerUserData',
			[
				'isConnectOnFly' => $is_connect_on_fly,
				'isConnected' => $module->connect_instance->is_connected(),
				'isActivated' => $module->connect_instance->is_activated(),
				'planData' => $module->connect_instance->is_activated() ? $module->connect_instance->get_connect_status() : null,
				'licenseKey' => $module->connect_instance->is_activated() ? $module->connect_instance->get_activation_state() : null,
				'imagesLeft' => $module->connect_instance->is_activated() ? $module->connect_instance->images_left() : null,
				'isOwner' => $module->connect_instance->is_connected() ? $module->connect_instance->user_is_subscription_owner() : null,
				'subscriptionEmail' => $connect_email ? $connect_email : null,

				'wpRestNonce' => wp_create_nonce( 'wp_rest' ),
				'disconnect' => wp_create_nonce( 'wp_rest' ),
				'authInitNonce' => wp_create_nonce( $module->connect_instance->connect_init_nonce() ),
				'authDisconnectNonce' => wp_create_nonce( $module->connect_instance->disconnect_nonce() ),
				'authDeactivateNonce' => wp_create_nonce( $module->connect_instance->deactivate_nonce() ),
				'authGetSubscriptionsNonce' => wp_create_nonce( $module->connect_instance->get_subscriptions_nonce() ),
				'authActivateNonce' => wp_create_nonce( $module->connect_instance->activate_nonce() ),
				'versionNonce' => wp_create_nonce( $module->connect_instance->version_nonce() ),
				'removeBackupsNonce' => wp_create_nonce( Remove_Backups::NONCE_NAME ),
				'restoreAllImagesNonce' => wp_create_nonce( Restore_All::NONCE_NAME ),
				'optimizeBulkNonce' => wp_create_nonce( Optimize_Bulk::NONCE_NAME ),
				'cancelBulkOptimizationNonce' => wp_create_nonce( Cancel_Bulk_Optimization::NONCE_NAME ),
			]
		);

		wp_set_script_translations( 'image-optimization-admin', 'image-optimization' );
	}

	private function should_render(): bool {
		return ( Utils::is_media_page() || Utils::is_plugin_page() ) && Utils::user_is_admin();
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		$this->register_components();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_global_assets' ] );
		add_filter( 'plugin_action_links', [ $this, 'add_plugin_links' ], 10, 2 );

		add_action('current_screen', function () {
			if ( ! $this->should_render() ) {
				return;
			}

			add_action( 'admin_notices', [ $this, 'maybe_add_quota_reached_notice' ] );

			if ( Utils::is_media_page() ) {
				add_action('in_admin_header', function () {
					$this->render_top_bar();
				});

				add_action('all_admin_notices', function () {
					$this->render_app();
				});
			}

			if ( Utils::is_plugin_page() ) {
				add_action('in_admin_header', function () {
					$this->render_top_bar();
				});
			}

			add_action('admin_enqueue_scripts', function () {
				$this->enqueue_scripts();
			});
		});
	}
}
