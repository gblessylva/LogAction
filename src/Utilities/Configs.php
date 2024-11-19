<?php
/**
 * Class containong all plugin config constants like version, api url, plugin name etc.
 *
 * @package logaction.
 * @author GBLESSYLVA <gblessylva@gmail.com>
 * @since 1.0.0.
 */

namespace LogAction\Utilities;

/**
 * Class for all contstants
 */
class Configs {
	// Application-specific constants.
	const VERSION     = '1.0.0';
	const TABLE_NAME  = 'logaction_logs';
	const PLUGIN_NAME = 'LogAction';


	/**
	 * Static property instead of a constant.
	 *
	 * @var $asset_url
	 */
	public static $asset_url;

	/**
	 * Full table name with prefix.
	 *
	 * @var string
	 */
	public static $logaction_table;
	/**
	 * Initialize the static property in a static method.
	 */
	public static function init() {
		global $wpdb;
		self::$asset_url = plugin_dir_url( __DIR__ ) . '../assets/';

		// Set full table name with wpdb prefix.
		self::$logaction_table = $wpdb->prefix . self::TABLE_NAME;
	}
	/**
	 * Method to retrieve all constants as an array, if needed
	 *
	 * @return new
	 */
	public static function all() {
		return ( new \ReflectionClass( __CLASS__ ) )->getConstants();
	}
}

Configs::init();
