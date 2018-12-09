<?php
namespace App\Components\Api\services;


class UserService {

	/**
	 * @var Main
	 */
	private $_mainRepository;

	/**
	 * @var ReferralsService
	 */
	private $_referralService;

	/**
	 * @var Security
	 */
	private $_securityRepository;

	/**
	 * @var UserRepository
	 */
	private $_userRepository;

	/**
	 * @var AppUserService
	 */
	private $_userService;


	/**
	 * UserService constructor.
	 *
	 * @param Main     $main
	 * @param Security $security
	 */
	public function __construct(
		Main $main,
		Security $security
	) {
		$this->_mainRepository = $main;
		$this->_securityRepository = $security;
		$this->_userRepository = resolve('\App\Services\Repository\UserRepository');
		$this->_userService = resolve('\App\Services\UserService');
		$this->_referralService = resolve('\App\Services\ReferralsService');
	}

	/**
	 * @param bool $withKYC
	 * @param int  $limit
	 *
	 * @return array
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getCustomers($withKYC = false, $limit = null) {
		$customers = $this->_userRepository->loadUsersByParameters([
			'active' => User::USER_STATUS_ACTIVE,
		], $limit, null, null, Role::ROLE_CUSTOMER);

		$result = [];
		foreach ($customers as $customer) {
			$result[] = $this->convertUserEntityToModel($customer, $withKYC);
		}

		return $result;
	}

	/**
	 * @param User $user
	 * @param bool $withKYC
	 *
	 * @return UserModel
	 */
	public function convertUserEntityToModel(User $user, $withKYC = false) {
		$model = new UserModel();
		$model->id = (int)$user->id;
		$model->provider = $user->provider;
		$model->providerId = $user->provider_id;
		$model->wallet = $user->output_wallet;
		$model->balance = $user->totalBalance()->amount;
		$model->locale = $user->locale;
		$model->location = $user->location;
		$model->ip = $user->ip;
		$model->utmSource = $user->utm_source;
		$model->utmMedium = $user->utm_medium;
		$model->utmCampaign = $user->utm_campaign;
		$model->created = $user->created_at;

		if ($withKYC) {
			$document = $user->documents->first();
			if ($document !== null) {
				$model->relations['kyc'] = $this->convertDocumentEntityToModel($document);
			}
		}

		return $model;
	}

	/**
	 * @param Document $document
	 * @param bool     $withUser
	 *
	 * @return DocumentModel
	 */
	public function convertDocumentEntityToModel(Document $document = null, $withUser = false) {
		$model = new DocumentModel();
		$model->fistName = $document->first_name;
		$model->lastName = $document->last_name;
		$model->middleName = $document->middle_name;
		$model->passport = $document->passport;
		$model->birthDate = $document->birth_date;
		$model->phone = $document->phone;
		$model->nationality = $document->nationality;
		$model->country = $document->country;
		$model->city = $document->city;
		$model->address = $document->address;
		$model->zip = $document->zip;
		$model->status = $document->status;
		$model->statusName = $document->getStatusString();
		$model->created = $document->created_at;
		$model->updated = $document->updated_at;

		if ($withUser) {
			$model->relations['user'] = $this->convertUserEntityToModel($document->user);
		}

		return $model;
	}

	/**
	 * @return int
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getCustomersCount() {
		$customers = $this->_userRepository->loadUsersByParameters([
			'active' => User::USER_STATUS_ACTIVE,
		], null, null, null, Role::ROLE_CUSTOMER);

		return $customers->count();
	}

	/**
	 * @param bool     $withUser
	 * @param int|null $limit
	 *
	 * @return array
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getDocuments($withUser = false, $limit = null) {
		$documents = $this->_userRepository->loadAllDocuments([], $limit);
		$result = [];
		foreach ($documents as $document) {
			$result[] = $this->convertDocumentEntityToModel($document, true);
		}

		return $result;
	}

	/**
	 * @return int
	 * @throws \App\Exceptions\InvalidQueryRange
	 */
	public function getDocumentsCount() {
		$documents = $this->_userRepository->loadAllDocuments([], null);
		return $documents->count();
	}

	/**
	 * @param array $ids
	 */
	public function markCurUserNotificationsAsViewed($ids) {
		$user = Auth::user();
		$this->_userService->markUserNotificationsAsViewed($user, $ids);
	}

