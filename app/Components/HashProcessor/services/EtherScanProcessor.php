<?php
namespace App\Components\HashProcessor\services;


class EtherScanProcessor {

	/**
	 * @see api.therscan.io
	 */
	const ETHER_SCAN_TX_URL    = 'https://api.etherscan.io/api?module=proxy&action=eth_getTransactionByHash';
	const ETHER_SCAN_BLOCK_URL = 'https://api.etherscan.io/api?module=proxy&action=eth_getBlockByNumber';

	const ETHER_SCAN_ROPSTEN_TX_URL    = 'https://ropsten.etherscan.io/api?module=proxy&action=eth_getTransactionByHash';
	const ETHER_SCAN_ROPSTEN_BLOCK_URL = 'https://ropsten.etherscan.io/api?module=proxy&action=eth_getBlockByNumber';

	/**
	 * http://api.etherscan.io/api?module=account&action=txlist
	 * &address=0xddbd2b932c763ba5b1b7ae3b362eac3e8d40121a&startblock=0&endblock=99999999&sort=asc&apikey=YourApiKeyToken
	 */
	const ETHER_SCAN_ADDRESS_URL = 'http://api.etherscan.io/api?module=account&action=txlist';

	/**
	 * @param $txId
	 * @param bool $isTest
	 * @return TxModel
	 * @throws ObjectDoesNotExist
	 * @throws ResponseDoesNotExist
	 * @throws TxDoesNotExistException
	 * @throws InvalidHashFormat
	 */
	public function getBlockInfo($txId, $isTest = false) {
		$apiKey = config('payments.etherscanio.api_key');
		try {
			$url = self::ETHER_SCAN_TX_URL . '&txhash=' . $txId . '&apikey=' . $apiKey;
			if ($isTest) {
				$url = self::ETHER_SCAN_ROPSTEN_TX_URL . '&txhash=' . $txId . '&apikey=' . $apiKey;
			}

			$response = Curl::to($url)
				->withTimeout(50)->asJsonResponse()->get();
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist('Tx block information not accessible');
		}

		if (!$response) {
			throw new ResponseDoesNotExist('Tx block information not accessible');
		}

		if (isset($response->error)) {
			throw new InvalidHashFormat('Hash in invalid format');
		}

		if ($response->result === null) {
			throw new TxDoesNotExistException('Tx block does not exist');
		}

		if (!CSL::engineService()->isAddressValid($response->result->to, 'ETH')) {
			throw new ObjectDoesNotExist('Wallet with address ' . $response->result->to . ' does not exist');
		}

		$timestamp = $this->getBlockTimestamp($response->result->blockNumber);

		return new TxModel(
			$txId,
			$response->result->blockHash,
			db_config('payments.chainso.minimal_confirmations', 6),
			hexdec(substr($timestamp, 2)),
			null,
			$response->result->to,
			(hexdec($response->result->value) / pow(10, 18)),
			[strtoupper($response->result->from)],
			'ETH'
		);
	}

	/**
	 * @param string $address
	 * @return array
	 * @throws InvalidHashFormat
	 * @throws ResponseDoesNotExist
	 * @throws TxDoesNotExistException
	 */
	public function getTransactionsFromAddress($address) {
		$apiKey = config('payments.etherscanio.api_key');
		try {
			$url = self::ETHER_SCAN_ADDRESS_URL . '&address=' . $address
				. '&startblock=0&endblock=99999999&sort=desc'
				. '&apikey=' . $apiKey;

			$response = Curl::to($url)
				->withTimeout(50)->asJsonResponse()->get();
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist('Address information not accessible');
		}

		if (!$response) {
			throw new ResponseDoesNotExist('Address information not accessible');
		}

		if (isset($response->error)) {
			throw new InvalidHashFormat('Hash in invalid format');
		}

		if ($response->result === null) {
			throw new TxDoesNotExistException('Address does not exist');
		}

		$result = [];
		foreach ($response->result as $item) {
			if (strtoupper($item->to) !== strtoupper($address)) {
				continue;
			}

			$txModel = new TxModel(
				$item->hash,
				$item->blockHash,
				$item->confirmations,
				$item->timeStamp,
			0,
				$address,
				($item->value / pow(10, 18)),
				[],
				'ETH'
			);

			$result[] = $txModel;
		}

		return $result;
	}

	/**
	 * @param $txId
	 * @param bool $isTest
	 * @return TxModel
	 * @throws ObjectDoesNotExist
	 * @throws ResponseDoesNotExist
	 * @throws TxDoesNotExistException
	 * @throws InvalidHashFormat
	 */
	public function getBlockTimestamp($blockNumber, $isTest = false) {
		$apiKey = config('payments.etherscanio.api_key');
		try {
			$url = self::ETHER_SCAN_BLOCK_URL . '&tag=' . $blockNumber . '&boolean=false&apikey=' . $apiKey;
			if ($isTest) {
				$url = self::ETHER_SCAN_ROPSTEN_BLOCK_URL . '&tag=' . $blockNumber . '&apikey=' . $apiKey;
			}

			$response = Curl::to($url)
				->withTimeout(50)->asJsonResponse()->get();
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist('Block information not accessible');
		}

		if (!$response) {
			throw new ResponseDoesNotExist('Block information not accessible');
		}

		if (isset($response->error)) {
			throw new InvalidHashFormat('Hash in invalid format');
		}

		if ($response->result === null) {
			throw new TxDoesNotExistException('Block does not exist');
		}

		return $response->result->timestamp;
	}

}