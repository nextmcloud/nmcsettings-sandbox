<?php

namespace OCA\NMCSettings\Settings\Personal;

use OC\Profile\ProfileManager;
use OCA\FederatedFileSharing\FederatedShareProvider;
use OCP\Accounts\IAccount;
use OCP\Accounts\IAccountManager;
use OCP\Accounts\IAccountProperty;
use OCP\App\IAppManager;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\IUser;
use OCP\IUserManager;
use OCP\L10N\IFactory;
use OCP\Notification\IManager;
use OCP\Settings\ISettings;

class NmcPersonalInfo implements ISettings {

	/** @var IConfig */
	private $config;

	/** @var IUserManager */
	private $userManager;

	/** @var IAccountManager */
	private $accountManager;

	/** @var ProfileManager */
	private $profileManager;

	/** @var IGroupManager */
	private $groupManager;

	/** @var IAppManager */
	private $appManager;

	/** @var IFactory */
	private $l10nFactory;

	/** @var IL10N */
	private $l;

	/** @var IInitialStateService */
	private $initialStateService;

	/** @var IManager */
	private $manager;

	public function __construct(
		IConfig $config,
		IUserManager $userManager,
		IGroupManager $groupManager,
		IAccountManager $accountManager,
		ProfileManager $profileManager,
		IAppManager $appManager,
		IFactory $l10nFactory,
		IL10N $l,
		IInitialStateService $initialStateService,
		IManager $manager
	) {
		$this->config = $config;
		$this->userManager = $userManager;
		$this->accountManager = $accountManager;
		$this->profileManager = $profileManager;
		$this->groupManager = $groupManager;
		$this->appManager = $appManager;
		$this->l10nFactory = $l10nFactory;
		$this->l = $l;
		$this->initialStateService = $initialStateService;
		$this->manager = $manager;
	}

	public function getForm(): TemplateResponse {
		$federationEnabled = $this->appManager->isEnabledForUser('federation');
		$federatedFileSharingEnabled = $this->appManager->isEnabledForUser('federatedfilesharing');
		$lookupServerUploadEnabled = false;
		if ($federatedFileSharingEnabled) {
			/** @var FederatedShareProvider $shareProvider */
			$shareProvider = \OC::$server->query(FederatedShareProvider::class);
			$lookupServerUploadEnabled = $shareProvider->isLookupServerUploadEnabled();
		}

		$uid = \OC_User::getUser();
		$user = $this->userManager->get($uid);
		$account = $this->accountManager->getAccount($user);

		$messageParameters = $this->getMessageParameters($account);

		$personalInfoParameters = [
			'userId' => $uid,
			'avatar' => $this->getProperty($account, IAccountManager::PROPERTY_AVATAR),
			'groups' => $this->getGroups($user),
			'displayName' => $this->getProperty($account, IAccountManager::PROPERTY_DISPLAYNAME),
			'emailMap' => $this->getEmailMap($account),
			'phone' => $this->getProperty($account, IAccountManager::PROPERTY_PHONE),
			'defaultPhoneRegion' => $this->config->getSystemValueString('default_phone_region'),
			'location' => $this->getProperty($account, IAccountManager::PROPERTY_ADDRESS),
			'website' => $this->getProperty($account, IAccountManager::PROPERTY_WEBSITE),
			'twitter' => $this->getProperty($account, IAccountManager::PROPERTY_TWITTER),
			'languageMap' => $this->getLanguageMap($user),
			'localeMap' => $this->getLocaleMap($user),
			'profileEnabledGlobally' => $this->profileManager->isProfileEnabled(),
			'profileEnabled' => $this->profileManager->isProfileEnabled($user),
			'organisation' => $this->getProperty($account, IAccountManager::PROPERTY_ORGANISATION),
			'role' => $this->getProperty($account, IAccountManager::PROPERTY_ROLE),
			'headline' => $this->getProperty($account, IAccountManager::PROPERTY_HEADLINE),
			'biography' => $this->getProperty($account, IAccountManager::PROPERTY_BIOGRAPHY),
		];

		$parameters = [
			'lookupServerUploadEnabled' => $lookupServerUploadEnabled,
			'isFairUseOfFreePushService' => $this->isFairUseOfFreePushService(),
			'profileEnabledGlobally' => $this->profileManager->isProfileEnabled(),
		] + $messageParameters + $personalInfoParameters;

		$accountParameters = [
			'avatarChangeSupported' => $user->canChangeAvatar(),
			'displayNameChangeSupported' => $user->canChangeDisplayName(),
			'federationEnabled' => $federationEnabled,
			'lookupServerUploadEnabled' => $lookupServerUploadEnabled,
		];

		$profileParameters = [
			'profileConfig' => $this->profileManager->getProfileConfigWithMetadata($user, $user),
		];

		$this->initialStateService->provideInitialState('settings', 'profileEnabledGlobally', $this->profileManager->isProfileEnabled());
		$this->initialStateService->provideInitialState('settings', 'personalInfoParameters', $personalInfoParameters);
		$this->initialStateService->provideInitialState('settings', 'accountParameters', $accountParameters);
		$this->initialStateService->provideInitialState('settings', 'profileParameters', $profileParameters);

		return new TemplateResponse('nmcsettings', 'settings/personal/account', $parameters, '');
	}
	
