<?php

namespace OCA\NMCSettings\Settings\Personal;

use OC\Files\View;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Files\FileInfo;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IL10N;
use OCP\IUserManager;
use OCP\L10N\IFactory;
use OCP\Settings\ISettings;

class NmcPersonalInfoStorage implements ISettings {

	/** @var IConfig */
	private $config;

	/** @var IUserManager */
	private $userManager;

	/** @var IL10N */
	private $l;

	/** @var IFactory */
	private $l10nFactory;

	/** @var IDBConnection */
	private $db;

	private $uid;

	public function __construct(
		IUserManager $userManager,
		IL10N $l,
		IFactory $l10nFactory,
		IDBConnection $db,
		IConfig $config
	) {
		$this->userManager = $userManager;
		$this->l = $l;
		$this->l10nFactory = $l10nFactory;
		$this->db = $db;
		$this->config = $config;
		$this->uid = \OC_User::getUser();
	}

	public function getForm(): TemplateResponse {

		$user = $this->userManager->get($this->uid);

		$imageMimetypes = "'image','image/jpg','image/jpeg','image/gif','image/png','image/svg+xml','image/webp'";
		$videoMimetypes = "'video','video/3gpp','video/mp4', 'video/mov', 'video/avi', 'video/flv'";

		$imageStorageInBytes = $this->storageUtilization($this->uid, $imageMimetypes);
		$videoStorageInBytes = $this->storageUtilization($this->uid, $videoMimetypes);
		$photoVideoSizeInBytes = $imageStorageInBytes + $videoStorageInBytes;

		// make sure FS is setup before querying storage related stuff...
		\OC_Util::setupFS($user->getUID());

		$storageInfo = \OC_Helper::getStorageInfo('/');
		if ($storageInfo['quota'] === FileInfo::SPACE_UNLIMITED) {
			$totalSpace = $this->l->t('Unlimited');
		} else {
			$totalSpace = $this->humanFileSize($storageInfo['total']);
		}

		$trashSizeinBytes = self::getTrashbinSize($this->uid);
		$filesSizeInBytes = $storageInfo['used'] - ($photoVideoSizeInBytes);

		if($filesSizeInBytes < 0) {
			$filesSizeInBytes = 0;
		}

		$personalInfoStorageParameters = [
			'quota' => $storageInfo['quota'],
			'totalSpace' => $totalSpace,
			'tariff' => $this->getTariff($storageInfo['quota']),
			'usage' => $this->humanFileSize($storageInfo['used']),
			'usageRelative' => round($storageInfo['relative']),
			'trashSize' => $this->humanFileSize($trashSizeinBytes),
			'photoVideoSize' => $this->humanFileSize($photoVideoSizeInBytes),
			'filesSize' => $this->humanFileSize($filesSizeInBytes),
			'trashSizeInPer' => round(($trashSizeinBytes / $storageInfo['quota']) * 100) ,
			'photoVideoSizeInPer' => round(($photoVideoSizeInBytes / $storageInfo['quota']) * 100),
			'filesSizeInPer' => round(($filesSizeInBytes / $storageInfo['quota']) * 100) ,
		];

		$parameters = $personalInfoStorageParameters;

		return new TemplateResponse('nmcsettings', 'settings/personal/storage', $parameters, '');
	}
	
	public function getSection(): string {
		return 'account';
	}

	public function getPriority(): int {
		return 99;
	}

	private function getTariff($quota) {

		$totalSpaceInGB = null;

		if($quota >= 1024) {
			$totalSpaceInKB = round($quota / 1024, 1);
			$totalSpaceInMB = round($totalSpaceInKB / 1024, 1);
			$totalSpaceInGB = round($totalSpaceInMB / 1024, 1);
		}

		if ($quota == 0) {
			$tariff = $this->l->t('No space allocated');
		} elseif($quota === FileInfo::SPACE_UNLIMITED) {
			$tariff = $this->l->t('Unlimited');
		} elseif($quota === FileInfo::SPACE_UNKNOWN) {
			$tariff = $this->l->t('Space unknown');
		} elseif($quota === FileInfo::SPACE_NOT_COMPUTED) {
			$tariff = $this->l->t('Space not computed');
		} elseif ($totalSpaceInGB == 1 || $totalSpaceInGB == 3 || $totalSpaceInGB == 10) {
			$tariff = $this->l->t('MagentaCLOUD Free');
		} elseif ($totalSpaceInGB == 15 || $totalSpaceInGB == 25) {
			$tariff = $this->l->t('MagentaCLOUD S');
		} elseif ($totalSpaceInGB == 100) {
			$tariff = $this->l->t('MagentaCLOUD M');
		} elseif ($totalSpaceInGB == 500) {
			$tariff = $this->l->t('MagentaCLOUD L');
		} elseif ($totalSpaceInGB == 1024) {
			$tariff = $this->l->t('MagentaCLOUD XL');
		} elseif ($totalSpaceInGB == 5120) {
			$tariff = $this->l->t('MagentaCLOUD XXL');
		} else {
			$tariff = $this->l->t('Tariff unknown');
		}

		return $tariff;
	}

	private static function getTrashbinSize($user) {
		$view = new View('/' . $user);
		$fileInfo = $view->getFileInfo('/files_trashbin');
		return isset($fileInfo['size']) ? $fileInfo['size'] : 0;
	}

	private function storageUtilization($user = null, $filterMimetypes = null) {
		$details = null;

		$rootFolder = \OC::$server->getRootFolder()->getUserFolder($user);
		$storageId = $rootFolder->getStorage()->getCache()->getNumericStorageId();

		$query = $this->db->getQueryBuilder();

		$query->selectAlias($query->func()->sum('size'), 'f1')
			->from('filecache', 'fc')
			->innerJoin('fc', 'mimetypes', 'mt', $query->expr()->eq('fc.mimetype', 'mt.id'))
			->where('mt.mimetype in('.$filterMimetypes.')')
			->andWhere($query->expr()->neq('fc.size', $query->createPositionalParameter(-1)))
			->andWhere("fc.path NOT Like 'files_trashbin/files/%'")
			->andWhere($query->expr()->eq('fc.storage', $query->createPositionalParameter($storageId)));

		$result = $query->execute();

		while ($row = $result->fetch()) {
			$details = $row['f1'];
		}
		$result->closeCursor();
		return $details;
	}

	private function humanFileSize($bytes, $binary = true) {
		$humanList = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		$kilo = 1000;

		if($binary) {
			$humanList = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
			$kilo = 1024;
		}

		$order = floor(log($bytes) / log($kilo));
		$order = min(sizeof($humanList) - 1, $order);
		$readableFormat = $humanList[$order];
		$relativeSize = round($bytes / pow($kilo, $order), 1);
		
		if ($bytes < $kilo) {
			return "$bytes B";
		} else {
			$decimalSeparator = ',';
			$lang = $this->config->getUserValue($this->uid, 'core', 'lang', $this->l10nFactory->findLanguage());
			$locale = explode('_', $lang);

			if($locale[0] === 'en') {
				$decimalSeparator = '.';
			}

			$relativeSize = number_format($relativeSize, 1, $decimalSeparator, '');
			return $relativeSize . ' ' . $readableFormat;
		}
	}
}
