<?php

namespace Infrastructure\Security;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

class Security implements SecurityInterface
{
    public const LAST_USER = '_application.last_logged_user';

    protected static ?self $instance = null;

    private bool $authenticated;

    private ?User $user;
    private ?array $credentials;
    private SessionInterface $session;
    private UserProvider $provider;

    private function __construct(SessionInterface $session, UserProvider $provider)
    {
        $this->session = $session;
        $this->provider = $provider;

        $this->init();
        $this->getAuthSession();
    }

    private function init(): void
    {
        $this->setAuthenticated(false);
        $this->credentials = null;
        $this->user = null;
    }


    private function getAuthSession(): void
    {
        if ($this->session->has(self::LAST_USER)) {
            $credentials = $this->session->get(self::LAST_USER);
            $credentials = unserialize($credentials);
            $this->withCredentials($credentials);
            $this->auth();
            return;
        }
    }

    private function setAuthSession(): void
    {
        if ($this->hasCredentials()) {
            $this->session->set(self::LAST_USER, serialize($this->credentials));
            return;
        }

        $this->clearAuthSession();
    }

    private function clearAuthSession(): void
    {
        $this->session->remove(self::LAST_USER);
        $this->setAuthenticated(false);
    }

    private function withCredentials(array $credentials): void
    {
        Assert::keyExists($credentials, 'name');
        Assert::keyExists($credentials, 'password');

        $this->credentials = [
            'name' => $credentials['name'],
            'password' => $credentials['password']
        ];
    }

    private function auth(): void
    {
        if ($this->hasCredentials()) {
            $user = $this->provider->loadUser($this->credentials);
            if ($this->provider->checkCredentials($this->credentials, $user)) {
                $this->setUser($user);
                $this->setAuthenticated(true);
                return;
            }
        }

        $this->clearAuthSession();
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function hasCredentials(): bool
    {
        return is_array($this->credentials);
    }

    private function setAuthenticated(bool $authenticated): void
    {
        $this->authenticated = $authenticated;
    }

    public function isAuth(): bool
    {
        return $this->authenticated;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function loginAs(User $user): void
    {
        $this->setUser($user);
        $this->withCredentials([
            'name' => $user->getName(),
            'password' => $user->getPasswordHash()
        ]);
        $this->setAuthSession();
    }

    public function logout(): void
    {
        $this->clearAuthSession();
    }

    public static function getInstance(SessionInterface $session, UserProvider $provider): self
    {
        if(self::$instance instanceof self){
            return self::$instance;
        }
        $instance = new self($session, $provider);
        self::$instance = $instance;
        return self::$instance;
    }
}