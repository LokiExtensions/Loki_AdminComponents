<?php declare(strict_types=1);

namespace Loki\AdminComponents\Util;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;

class RedirectUrlValidator
{
    public function __construct(
        private readonly BackendUrlInterface $backendUrl,
    ) {
    }

    public function isValid(mixed $url): bool
    {
        if (!is_string($url) || $url === '') {
            return false;
        }

        if (preg_match('/[\s\x00-\x1F\x7F\\\\]/', $url)) {
            return false;
        }

        $urlParts = parse_url($url);
        if ($urlParts === false) {
            return false;
        }

        if (!isset($urlParts['host'])) {
            return !isset($urlParts['scheme']) && str_starts_with($url, '/') && !str_starts_with($url, '//');
        }

        $scheme = strtolower((string)($urlParts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $backendHost = (string)parse_url($this->backendUrl->getBaseUrl(), PHP_URL_HOST);

        return $backendHost !== '' && strtolower($urlParts['host']) === strtolower($backendHost);
    }
}