	public function getSection(): string {
		return 'account';
	}

	public function getPriority(): int {
		return 0;
	}

	/**
	 * Check if is fair use of free push service
	 * @return boolean
	 */
	private function isFairUseOfFreePushService(): bool {
		return $this->manager->isFairUseOfFreePushService();
	}

	/**
	 * returns the property data in an
	 * associative array
	 */
	private function getProperty(IAccount $account, string $property): array {
		$property = [
			'name' => $account->getProperty($property)->getName(),
			'value' => $account->getProperty($property)->getValue(),
			'scope' => $account->getProperty($property)->getScope(),
			'verified' => $account->getProperty($property)->getVerified(),
		];

		return $property;
	}

	private function getGroups(IUser $user): array {
		$groups = array_map(
			static function (IGroup $group) {
				return $group->getDisplayName();
			},
			$this->groupManager->getUserGroups($user)
		);
		sort($groups);

		return $groups;
	}

	private function getEmailMap(IAccount $account): array {
		$systemEmail = [
			'name' => $account->getProperty(IAccountManager::PROPERTY_EMAIL)->getName(),
			'value' => $account->getProperty(IAccountManager::PROPERTY_EMAIL)->getValue(),
			'scope' => $account->getProperty(IAccountManager::PROPERTY_EMAIL)->getScope(),
			'verified' => $account->getProperty(IAccountManager::PROPERTY_EMAIL)->getVerified(),
		];

		$additionalEmails = array_map(
			function (IAccountProperty $property) {
				return [
					'name' => $property->getName(),
					'value' => $property->getValue(),
					'scope' => $property->getScope(),
					'verified' => $property->getVerified(),
					'locallyVerified' => $property->getLocallyVerified(),
				];
			},
			$account->getPropertyCollection(IAccountManager::COLLECTION_EMAIL)->getProperties(),
		);

		$emailMap = [
			'primaryEmail' => $systemEmail,
			'additionalEmails' => $additionalEmails,
			'notificationEmail' => (string)$account->getUser()->getPrimaryEMailAddress(),
		];

		return $emailMap;
	}

	private function getLanguageMap(IUser $user): array {
		$forceLanguage = $this->config->getSystemValue('force_language', false);
		if ($forceLanguage !== false) {
			return [];
		}

		$uid = $user->getUID();

		$userConfLang = $this->config->getUserValue($uid, 'core', 'lang', $this->l10nFactory->findLanguage());
		$languages = $this->l10nFactory->getLanguages();

		// associate the user language with the proper array
		$userLangIndex = array_search($userConfLang, array_column($languages['commonLanguages'], 'code'));
		$userLang = $languages['commonLanguages'][$userLangIndex];
		// search in the other languages
		if ($userLangIndex === false) {
			$userLangIndex = array_search($userConfLang, array_column($languages['otherLanguages'], 'code'));
			$userLang = $languages['otherLanguages'][$userLangIndex];
		}
		// if user language is not available but set somehow: show the actual code as name
		if (!is_array($userLang)) {
			$userLang = [
				'code' => $userConfLang,
				'name' => $userConfLang,
			];
		}

		return array_merge(
			['activeLanguage' => $userLang],
			$languages
		);
	}

	private function getLocaleMap(IUser $user): array {
		$forceLanguage = $this->config->getSystemValue('force_locale', false);
		if ($forceLanguage !== false) {
			return [];
		}

		$uid = $user->getUID();
		$userLocaleString = $this->config->getUserValue($uid, 'core', 'locale', $this->l10nFactory->findLocale());
		$userLang = $this->config->getUserValue($uid, 'core', 'lang', $this->l10nFactory->findLanguage());
		$localeCodes = $this->l10nFactory->findAvailableLocales();
		$userLocale = array_filter($localeCodes, fn ($value) => $userLocaleString === $value['code']);

		if (!empty($userLocale)) {
			$userLocale = reset($userLocale);
		}

		$localesForLanguage = array_values(array_filter($localeCodes, fn ($localeCode) => str_starts_with($localeCode['code'], $userLang)));
		$otherLocales = array_values(array_filter($localeCodes, fn ($localeCode) => !str_starts_with($localeCode['code'], $userLang)));

		if (!$userLocale) {
			$userLocale = [
				'code' => 'en_GB',
				'name' => 'English'
			];
		}

		return [
			'activeLocaleLang' => $userLocaleString,
			'activeLocale' => $userLocale,
			'localesForLanguage' => $localesForLanguage,
			'otherLocales' => $otherLocales,
		];
	}

	private function getMessageParameters(IAccount $account): array {
		$needVerifyMessage = [IAccountManager::PROPERTY_EMAIL, IAccountManager::PROPERTY_WEBSITE, IAccountManager::PROPERTY_TWITTER];
		$messageParameters = [];
		foreach ($needVerifyMessage as $property) {
			switch ($account->getProperty($property)->getVerified()) {
				case IAccountManager::VERIFIED:
					$message = $this->l->t('Verifying');
					break;
				case IAccountManager::VERIFICATION_IN_PROGRESS:
					$message = $this->l->t('Verifying …');
					break;
				default:
					$message = $this->l->t('Verify');
			}
			$messageParameters[$property . 'Message'] = $message;
		}
		return $messageParameters;
	}
}
