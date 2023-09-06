<?php
/**
 * @copyright Copyright (c) 2023 T-Systems International
 *
 * @author M. Mura <Mauro-Efisio.Mura@t-systems.com>
 *
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\NMCSettings\AppInfo;

use OC\AppFramework\DependencyInjection\DIContainer;
use OC\Server;
use OC\Settings\Manager;
use OCA\NMCSettings\ManagerDecorator;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\IAppContainer;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\Defaults;
use OCP\IServerContainer;
use OCP\Settings\IManager;
use OCA\NMCSettings\Listener\BeforeTemplateRenderedListener;

class Application extends App implements IBootstrap {
	public const APP_ID = 'nmcsettings';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function getCapturedSettingsContainer() {
		$appName = "settings";
		
		try {
			$container = \OC::$server->getRegisteredAppContainer($appName);
		} catch (QueryException $e) {
			$container = new DIContainer($appName);
			\OC::$server->registerAppContainer($appName, $container);
		}

		return $container;
	}

	public function register(IRegistrationContext $context): void {
/*
		$this->getCapturedSettingsContainer()->registerService(IManager::class, function ($c) {

			$manager = $c->get(Manager::class);
			$managerDecorator = new ManagerDecorator($manager);

			return $managerDecorator;
		});
*/

		// the listener is helpful to enforce theme constraints and inject additional parts
		$context->registerEventListener(BeforeTemplateRenderedEvent::class, BeforeTemplateRenderedListener::class);
	}

	public function boot(IBootContext $context): void {
	}
}
