<?php

namespace Ugarit\Telescope\Actions;

use Heritage\Support\ServiceProvider;

class UninstallAction
{
    public function handle(): void
    {
        if (method_exists(ServiceProvider::class, 'removeProviderFromBootstrapFile')) {
            ServiceProvider::removeProviderFromBootstrapFile('TelescopeServiceProvider');
        }
    }
}
