<?php

declare(strict_types=1);

// SPDX-FileCopyrightText: 2022 Carl Schwan <carl@carlschwan.eu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NMCSettings\Settings\Personal\Security;

use OCA\EndToEndEncryption\Config as EConfig;
use OCA\EndToEndEncryption\IKeyStorage;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IUserSession;
use OCP\Settings\ISettings;

class NmcEndToEndEncryption implements ISettings {
	private IKeyStorage $keyStorage;
	private IInitialStateService $initialStateService;
	private ?string $userId;
	private IUserSession $userSession;
	private EConfig $eConfig;
	private IConfig $config;

	public function __construct(IKeyStorage $keyStorage, IInitialStateService $initialStateService, ?string $userId, IUserSession $userSession, EConfig $eConfig, IConfig $config) {
		$this->keyStorage = $keyStorage;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
		$this->eConfig = $eConfig;
		$this->config = $config;
		$this->userSession = $userSession;
	}

	public function getForm(): TemplateResponse {
		$isDisabledForUser = $this->eConfig->isDisabledForUser($this->userSession->getUser());

		if($this->config->getAppValue('end_to_end_encryption', 'enabled') === 'no' || $isDisabledForUser) {
			return new TemplateResponse('settings', 'settings/empty', [], '');
		}

		assert($this->userId !== null, "We are always logged in inside the setting app");

		$hasKey = $this->keyStorage->publicKeyExists($this->userId)
			&& $this->keyStorage->privateKeyExists($this->userId);
		
		$this->initialStateService->provideInitialState('end_to_end_encryption', 'hasKey', $hasKey);

		return new TemplateResponse(
			'end_to_end_encryption',
			'settings',
			["canUseApp" => !$isDisabledForUser]
		);
	}

	public function getSection(): string {
		return 'sessions';
	}

	public function getPriority(): int {
		return 5;
	}
}
