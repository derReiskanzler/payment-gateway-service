<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Controllers;

/**
 * @codeCoverageIgnore
 */
class HealthzController extends AbstractHealthzController
{
    /**
     * Checks multiple aspects of the application to ensure proper functionality.
     */
    protected function check(): void
    {
        if ($this->configValueEmpty('amhclient.amh_oauth_credentials.client_id')) {
            $this->error('Client ID (for requesting tokens @amh-auth) is empty!');
        }

        if ($this->configValueEmpty('amhclient.amh_oauth_credentials.client_secret')) {
            $this->error('Client Secret (for requesting tokens @amh-auth) is empty!');
        }

        if ($this->configValueEmpty('amhclient.service.auth.base_url')) {
            $this->error('Auth base url is empty!');
        }

        if ($this->configValueEmpty('jwt.keys.public')) {
            $this->error('Public key is missing or empty!');
        }

        if ($this->fileExistAndNotEmpty('logging.channels.single.path', true)) {
            $this->warning('Logfile is not empty!');
        }

        if (!$this->databaseConnectionWorks()) {
            $this->error('Cannot connect to database!');
        }
    }
}
