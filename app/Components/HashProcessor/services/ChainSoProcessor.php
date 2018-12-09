<?php
namespace App\Components\HashProcessor\services;


class ChainSoProcessor {

	/**
	 * Const address to api method
	 */
	const CHAIN_SO_TX_INPUTS_URL = 'https://chain.so/api/v2/get_tx_inputs/';

	/**
	 * Const address to tx block
	 */
	//const CHAIN_SO_TX_URL = 'https://chain.so/api/v2/get_tx/';
	const CHAIN_SO_TX_URL = 'https://chain.so/api/v2/tx/';

	/**
	 * Const address method
	 */
	const CHAIN_SO_ADDRESS_URL = 'https://chain.so/api/v2/get_tx_received/';

	public function __construct() {}

	/**
	 * @param string $txId
	 * @param string $network
	 * @return TxModel
	 * @throws ResponseDoesNotExist
	 * @throws TxDoesNotExistException
	 */
	public function getTxInputs($txId, $network = 'BTC') {
		try {
			$response = Curl::to(self::CHAIN_SO_TX_URL . $network . '/' . $txId)
				->withTimeout(5)->asJsonResponse()->get();
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist('Tx block information not accessible');
		}

		if ($response->status === 'fail') {
			throw new TxDoesNotExistException('Tx with hash ' . $txId . ' does not exist');
		}

		$address = false;
		$value  = false;
		foreach ($response->data->outputs as $output) {
			if (CSL::engineService()->isAddressValid($output->address, $network)) {
				$address = $output->address;
				$value   = floatval($output->value);
			}
		}

		if ($address === false || $value === false) {
			throw new TxDoesNotExistException('Tx with hash ' . $txId . ' does not exist');
		}

		$inputAddresses = [];
		foreach ($response->data->inputs as $input) {
			$inputAddresses[] = strtoupper($input->address);
		}

		return new TxModel(
			$response->data->txid,
			$response->data->blockhash,
			$response->data->confirmations,
			$response->data->time,
			null,
			$address,
			$value,
			$inputAddresses,
			$network
		);
	}

	/**
	 * @param string $address
	 * @param string $network
	 * @return array of TxModel
	 * @throws ResponseDoesNotExist
	 */
	public function getTransactionsFromAddress($address, $network) {
		try {
			$response = Curl::to(self::CHAIN_SO_ADDRESS_URL . $network . '/' . $address . '/')
				->withTimeout(2)->asJsonResponse()->get();

			if ($response->status === 'fail') {
				throw new TxDoesNotExistException('Fail to retrieve information about ' . $address);
			}

	 		$result = [];
			foreach ($response->data->txs as $tx) {
				$txModel = new TxModel(
					$tx->txid,
					null,
					$tx->confirmations,
					$tx->time,
					null,
					$address,
					$tx->value,
					[],
					$network
				);

				$result[] = $txModel;
			}

			return $result;
		} catch (\Exception $exception) {
			throw new ResponseDoesNotExist($exception->getMessage());
		}
	}
}