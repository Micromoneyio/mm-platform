<?php
namespace App\Components\Api\services;

class SiteContentService {

	/**
	 * @var BaseSiteContentService
	 */
	private $_siteContentService;

	public function __construct() {
		$this->_siteContentService = resolve('\App\Services\SiteContentService');
	}


	/**
	 * @param string $sectionName
	 * @param string $language
	 * @param string $key
	 * @return array|string|null
	 * @throws \Exception
	 */
	public function getTranslationByKey(
		$sectionName,
		$language,
		$key
	) {
		return Translation::getTranslationByKey($sectionName, $language, $key);
	}

	/**
	 * @param string $sectionName
	 * @param string $language
	 * @param string $key
	 * @param string $value
	 * @return void
	 * @throws \Exception
	 */
	public function setTranslationByKey(
		$sectionName,
		$language,
		$key,
		$value
	) {
		Translation::setTranslationByKey($sectionName, $language, $key, $value);
	}

	/**
	 * @param int  $langId
	 * @param bool $status
	 * @return void
	 * @throws \Exception
	 */
	public function setLanguageStatus($langId, $status) {
		$this->_siteContentService->updateLanguage($langId, [
			'status' => $status
		]);
	}

	/**
	 * @param string $term
	 * @param string $replaceTerm
	 * @return void
	 * @throws \Exception
	 */
	public function translationReplaceAll($term, $replaceTerm) {
		Translation::translationReplaceAll($term, $replaceTerm);
	}

}