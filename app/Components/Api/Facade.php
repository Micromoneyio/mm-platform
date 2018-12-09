<?php

namespace App\Components\Api;


class Facade {

	/**
	 * @param bool     $withKYC
	 * @param int|null $limit
	 *
	 * @return array
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public static function getCustomers($withKYC = false, $limit = null) {
		return CSL::userService()->getCustomers($withKYC, $limit);
	}

	/**
	 * @return int
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public static function getCustomersCount() {
		return CSL::userService()->getCustomersCount();
	}

	/**
	 * @param bool     $withUser
	 * @param int|null $limit
	 *
	 * @return array
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public static function getDocuments($withUser = false, $limit = null) {
		return CSL::userService()->getDocuments($withUser, $limit);
	}

	/**
	 * @return int
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public static function getDocumentsCount() {
		return CSL::userService()->getDocumentsCount();
	}

	/**
	 * @param int|null $customerId
	 * @param bool     $withUser
	 * @param int|null $limit
	 *
	 * @return array
	 * @throws \App\Exceptions\InvalidQueryRange
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public static function getPurchases(
		$customerId = null,
		$withUser = false,
		$limit = null
	) {
		return CSL::walletService()->getPurchases($customerId, $withUser, $limit);
	}

	/**
	 * @param int|null $customerId
	 *
	 * @return int
	 * @throws \App\Exceptions\InvalidQueryRange
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public static function getPurchasesCount($customerId = null) {
		return CSL::walletService()->getPurchasesCount($customerId);
	}

	/**
	 * @param string $address
	 *
	 * @return float
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public static function getAddressBalance($address) {
		return CSL::walletService()->getAddressBalance($address);
	}

	/**
	 * @param string $sectionName
	 * @param string $language
	 * @param string $key
	 *
	 * @return array|string|null
	 * @throws \Exception
	 */
	public static function getTranslationByKey(
		$sectionName,
		$language,
		$key
	) {
		return CSL::languageService()->getTranslationByKey($sectionName, $language, $key);
	}

	/**
	 * @param string $sectionName
	 * @param string $language
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function setTranslationByKey(
		$sectionName,
		$language,
		$key,
		$value
	) {
		CSL::languageService()->setTranslationByKey($sectionName, $language, $key, $value);
	}

	/**
	 * @param int  $langId
	 * @param bool $status
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function setLanguageStatus($langId, $status) {
		CSL::languageService()->setLanguageStatus($langId, $status);
	}

	/**
	 * @param string  $social
	 * @param boolean $status
	 *
	 * @return void
	 * @throws \App\Components\Config\exceptions\UnknownSystemConfigException
	 */
	public static function setSocialStatus($social, $status) {
		CSL::settingsService()->setSocialStatus($social, $status);
	}

	/**
	 * @param string $term
	 * @param string $replaceTerm
	 *
	 * @return void
	 * @throws \Exception
	 */
	public static function translationReplaceAll($term, $replaceTerm) {
		CSL::languageService()->translationReplaceAll($term, $replaceTerm);
	}

	/**
	 * @param array $ids
	 *
	 * @return void
	 */
	public static function markCurUserNotificationsAsViewed(array $ids) {
		CSL::userService()->markCurUserNotificationsAsViewed($ids);
	}

	/**
	 * @param array $ids
	 *
	 * @return void
	 */
	public static function checkPaymentAddressLogsNow(array $ids) {
		CSL::walletService()->checkPaymentAddressLogsNow($ids);
	}

	/**
	 * @param array $ids
	 * @param bool  $isCheckable
	 *
	 * @return void
	 */
	public static function setPaymentAddressLogsIsCheckable(array $ids, $isCheckable) {
		CSL::walletService()->setPaymentAddressLogsIsCheckable($ids, $isCheckable);
	}

	/**
	 * @return model\TagModel[]
	 */
	public static function getTags() {
		return CSL::userService()->getTags();
	}

	/**
	 * @param int $id
	 *
	 * @return model\TagModel
	 */
	public static function getTagById(int $id) {
		return CSL::userService()->getTagById($id);
	}

	/**
	 * @param int $id
	 *
	 * @throws \Exception
	 */
	public static function removeTag(int $id) {
		CSL::userService()->removeTag($id);
	}

	/**
	 * @param array $data
	 * @param int   $id
	 *
	 * @return model\TagModel
	 */
	public static function setTag(array $data, $id) {
		return CSL::userService()->setTag($data, $id);
	}

	/**
	 * @param int $userId
	 * @param int $tagId
	 *
	 * @return \App\Models\User
	 */
	public static function attachTagToUser(int $userId, int $tagId) {
		return CSL::userService()->attachTagToUserByIds($userId, $tagId);
	}

	/**
	 * @param int $userId
	 * @param int $tagId
	 *
	 * @return \App\Models\User
	 */
	public static function detachTagFromUser(int $userId, int $tagId) {
		return CSL::userService()->detachTagFromUserById($userId, $tagId);
	}

	/**
	 * @param string $email
	 */
	public static function inviteUserByEmail($email) {
		CSL::userService()->inviteUserByEmail($email);
	}

	/**
	 * @param string $type
	 */
	public static function logInvite($type) {
		CSL::userService()->logInvite($type);
	}

	/**
	 * @param int    $templateId
	 * @param string $email
	 *
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public static function sendTemplateTestEmail($templateId, $email) {
		CSL::settingsService()->sendTemplateTestEmail($templateId, $email);
	}

	/**
	 * @param int $id
	 *
	 * @return \App\Models\NewsLine
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 */
	public static function changeNewsLineActiveStatus(int $id) {
		return CSL::newsLineService()->changeActiveStatus($id);
	}

	/**
	 * @param $currencyCode
	 * @param $status
	 *
	 * @throws \App\Components\Config\exceptions\UnknownSystemConfigException
	 * @throws \App\Exceptions\ObjectDoesNotExist
	 * @throws \App\Exceptions\PropertyDoesNotExist
	 */
	public static function setSettingsCurrencyStatus($currencyCode, $status) {
		CSL::settingsService()->setSettingsCurrencyStatus($currencyCode, $status);
	}

	/**
	 * @param int    $type
	 * @param string $username
	 * @throws \App\Exceptions\InvalidDateTimeException
	 * @throws \App\Exceptions\PropertyDoesNotExist
	 */
	public static function addUserMessenger($type, $username) {
		CSL::userService()->addUserMessenger($type, $username);
	}

	/**
	 * @return array
	 */
	public static function getInvitesForBonus() {
		return CSL::userService()->getUserInvitesForBonus();
	}
}
