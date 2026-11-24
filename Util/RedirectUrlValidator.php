<?php declare(strict_types=1);

namespace Loki\AdminComponents\Util;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\Url\HostChecker;

class RedirectUrlValidator
{
    public function __construct(
        private readonly BackendUrlInterface $backendUrl,
        private readonly HostChecker $hostChecker,
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
        if ($backendHost !== '' && strtolower($urlParts['host']) === strtolower($backendHost)) {
            return true;
        }

        return $this->hostChecker->isOwnOrigin($url);
    }
}
