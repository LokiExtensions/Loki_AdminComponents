<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ProviderHandler;

use RuntimeException;

class ProviderHandlerListing
{
    public function __construct(
        /** @var ProviderHandlerInterface[] $providerHandlers */
        private array $providerHandlers = []
    ) {
    }

    public function getByProvider($provider): ProviderHandlerInterface
    {
        foreach ($this->providerHandlers as $providerHandler) {
            if ($providerHandler->match($provider)) {
                return $providerHandler;
            }
        }

        throw new RuntimeException('Unknown provider type: '.get_class($provider));
    }

    public function getByName(string $wantedProviderHandlerName): ProviderHandlerInterface
    {
        foreach ($this->providerHandlers as $providerHandlerName => $providerHandler) {
            if ($providerHandlerName === $wantedProviderHandlerName) {
                return $providerHandler;
            }
        }

        throw new RuntimeException('No provider handler found for name "'.$wantedProviderHandlerName.'"');
    }
}
