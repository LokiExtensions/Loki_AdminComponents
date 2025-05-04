<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

class ProviderHandlerResolver
{
    public function __construct(
        private array $providerHandlers = []
    ) {
    }

    public function resolve(string $wantedProviderHandlerName): ProviderHandlerInterface
    {
        foreach ($this->providerHandlers as $providerHandlerName => $providerHandler) {
            if ($providerHandlerName === $wantedProviderHandlerName) {
                return $providerHandler;
            }
        }

        throw new \RuntimeException('No provider handler found for name "'.$wantedProviderHandlerName.'"');
    }
}
