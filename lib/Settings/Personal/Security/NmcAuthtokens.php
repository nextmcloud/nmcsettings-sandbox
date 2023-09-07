<?php
namespace OCA\NMCSettings\Settings\Personal\Security;

use OCP\App\IAppManager;
use OCP\AppFramework\Services\IInitialState;
use OCP\IUserSession;
use function array_map;
use OC\Authentication\Exceptions\InvalidTokenException;
use OC\Authentication\Token\INamedToken;
use OC\Authentication\Token\IProvider as IAuthTokenProvider;
use OC\Authentication\Token\IToken;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IInitialStateService;
use OCP\ISession;
use OCP\Session\Exceptions\SessionNotAvailableException;
use OCP\Settings\ISettings;

class NmcAuthtokens implements ISettings {

    /** @var string */
    protected $appName;
	
	/** @var IAuthTokenProvider */
	private $tokenProvider;

	/** @var ISession */
	private $session;

	/** @var IInitialStateService */
	private $initialState;

	/** @var string|null */
	private $uid;

	/** @var IUserSession */
	private $userSession;

    /** @var string */
    private $appWebPath;

	public function __construct(
        	string $appName,
			IAuthTokenProvider $tokenProvider,
			ISession $session,
			IUserSession $userSession,
			IInitialStateService $initialState,
			IAppManager $appManager,
			?string $UserId) {
		$this->appName = $appName;
		$this->tokenProvider = $tokenProvider;
		$this->session = $session;
		$this->initialState = $initialState;
		$this->uid = $UserId;
		$this->userSession = $userSession;
        $this->appWebPath = $appManager->getAppWebPath($appName);
	}

    /**
     * @return TemplateResponse
     */
    public function getForm() {
		$this->initialState->provideInitialState(
			'settings',
			'app_tokens',
			$this->getAppTokens()
		);

		$this->initialState->provideInitialState(
			'settings',
			'can_create_app_token',
			$this->userSession->getImpersonatingUserID() === null
		);

        return new TemplateResponse('nmcsettings', 'settings/personal/session', [
            "appWebPath" => $this->appWebPath
        ]);
    }

    public function getSection() {
        return 'session'; // Name of the previously created section.
    }

    /**
     * @return int whether the form should be rather on the top or bottom of
     * the admin section. The forms are arranged in ascending order of the
     * priority values. It is required to return a value between 0 and 100.
     *
     * E.g.: 70
     */
    public function getPriority() {
        return 10;
    }

	private function getAppTokens(): array {
		$tokens = $this->tokenProvider->getTokenByUser($this->uid);

		try {
			$sessionId = $this->session->getId();
		} catch (SessionNotAvailableException $ex) {
			return [];
		}
		try {
			$sessionToken = $this->tokenProvider->getToken($sessionId);
		} catch (InvalidTokenException $ex) {
			return [];
		}

		return array_map(function (IToken $token) use ($sessionToken) {
			$data = $token->jsonSerialize();
			$data['canDelete'] = true;
			$data['canRename'] = $token instanceof INamedToken;
			if ($sessionToken->getId() === $token->getId()) {
				$data['canDelete'] = false;
				$data['canRename'] = false;
				$data['current'] = true;
			}
			return $data;
		}, $tokens);
	}
}