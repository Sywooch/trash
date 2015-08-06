<?php

class ServiceMasterIdentity extends MasterIdentity
{
	const ERROR_NOT_AUTHENTICATED = 3;

	/**
	 * @var EAuthServiceBase the authorization service instance.
	 */
	protected $service;

	/**
	 * Constructor.
	 *
	 * @param EAuthServiceBase $service the authorization service instance.
	 */
	public function __construct($service)
	{
		$this->service = $service;
	}

	/**
	 * Authenticates a user based on {@link username}.
	 * This method is required by {@link IUserIdentity}.
	 *
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		if ($this->service->isAuthenticated) {
			$this->username = $this->service->getAttribute('name');
			$this->setState('id', $this->service->id);
			if ($this->service instanceof FacebookOAuthService) {
				$this->setState(
					'photo',
					$this->service->getAttribute('photo_medium')
				);
			} else {
				$this->setState('photo', $this->service->photo_medium);
			}

			$this->setState('name', $this->username);
			$this->setState('service', $this->service->serviceName);
			//$this->setState('city', $this->service->city);
			$this->errorCode = self::ERROR_NONE;

		} else {
			$this->errorCode = self::ERROR_NOT_AUTHENTICATED;
		}
		return !$this->errorCode;
	}
}