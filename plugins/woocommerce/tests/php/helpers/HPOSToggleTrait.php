<?php

namespace Automattic\WooCommerce\RestApi\UnitTests;

use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\RestApi\UnitTests\Helpers\OrderHelper;

/**
 * Trait HPOSToggleTrait.
 *
 * Provides methods to toggle the HPOS feature on and off.
 */
trait HPOSToggleTrait {

	/**
	 * Call in setUp to enable COT/HPOS.
	 *
	 * @return void
	 */
	public function setup_cot() {
		// Remove the Test Suite’s use of temporary tables https://wordpress.stackexchange.com/a/220308.
		remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
		remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );
		OrderHelper::delete_order_custom_tables();
		OrderHelper::create_order_custom_table_if_not_exist();

		$this->toggle_cot( true );
	}

	/**
	 * Call in teardown to disable COT/HPOS.
	 */
	public function clean_up_cot_setup(): void {
		$this->toggle_cot( false );

		// Add back removed filter.
		add_filter( 'query', array( $this, '_create_temporary_tables' ) );
		add_filter( 'query', array( $this, '_drop_temporary_tables' ) );
	}

	/**
	 * Enables or disables the custom orders table across WP temporarily.
	 *
	 * @param boolean $enabled TRUE to enable COT or FALSE to disable.
	 * @return void
	 */
	private function toggle_cot( bool $enabled ): void {
		OrderHelper::toggle_cot( $enabled );
	}

	/**
	 * Helper function to enable COT <> Posts sync.
	 */
	private function enable_cot_sync() {
		$hook_name = 'pre_option_' . DataSynchronizer::ORDERS_DATA_SYNC_ENABLED_OPTION;
		remove_all_actions( $hook_name );
		add_filter(
			$hook_name,
			function () {
				return 'yes';
			}
		);
	}

	/**
	 * Helper function to disable COT <> Posts sync.
	 */
	private function disable_cot_sync() {
		$hook_name = 'pre_option_' . DataSynchronizer::ORDERS_DATA_SYNC_ENABLED_OPTION;
		remove_all_actions( $hook_name );
		add_filter(
			$hook_name,
			function () {
				return 'no';
			}
		);
	}
}
