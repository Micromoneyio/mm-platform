<?php

	namespace App\Components\Rater;

	use App\Components\Rater\services\RateService;


	/**
	 * Class CSL
	 * @package App\Components\Rater
	 */
	class CSL {

		/**
		 * @var array
		 */
		private static $_container = [];

		/**
		 * @return RateService
		 */
		public static function rateService() {
			if (!array_key_exists('rateService', self::$_container)) {
				self::$_container['rateService'] = resolve('App\Components\Rater\services\RateService');
			}
			return self::$_container['rateService'];
		}
	}