<?php
/**
 * @copyright Copyright (c) 2017 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\NMCSettings\Settings\Personal\Security;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\ISession;
use OCP\IUserSession;
use OCP\Settings\ISettings;

class NmcEncryption implements ISettings {

	/** @var IConfig */
	private $config;
	/** @var ISession */
	private $session;
	/** @var IUserSession */
	private $userSession;

	public const NOT_INITIALIZED = '0';
	public const INIT_EXECUTED = '1';
	public const INIT_SUCCESSFUL = '2';

	public function __construct(IConfig $config, ISession $session, IUserSession $userSession) {
		$this->config = $config;
		$this->session = $session;
		$this->userSession = $userSession;
	}

	/**
	 * @return TemplateResponse returns the instance with all parameters set, ready to be rendered
	 * @since 9.1
	 */
	public function getForm() {
		if($this->config->getAppValue('encryption', 'enabled') === 'no') {
			return new TemplateResponse('settings', 'settings/empty', [], '');
		}

		$recoveryAdminEnabled = $this->config->getAppValue('encryption', 'recoveryAdminEnabled');
		$privateKeySet = $this->isPrivateKeySet();

		if (!$recoveryAdminEnabled && $privateKeySet) {
			return new TemplateResponse('settings', 'settings/empty', [], '');
		}

		$userId = $this->userSession->getUser()->getUID();
		$recoveryEnabledForUser = $this->isRecoveryEnabledForUser($userId);

		$parameters = [
			'recoveryEnabled' => $recoveryAdminEnabled,
			'recoveryEnabledForUser' => $recoveryEnabledForUser,
			'privateKeySet' => $privateKeySet,
			'initialized' => $this->getStatus(),
		];
		return new TemplateResponse('encryption', 'settings-personal', $parameters, '');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 * @since 9.1
	 */
	public function getSection() {
		return 'sessions';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 * @since 9.1
	 */
	public function getPriority() {
		return 99;
	}

	/**
	 * Gets status if we already tried to initialize the encryption app
	 *
	 * @return string init status INIT_SUCCESSFUL, INIT_EXECUTED, NOT_INITIALIZED
	 */
	public function getStatus() {
		$status = $this->session->get('encryptionInitialized');
		if (is_null($status)) {
			$status = self::NOT_INITIALIZED;
		}

		return $status;
	}

	/**
	 * check if private key is set
	 *
	 * @return boolean
	 */
	public function isPrivateKeySet() {
		$key = $this->session->get('privateKey');
		if (is_null($key)) {
			return false;
		}

		return true;
	}

	/**
	 * check if recovery key is enabled for user
	 *
	 * @param string $uid
	 * @return bool
	 */
	public function isRecoveryEnabledForUser($uid) {
		$recoveryMode = $this->config->getUserValue($uid,
			'encryption',
			'recoveryEnabled',
			'0');

		return ($recoveryMode === '1');
	}
}
