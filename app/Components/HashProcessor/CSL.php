<?php
namespace App\Components\HashProcessor;

class CSL {

	/**
	 * Container for services
	 * @var array
	 */
	private static $_container = [];

	/**
	 * @return ChainSoProcessor
	 */
	public static function chainSoProcessor() {
		if (!array_key_exists('chainSoProcessor', self::$_container)) {
			self::$_container['chainSoProcessor'] = new ChainSoProcessor();
		}

		return self::$_container['chainSoProcessor'];
	}

	/**
	 * @return EngineService
	 */
	public static function engineService() {
		if (!array_key_exists('engineService', self::$_container)) {
			/** @var WalletService $walletService */
			$walletService = $api = resolve('App\Services\WalletService');
			self::$_container['engineService'] = new EngineService($walletService);
		}

		return self::$_container['engineService'];
	}

	/**
	 * @return EtherScanProcessor
	 */
	public static function etherScanProcessor() {
		if (!array_key_exists('etherScanProcessor', self::$_container)) {
			self::$_container['etherScanProcessor'] = new EtherScanProcessor();
		}

		return self::$_container['etherScanProcessor'];
	}

	/**
	 * @return BlockdozerProcessor
	 */
	public static function blockdozerProcessor() {
		if (!array_key_exists('blockdozerProcessor', self::$_container)) {
			self::$_container['blockdozerProcessor'] = new BlockdozerProcessor();
		}

		return self::$_container['blockdozerProcessor'];
	}
}