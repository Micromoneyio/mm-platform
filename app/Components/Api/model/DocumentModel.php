<?php
/**
 * Created by PhpStorm.
 * User: bazil
 * Date: 15.02.18
 * Time: 22:00
 */

namespace App\Components\Api\model;

/**
 * Class DocumentModel
 * @package App\Components\Api\model
 */
class DocumentModel {

	/** @var string */
	public $fistName;

	/** @var string */
	public $lastName;

	/** @var string */
	public $middleName;

	/** @var string */
	public $passport;

	/** @var \DateTime */
	public $birthDate;

	/** @var string */
	public $phone;

	/** @var string */
	public $nationality;

	/** @var string */
	public $country;

	/** @var string */
	public $city;

	/** @var string */
	public $address;

	/** @var string */
	public $zip;

	/** @var int */
	public $status;

	/** @var string */
	public $statusName;

	/** @var \DateTime */
	public $created;

	/** @var \DateTime */
	public $updated;

	/** @var array  */
	public $relations = [];
}