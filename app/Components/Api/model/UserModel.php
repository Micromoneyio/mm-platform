<?php
namespace App\Components\Api\model;

/**
 * Class UserModel
 * @package App\Components\Api\model
 */
class UserModel {

	/** @var int */
	public $id;

	/** @var string */
	public $provider;

	/** @var mixed */
	public $providerId;

	/** @var string */
	public $wallet;

	/** @var float */
	public $balance;

	/** @var string */
	public $locale;

	/** @var string */
	public $location;

	/** @var string */
	public $ip;

	/** @var string */
	public $utmSource;

	/** @var string */
	public $utmMedium;

	/** @var string */
	public $utmCampaign;

	/** @var \DateTime */
	public $created;

	/** @var array  */
	public $relations = [];
}