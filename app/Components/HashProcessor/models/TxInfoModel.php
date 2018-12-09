<?php
namespace App\Components\HashProcessor\models;


class TxInfoModel {

	/**
	 * @var string
	 */
	public $txid;

	/**
	 * @var string
	 */
	public $network;

	/**
	 * @var float
	 */
	public $value;

	/**
	 * @var string
	 */
	public $address;

	/**
	 * @var string
	 */
	public $script;

	/**
	 * TxInfoModel constructor.
	 * @param string $txid
	 * @param string $network
	 * @param float $value
	 * @param string $address
	 * @param string $script
	 */
	public function __construct(
		$txid,
		$network,
		$value,
		$address,
		$script
	) {
		$this->txid     = $txid;
		$this->network  = $network;
		$this->value    = floatval($value);
		$this->address  = $address;
		$this->script   = $script;
	}
}