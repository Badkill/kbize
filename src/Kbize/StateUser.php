<?php
namespace Kbize;
use Kbize\Config\ConfigRepository;

class StateUser implements User
{
    private $configRepository;
    private $data;

    public function __construct(ConfigRepository $configRepository)
    {
        $this->data = $configRepository->toArray();
        $this->configRepository = $configRepository;
    }

    public function isAuthenticated()
    {
        return $this->apikey() ? true : false;
    }

    public function update(array $userData)
    {
        $this->configRepository
            ->replace($userData)
            ->store()
        ;

        return $this;
    }

    public function logout()
    {
        $this->configRepository->destroy();

        return $this;
    }

    public function apikey()
    {
        if (isset($this->data['apikey'])) {
            return $this->data['apikey'];
        }

        return null;
    }
}
