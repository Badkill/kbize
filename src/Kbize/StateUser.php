<?php
namespace Kbize;
use Kbize\Config\ConfigRepository;

class StateUser implements User
{
    const CONFIG_REPOSITORY_NAME = 'user.yml';

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
        $this->replaceData($userData);

        $this->configRepository
            ->replace($this->data)
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

    public function toArray()
    {
        return $this->data;
    }

    private function replaceData(array $userData)
    {
        //TODO:! ensure data is valid

        $this->data = $userData;
    }
}