	/**
	 * @return TagModel[]
	 */
	public function getTags() {
		$tags = $this->_userRepository->loadTags();
		$result = [];
		foreach ($tags as $tag) {
			$result[] = $this->convertTagEntryToModel($tag);
		}
		return $result;
	}

	/**
	 * @param Tag $tag
	 *
	 * @return TagModel
	 */
	public function convertTagEntryToModel(Tag $tag) {
		$model = new TagModel();
		$model->id = $tag->id;
		$model->title = $tag->title;
		$model->color = $tag->color;
		return $model;
	}

	/**
	 * @param int $id
	 *
	 * @return TagModel
	 */
	public function getTagById(int $id) {
		$tag = $this->_userRepository->loadTagById($id);
		return $this->convertTagEntryToModel($tag);
	}

	/**
	 * @param int $id
	 *
	 * @throws \Exception
	 */
	public function removeTag(int $id) {
		$tag = $this->_userRepository->loadTagById($id);
		if (!isset($tag)) {
			throw new NotFoundHttpException('Tag not found.');
		}
		$this->_userRepository->removeTag($tag);
	}

	/**
	 * @param array    $model
	 * @param int|null $id
	 *
	 * @return TagModel
	 */
	public function setTag(array $model, $id = null) {
		if (isset($id)) {
			$tag = $this->_userRepository->loadTagById((int)$id);
			if (!isset($tag)) {
				throw new NotFoundHttpException("Tag $id not found");
			}
		} else {
			$tag = new Tag();
		}
		$tag->title = $model['title'];
		$tag->color = $model['color'];
		$tag = $this->_userRepository->storeTagByEntry($tag);
		return $this->convertTagEntryToModel($tag);
	}

	/**
	 * @param int $userId
	 * @param int $tagId
	 *
	 * @return User
	 */
	public function attachTagToUserByIds(int $userId, int $tagId) {
		/** @var User $user */
		$user = $this->_userRepository->loadUserById($userId);
		if (!isset($user)) {
			throw new NotFoundHttpException('User not found');
		}
		/** @var Tag $tag */
		$tag = $this->_userRepository->loadTagById($tagId);
		if (!isset($tag)) {
			throw new NotFoundHttpException('Tag not found');
		}

		return $this->_userRepository->attachTagToUser($user, $tag);
	}

	/**
	 * @param int $userId
	 * @param int $tagId
	 *
	 * @return User
	 */
	public function detachTagFromUserById(int $userId, int $tagId) {
		/** @var User $user */
		$user = $this->_userRepository->loadUserById($userId);
		if (!isset($user)) {
			throw new NotFoundHttpException('User not found');
		}
		/** @var Tag $tag */
		$tag = $this->_userRepository->loadTagById($tagId);
		if (!isset($tag)) {
			throw new NotFoundHttpException('Tag not found');
		}

		return $this->_userRepository->detachTagFromUser($user, $tag);
	}

	/**
	 * @param string $email
	 */
	public function inviteUserByEmail($email) {
		$user = Auth::user();
		if (empty($user)) {
			throw new AccessDeniedException();
		}
		$this->_userService->inviteUserFromEmail($user, $email);
	}

	/**
	 * @param string $type
	 */
	public function logInvite($type) {
		$user = Auth::user();
		if (empty($user)) {
			throw new AccessDeniedException();
		}
		$this->_userService->inviteUser($user, null, $type);
	}

	/**
	 * @param $type
	 * @param $username
	 * @throws \App\Exceptions\InvalidDateTimeException
	 * @throws \App\Exceptions\PropertyDoesNotExist
	 */
	public function addUserMessenger($type, $username) {
		/** @var User $user */
		$user = Auth::user();

		$this->_userService->updateUserDocument($user, [
			'messenger_type'    => $type,
			'messenger_username' => $username,
		]);
	}

	/**
	 * @return array
	 */
	public function getUserInvitesForBonus() {
		/** @var User $user */
		$user = Auth::user();

		return [
			'requiredAmount' => (int) db_config('motivation.invitations.amount', 0),
			'amount' => $this->_referralService->loadCountOfInvitationsByUser($user)
		];
	}

}