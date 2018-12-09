<?php
namespace App\Components\Api\services;


/**
 * Class NewsLineService
 * @package App\Components\Api\services
 */
class NewsLineService {

	/**
	 * @var \App\Services\NewsLineService
	 */
	private $_newsLineService;

	/**
	 * NewsLineService constructor.
	 */
	public function __construct() {
		$this->_newsLineService = resolve('\App\Services\NewsLineService');
	}

	/**
	 * @param int $id
	 *
	 * @return \App\Models\NewsLine
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public function changeActiveStatus(int $id) {
		return $this->_newsLineService->changeNewsLineActiveStatus($id);
	}


}