<?php
	namespace App\Components\Rater;

	use App\Components\Rater\services\RateService;

	/**
	 * Class Facade
	 * @package App\Components\Rater
	 */
	class Facade {

		/**
		 * @param int $limit
		 *
		 * @return mixed
		 */
		public static function getOldestRates($limit = 100) {
			return CSL::rateService()->getOldestRates($limit);
		}

		/**
		 * @param bool $hasForceUpdate
		 * @param bool $hasUseSleeping
		 *
		 * @throws \App\Components\PaymentProvider\exceptions\FiatCurrencyNameNotFound
		 * @throws \App\Exceptions\AbstractException
		 */
		public static function updateRates($hasForceUpdate = false, $hasUseSleeping = true) {
			CSL::rateService()->updateRates($hasForceUpdate, $hasUseSleeping);
		}

		/**
		 * @param string $tokenFrom
		 * @param string $tokenTo
		 * @param float $amount
		 *
		 * @return float
		 */
		public static function convert($tokenFrom, $tokenTo, $amount) {
			return CSL::rateService()->convert($tokenFrom, $tokenTo, doubleval($amount));
		}

		/**
		 * @param string $currencyFrom
		 * @param float  $amount
		 *
		 * @return float
		 */
		public static function convertToBase($amount, $currencyFrom = RateService::DEFAULT_CURRENCY) {
			if (is_null($amount)) {
				return null;
			}
			return CSL::rateService()->convertAmountToBase($currencyFrom, $amount);
		}

		/**
		 * @param string 		$currency
		 * @param null|string   $timestamp
		 *
		 * @return float
		 */
		public static function getCurrencyToBaseRate($currency = RateService::DEFAULT_CURRENCY, $timestamp = null) {
			return CSL::rateService()->getCurrencyToBaseRate($currency, $timestamp);
		}

		/**
		 * @param float       $amount
		 * @param string      $currencyIn
		 * @param null|string $timestamp
		 *
		 * @return float|int
		 */
		public static function convertToToken(float $amount, string $currencyIn = RateService::DEFAULT_CURRENCY, $timestamp = null) {
			return CSL::rateService()->convertToToken($currencyIn, $amount, $timestamp);
		}

		/**
		 * @return array
		 */
		public static function getCurrencies() {
			return CSL::rateService()->getAllCurrencies();
		}

		/**
		 * @param float     	$amount
		 * @param null|string	$currency
		 * @param null|string	$timestamp
		 *
		 * @return float
		 */
		public static function normalizeAmount($amount, $currency = null, $timestamp = null) {
			return CSL::rateService()->normalizeAmount($amount, $currency, $timestamp);
		}

		/**
		 * @param string     	$currency
		 * @param null|string	$timestamp
		 *
		 * @return float
		 */
		public static function normalizeRate($currency, $timestamp = null) {
			return CSL::rateService()->getCurrencyRate($currency, $timestamp);
		}

		/**
		 * @param string $currency
		 *
		 * @return bool
		 */
		public static function isDefaultCurrency($currency) {
			return $currency === RateService::DEFAULT_CURRENCY;
		}
	}