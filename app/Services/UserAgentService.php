<?php

namespace App\Services;

class UserAgentService
{
    /**
     * Parse user agent string and extract device, browser, and OS information
     */
    public static function parse(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'browser_version' => null,
                'os' => 'unknown',
                'os_version' => null,
            ];
        }

        return [
            'device_type' => self::detectDeviceType($userAgent),
            'browser' => self::detectBrowser($userAgent)['name'],
            'browser_version' => self::detectBrowser($userAgent)['version'],
            'os' => self::detectOS($userAgent)['name'],
            'os_version' => self::detectOS($userAgent)['version'],
        ];
    }

    /**
     * Detect device type from user agent
     */
    protected static function detectDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        // Mobile devices
        if (preg_match('/(iphone|ipod|android|blackberry|mini|windows\sce|palm)/i', $userAgent)) {
            return 'mobile';
        }

        // Tablets
        if (preg_match('/(ipad|tablet|kindle|silk|playbook)/i', $userAgent)) {
            return 'tablet';
        }

        // Desktop
        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    protected static function detectBrowser(string $userAgent): array
    {
        $browsers = [
            'Edg' => ['name' => 'Edge', 'pattern' => '/Edg\/([0-9.]+)/'],
            'Chrome' => ['name' => 'Chrome', 'pattern' => '/Chrome\/([0-9.]+)/'],
            'Safari' => ['name' => 'Safari', 'pattern' => '/Version\/([0-9.]+).*Safari/'],
            'Firefox' => ['name' => 'Firefox', 'pattern' => '/Firefox\/([0-9.]+)/'],
            'Opera' => ['name' => 'Opera', 'pattern' => '/Opera\/([0-9.]+)/'],
            'OPR' => ['name' => 'Opera', 'pattern' => '/OPR\/([0-9.]+)/'],
            'MSIE' => ['name' => 'Internet Explorer', 'pattern' => '/MSIE ([0-9.]+)/'],
            'Trident' => ['name' => 'Internet Explorer', 'pattern' => '/rv:([0-9.]+)/'],
        ];

        foreach ($browsers as $key => $browser) {
            if (stripos($userAgent, $key) !== false) {
                preg_match($browser['pattern'], $userAgent, $matches);
                return [
                    'name' => $browser['name'],
                    'version' => $matches[1] ?? null,
                ];
            }
        }

        return ['name' => 'unknown', 'version' => null];
    }

    /**
     * Detect operating system from user agent
     */
    protected static function detectOS(string $userAgent): array
    {
        $os_array = [
            '/windows nt 10/i' => ['name' => 'Windows', 'version' => '10'],
            '/windows nt 11/i' => ['name' => 'Windows', 'version' => '11'],
            '/windows nt 6.3/i' => ['name' => 'Windows', 'version' => '8.1'],
            '/windows nt 6.2/i' => ['name' => 'Windows', 'version' => '8'],
            '/windows nt 6.1/i' => ['name' => 'Windows', 'version' => '7'],
            '/windows nt 6.0/i' => ['name' => 'Windows', 'version' => 'Vista'],
            '/windows nt 5.2/i' => ['name' => 'Windows', 'version' => 'Server 2003/XP x64'],
            '/windows nt 5.1/i' => ['name' => 'Windows', 'version' => 'XP'],
            '/windows xp/i' => ['name' => 'Windows', 'version' => 'XP'],
            '/macintosh|mac os x/i' => ['name' => 'macOS', 'version' => null],
            '/mac_powerpc/i' => ['name' => 'Mac OS', 'version' => '9'],
            '/linux/i' => ['name' => 'Linux', 'version' => null],
            '/ubuntu/i' => ['name' => 'Ubuntu', 'version' => null],
            '/iphone/i' => ['name' => 'iOS', 'version' => null],
            '/ipod/i' => ['name' => 'iOS', 'version' => null],
            '/ipad/i' => ['name' => 'iOS', 'version' => null],
            '/android/i' => ['name' => 'Android', 'version' => null],
            '/blackberry/i' => ['name' => 'BlackBerry', 'version' => null],
            '/webos/i' => ['name' => 'Mobile', 'version' => null],
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $userAgent)) {
                // Try to extract version for macOS
                if ($value['name'] === 'macOS') {
                    preg_match('/Mac OS X (10[._]\d+)/', $userAgent, $matches);
                    $value['version'] = $matches[1] ?? null;
                    if ($value['version']) {
                        $value['version'] = str_replace('_', '.', $value['version']);
                    }
                }
                
                // Try to extract version for Android
                if ($value['name'] === 'Android') {
                    preg_match('/Android (\d+\.\d+)/', $userAgent, $matches);
                    $value['version'] = $matches[1] ?? null;
                }
                
                // Try to extract version for iOS
                if ($value['name'] === 'iOS') {
                    preg_match('/OS (\d+_\d+)/', $userAgent, $matches);
                    $value['version'] = isset($matches[1]) ? str_replace('_', '.', $matches[1]) : null;
                }

                return $value;
            }
        }

        return ['name' => 'unknown', 'version' => null];
    }

    /**
     * Get browser language from Accept-Language header
     */
    public static function detectLanguage(?string $acceptLanguage): ?string
    {
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header (e.g., "fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7")
        $languages = explode(',', $acceptLanguage);
        $firstLanguage = $languages[0] ?? null;

        if ($firstLanguage) {
            // Extract the main language code (e.g., "fr-FR" -> "fr-FR")
            return explode(';', $firstLanguage)[0];
        }

        return null;
    }
}
