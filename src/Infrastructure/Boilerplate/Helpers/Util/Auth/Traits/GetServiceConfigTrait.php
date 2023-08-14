<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util\Auth\Traits;

use Exception;

trait GetServiceConfigTrait
{
    private string $amhclientServiceConfig = 'amhclient.service';

    /**
     * Get service-related config.
     *
     * @param string $configItem - path to the config item. MUST include the service name, CAN be more specific.
     *
     * @throws Exception if config amhclient.service.$configItem does not exist
     *
     * @example getServiceConfig('auth')['base_url'] == getServiceConfig('auth.base_url') == config('amhclient.service.auth.base_url')
     */
    public function getServiceConfig(string $configItem): mixed
    {
        $configPath = $this->amhclientServiceConfig.'.'.$configItem;
        $config = config($configPath);

        if (empty($config)) {
            throw new Exception('Missing config "'.$configPath.'"');
        }

        return $config;
    }
}
