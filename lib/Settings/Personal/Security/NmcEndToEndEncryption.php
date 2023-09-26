<?php

declare(strict_types=1);

// SPDX-FileCopyrightText: 2022 Carl Schwan <carl@carlschwan.eu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NMCSettings\Settings\Personal\Security;

use OCA\EndToEndEncryption\IKeyStorage;
use OCA\EndToEndEncryption\Config;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IInitialStateService;
use OCP\Settings\ISettings;
use OCP\IUserSession;

class NmcEndToEndEncryption implements ISettings {
	private IKeyStorage $keyStorage;
	private IInitialStateService $initialStateService;
	private ?string $userId;
	private IUserSession $userSession;
	private Config $config;

	public function __construct(IKeyStorage $keyStorage, IInitialStateService $initialStateService, ?string $userId, IUserSession $userSession, Config $config) {
		$this->keyStorage = $keyStorage;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
		$this->config = $config;
		$this->userSession = $userSession;
	}

	public function getForm(): TemplateResponse {
		assert($this->userId !== null, "We are always logged in inside the setting app");

		$hasKey = $this->keyStorage->publicKeyExists($this->userId)
			&& $this->keyStorage->privateKeyExists($this->userId);
		
		$this->initialStateService->provideInitialState('end_to_end_encryption', 'hasKey', $hasKey);

		$isDisabledForUser = $this->config->isDisabledForUser($this->userSession->getUser());

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
		return 90;
	}
}
