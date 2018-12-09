<?php
namespace App\Components\Api\services;


/**
 * Class SettingsService
 * @package App\Components\Api\services
 */
class SettingsService {


	/**
	 * @var \App\Services\SettingsService
	 */
	private $_settingsService;

	/**
	 * SettingsService constructor.
	 */
	public function __construct() {
		$this->_settingsService = resolve('\App\Services\SettingsService');
	}

	/**
	 * @param string $social
	 * @param boolean $status
	 * @throws \App\Components\Config\exceptions\UnknownSystemConfigException
	 */
	public function setSocialStatus($social, $status) {
		$this->_settingsService->setSocialStatus($social, $status);
	}

	/**
	 * @param int    $templateId
	 * @param string $email
	 *
	 * @throws ObjectDoesNotExist
	 */
	public function sendTemplateTestEmail($templateId, $email) {
		$user = Auth::user();
		if (empty($user)) {
			throw new AccessDeniedException();
		}

		$template = $this->_settingsService->getEmailTemplateById($templateId);
		if (empty($template)) {
			throw new ObjectDoesNotExist('Email template with id:' . $templateId . ' does not exists');
		}

		$this->_settingsService->sendTemplateTestEmail($template, $email);
	}

	/**
	 * @param string $currencyCode
	 * @param int    $status
	 *
	 * @throws \App\Components\Config\exceptions\UnknownSystemConfigException
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 * @throws \App\Exceptions\PropertyDoesNotExist
	 */
	public function setSettingsCurrencyStatus($currencyCode, $status) {
		$this->_settingsService->setCurrency($currencyCode, [
			'status' => $status
		]);
	}
}