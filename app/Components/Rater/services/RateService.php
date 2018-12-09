<?php
	namespace App\Components\Rater\services;


	/**
	 * Class RateService
	 * @package App\Components\Rater\services
	 */
	class RateService {
		/**
		 * All database contains values in this currency.
		 * Please use this const for convert models values to
		 * other currencies and tokens. Rater component use this
		 * currency of the default.
		 */
		const DEFAULT_CURRENCY = 'USD';

		/**
		 * @var SettingsService
		 */
		private $_settingsService;

		/**
		 * @var Main
		 */
		private $_mainRepository;

		/**
		 * @var RateRepository
		 */
		private $_rateRepository;

		/**
		 * RateService constructor.
		 *
		 * @param Main            $mainRepository
		 * @param RateRepository  $rateRepository
		 * @param SettingsService $settingsService
		 */
		public function __construct(
			Main $mainRepository,
			RateRepository $rateRepository,
			SettingsService $settingsService
		) {
			$this->_mainRepository = $mainRepository;
			$this->_rateRepository = $rateRepository;
			$this->_settingsService = $settingsService;
		}

		/**
		 * @param int $limit
		 *
		 * @return mixed
		 */
		public function getOldestRates($limit = 100) {
			return $this->_rateRepository->loadOldestRate($limit, 'currency', 'rate');
		}

		/**
		 * @param string $currencyFrom
		 * @param string $currencyTo
		 * @param double $amount
		 *
		 * @return double
		 */
		public function convert($currencyFrom, $currencyTo, $amount) {
			$currencyTo = strtoupper($currencyTo);
			$currencyFrom = strtoupper($currencyFrom);

			if ($currencyTo === $currencyFrom) {
				return $amount;
			}

			$rateFrom = $this->getCurrencyRate($currencyFrom);
			$rateTo = $this->getCurrencyRate($currencyTo);

			return doubleval($amount * $rateFrom / $rateTo);
		}

		/**
		 * @param string      $currency
		 * @param string|null $timestamp
		 *
		 * @return float
		 */
		public function getCurrencyRate(string $currency, $timestamp = null) {
			/** @var string $sign */
			$sign = db_config('payments.token.sign', 'DEMO');
			if ($currency === $sign) {
				return $this->_settingsService->getCurrentTokenRate();
			}

			if ($timestamp === null) {
				$currencyRate = $this->_rateRepository->loadLatestRateByCurrency($currency)->rate;
			} else {
				$currencyRate = $this->_rateRepository->loadClosestRateByCurrency($currency, $timestamp)->rate;
			}

			return $currencyRate;

		}

		/**
		 * Update rates
		 *
		 * @param bool $hasForceUpdate
		 * @param bool $hasUseSleeping
		 *
		 * @throws AbstractException
		 * @throws \App\Components\PaymentProvider\exceptions\FiatCurrencyNameNotFound
		 */
		public function updateRates($hasForceUpdate, $hasUseSleeping = true) {

			// Check last update
			$lastRatesUpdate = Rate::where('created_at', '>', Carbon::now()
				->subMinutes(config('payments.coinpayments.rates_update_time', 30))
			)->count();

			$usd = $this->_rateRepository->loadLatestRateByCurrency(self::DEFAULT_CURRENCY);
			if ($usd === null) {
				$this->_rateRepository->storeNewRateByCurrency(self::DEFAULT_CURRENCY, 1);
			}

			if ($lastRatesUpdate && !$hasForceUpdate) {
				throw new AbstractException('Rates already updated.');
			}

			// At first we need bitcoin rate
			$btcToUsd = PaymentProvider::getBitCoinRate();
			$rates = PaymentProvider::getRates();
			if (!$rates) {
				throw new AbstractException('Can not get rates from CoinPayments.net');
			}

			foreach ($rates as $currency => $rate) {
				if ($rate->accepted != 1) {
					continue;
				}

				$this->_rateRepository->storeNewRateByCurrency($currency, $rate->rate_btc * $btcToUsd);
			}

			$currentCurrency = db_config('payments.token.base', self::DEFAULT_CURRENCY);
			$signs = array_keys(Rate::FIAT_CURRENCY_RATES);

			$signs = array_diff($signs, [self::DEFAULT_CURRENCY]);
			if ($currentCurrency !== self::DEFAULT_CURRENCY && in_array($currentCurrency, $signs)) {
				$value = $this->_signToUsd($currentCurrency, $btcToUsd);
				$this->_rateRepository->storeNewRateByCurrency($currentCurrency, $value);
				$signs = array_diff($signs, [$currentCurrency]);
			}

			foreach ($signs as $index => $sign) {
				$value = $this->_signToUsd($sign, $btcToUsd);
				$this->_rateRepository->storeNewRateByCurrency($sign, $value);
				if ($index % 3 === 0 && $hasUseSleeping) {
					sleep(2);
				}
			}

			event(new RatesUpdatedEvent());
		}

		public function convertAmountToBase($currencyIn, $amount, $timestamp = null) {
			if ($currencyIn === null) {
				$currencyIn = self::DEFAULT_CURRENCY;
			}
			$rate = $this->getCurrencyToBaseRate($currencyIn, $timestamp);
			return $amount * $rate;
		}

		/**
		 * Convert rate from currencyIn to currencyBase
		 *
		 * @param string     $currencyIn Currency name in UPPERCASE
		 * @param int|string $timestamp
		 *
		 * @return float
		 */
		public function getCurrencyToBaseRate($currencyIn, $timestamp = null) {
			$currencyIn = strtoupper($currencyIn);
			/** @var string $currencyBase */
			$currencyBase = db_config('payments.token.base', self::DEFAULT_CURRENCY);
			if ($currencyIn === $currencyBase) {
				return 1;
			}

			/** @var string $sign */
			$sign = db_config('payments.token.sign', 'DEMO');
			if ($currencyIn === $sign) {
				return $this->_settingsService->getCurrentTokenRate();
			}

			$currencyBaseRate = 1;
			if ($timestamp === null) {
				if ($currencyBase !== self::DEFAULT_CURRENCY) {
					$currencyBaseRate = $this->_rateRepository->loadLatestRateByCurrency($currencyBase)->rate;
				}

				$currencyInRate = $this->_rateRepository->loadLatestRateByCurrency($currencyIn)->rate;
			} else {
				if ($currencyBase !== self::DEFAULT_CURRENCY) {
					$currencyBaseRate = $this->_rateRepository->loadClosestRateByCurrency($currencyBase,
						$timestamp)->rate;
				}

				$currencyInRate = $this->_rateRepository->loadClosestRateByCurrency($currencyIn, $timestamp)->rate;
			}

			return floatval($currencyInRate) / floatval($currencyBaseRate);
		}

		public function convertToToken($currencyIn, $amount, $timestamp = null) {
			$rateFromDefault = $this->_settingsService->getCurrentTokenRate();
			$amount = $this->convertAmountToBase($currencyIn, $amount, $timestamp);
			$rate = $this->getCurrencyToBaseRate(self::DEFAULT_CURRENCY, $timestamp);
			return $amount / ($rateFromDefault * $rate);
		}

		/**
		 * Normalize amount. Convert to USD.
		 *
		 * @param float      $amount
		 * @param string     $currency
		 * @param int|string $timestamp
		 *
		 * @return double
		 */
		public function normalizeAmount($amount, $currency = null, $timestamp = null) {
			if ($currency === null) {
				$currency = db_config('payments.token.base', self::DEFAULT_CURRENCY);
			}

			if ($currency === self::DEFAULT_CURRENCY) {
				return $amount;
			}

			return $this->convert($currency, self::DEFAULT_CURRENCY, $amount);
		}

		/**
		 * @return array
		 */
		public function getAllCurrencies() {
			$fiats = Rate::FIAT_CURRENCY_RATES;
			$currencies = $this->_settingsService->getAllCurrencies();

			$result = [];
			foreach ($fiats as $key => $fiat) {
				$result[$key] = [
					'network' 	=> $fiat['currency'],
					'label'		=> $fiat['name'],
					'prefix'	=> $fiat['prefix'],
					'postfix'	=> $fiat['postfix'],
					'precision' => get_precision($fiat['currency'])
				];
			}

			foreach ($currencies as $currency) {
				$key = strtoupper($currency->network);
				if (array_key_exists($key, $result)) {
					continue;
				}
				$result[$key] = [
					'network' 	=> strtoupper($currency->network),
					'label'		=> $currency->label,
					'prefix'	=> null,
					'postfix'	=> strtoupper($currency->network),
					'precision' => get_precision(strtoupper($currency->network))
				];
			}
			$result[self::DEFAULT_CURRENCY]['rate'] = 1;

			/** @var Rate[] $rates */
			$rates = $this->_rateRepository->loadLatestRatesByCurrenciesArray(array_keys($result));
			foreach ($rates as $rate) {
				if (!array_key_exists($rate->currency, $result)) {
					continue;
				}

				$result[$rate->currency]['rate'] = $rate->rate;
			}

			return $result;
		}

		/**
		 * @param string $sign
		 * @param float  $btcToUsd
		 *
		 * @return float
		 * @throws \App\Components\PaymentProvider\exceptions\FiatCurrencyNameNotFound
		 */
		private function _signToUsd($sign, $btcToUsd) {
			$btcToCurrency = PaymentProvider::getBitCoinRateToFiatCurrency($sign);
			$usdToCurrency = $btcToUsd / $btcToCurrency;
			return $usdToCurrency;
		}
	}