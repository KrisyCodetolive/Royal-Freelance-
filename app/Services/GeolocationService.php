<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    protected array $africanCountries = [
        // Afrique de l'Ouest (Francophone)
        'SN' => ['name' => 'Sénégal', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Free Money', 'Wave']],
        'CI' => ['name' => 'Côte d\'Ivoire', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'MTN Money', 'Moov Money']],
        'BF' => ['name' => 'Burkina Faso', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Coris Money']],
        'ML' => ['name' => 'Mali', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Malitel Money']],
        'TG' => ['name' => 'Togo', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Flooz', 'T-Money']],
        'BJ' => ['name' => 'Bénin', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['MTN Money', 'Moov Money']],
        'NE' => ['name' => 'Niger', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Airtel Money']],
        'GW' => ['name' => 'Guinée-Bissau', 'currency' => 'XOF', 'region' => 'West Africa', 'language' => 'French', 'mobile_money' => ['Orange Money']],

        // Afrique Centrale (Francophone)
        'CM' => ['name' => 'Cameroun', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'MTN Money']],
        'GA' => ['name' => 'Gabon', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Airtel Money', 'Moov Money']],
        'CG' => ['name' => 'Congo', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Airtel Money']],
        'CF' => ['name' => 'République Centrafricaine', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Orange Money']],
        'TD' => ['name' => 'Tchad', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Airtel Money', 'Tigo Cash']],
        'GQ' => ['name' => 'Guinée Équatoriale', 'currency' => 'XAF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Orange Money']],

        // Autres pays africains importants
        'MA' => ['name' => 'Maroc', 'currency' => 'MAD', 'region' => 'North Africa', 'language' => 'French/Arabic', 'mobile_money' => ['Orange Money', 'Inwi Money']],
        'TN' => ['name' => 'Tunisie', 'currency' => 'TND', 'region' => 'North Africa', 'language' => 'French/Arabic', 'mobile_money' => ['Ooredoo Money']],
        'DZ' => ['name' => 'Algérie', 'currency' => 'DZD', 'region' => 'North Africa', 'language' => 'French/Arabic', 'mobile_money' => ['CIB Mobile']],
        'MG' => ['name' => 'Madagascar', 'currency' => 'MGA', 'region' => 'East Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Airtel Money']],
        'CD' => ['name' => 'RD Congo', 'currency' => 'CDF', 'region' => 'Central Africa', 'language' => 'French', 'mobile_money' => ['Orange Money', 'Airtel Money', 'Vodacom M-Pesa']],
        'RW' => ['name' => 'Rwanda', 'currency' => 'RWF', 'region' => 'East Africa', 'language' => 'French/English', 'mobile_money' => ['MTN Money', 'Airtel Money']],
        'BI' => ['name' => 'Burundi', 'currency' => 'BIF', 'region' => 'East Africa', 'language' => 'French', 'mobile_money' => ['Ecocash']],

        // Pays anglophones (pour référence)
        'NG' => ['name' => 'Nigeria', 'currency' => 'NGN', 'region' => 'West Africa', 'language' => 'English', 'mobile_money' => ['PalmPay', 'OPay', 'Kuda']],
        'GH' => ['name' => 'Ghana', 'currency' => 'GHS', 'region' => 'West Africa', 'language' => 'English', 'mobile_money' => ['MTN Money', 'Vodafone Cash']],
        'KE' => ['name' => 'Kenya', 'currency' => 'KES', 'region' => 'East Africa', 'language' => 'English', 'mobile_money' => ['M-Pesa', 'Airtel Money']],
    ];

    protected array $cityEconomicData = [
        // Sénégal
        'Dakar' => ['gdp_per_capita' => 1800, 'internet_penetration' => 75, 'mobile_penetration' => 120, 'business_environment' => 8.5],
        // Côte d'Ivoire  
        'Abidjan' => ['gdp_per_capita' => 2100, 'internet_penetration' => 70, 'mobile_penetration' => 135, 'business_environment' => 8.2],
        // Cameroun
        'Douala' => ['gdp_per_capita' => 1900, 'internet_penetration' => 65, 'mobile_penetration' => 110, 'business_environment' => 7.8],
        'Yaoundé' => ['gdp_per_capita' => 2200, 'internet_penetration' => 72, 'mobile_penetration' => 115, 'business_environment' => 8.0],
        // Maroc
        'Casablanca' => ['gdp_per_capita' => 3500, 'internet_penetration' => 85, 'mobile_penetration' => 140, 'business_environment' => 9.0],
        'Rabat' => ['gdp_per_capita' => 3200, 'internet_penetration' => 80, 'mobile_penetration' => 135, 'business_environment' => 8.8],
        // RD Congo
        'Kinshasa' => ['gdp_per_capita' => 800, 'internet_penetration' => 35, 'mobile_penetration' => 85, 'business_environment' => 6.5],
        // Madagascar
        'Antananarivo' => ['gdp_per_capita' => 1200, 'internet_penetration' => 45, 'mobile_penetration' => 95, 'business_environment' => 7.0],
    ];

    /**
     * Get detailed location information from IP
     */
    public function getLocationFromIP(string $ip): array
    {
        // Use cache to avoid excessive API calls
        $cacheKey = "geolocation_" . md5($ip);

        // Check if cached (for logging purposes)
        $isCached = Cache::has($cacheKey);

        if ($isCached) {
            $cachedData = Cache::get($cacheKey);
            Log::info('🌍 [GEOLOCATION] Using cached data', [
                'ip' => $ip,
                'country' => $cachedData['country_code'] ?? 'N/A',
                'city' => $cachedData['city'] ?? 'N/A',
                'cached' => true,
            ]);
            return $cachedData;
        }

        // Not cached, perform fresh lookup
        Log::info('🌍 [GEOLOCATION] Starting fresh IP lookup', ['ip' => $ip]);

        $locationData = $this->performIpLookup($ip);

        // Cache for 1 hour (reduced from 24h for faster updates)
        Cache::put($cacheKey, $locationData, now()->addHour());

        return $locationData;
    }

    /**
     * Perform the actual IP lookup with fallback
     */
    protected function performIpLookup(string $ip): array
    {
        // Try primary API: ipapi.co (1000/day)
        $data = $this->tryIpapiCo($ip);
        if ($data) {
            Log::info('🌍 [GEOLOCATION] ipapi.co success', [
                'ip' => $ip,
                'country' => $data['country_code'] ?? 'N/A',
                'city' => $data['city'] ?? 'N/A',
            ]);
            return $this->enrichLocationData($data);
        }

        // Fallback 1: ip-api.com (45 req/min)
        $data = $this->tryIpApi($ip);
        if ($data) {
            Log::info('🌍 [GEOLOCATION] ip-api.com success', [
                'ip' => $ip,
                'country' => $data['country_code'] ?? 'N/A',
                'city' => $data['city'] ?? 'N/A',
            ]);
            return $this->enrichLocationData($data);
        }

        // Fallback 2: ipwho.is (unlimited, no key required)
        $data = $this->tryIpWhois($ip);
        if ($data) {
            Log::info('🌍 [GEOLOCATION] ipwho.is success', [
                'ip' => $ip,
                'country' => $data['country_code'] ?? 'N/A',
                'city' => $data['city'] ?? 'N/A',
            ]);
            return $this->enrichLocationData($data);
        }

        // Fallback 3: freeipapi.com (60 req/min)
        $data = $this->tryFreeIpApi($ip);
        if ($data) {
            Log::info('🌍 [GEOLOCATION] freeipapi.com success', [
                'ip' => $ip,
                'country' => $data['country_code'] ?? 'N/A',
                'city' => $data['city'] ?? 'N/A',
            ]);
            return $this->enrichLocationData($data);
        }

        // All APIs failed
        Log::warning('🌍 [GEOLOCATION] All APIs failed, using fallback', ['ip' => $ip]);
        return $this->getBasicLocationData($ip);
    }

    /**
     * Try ipapi.co API (primary - 1000 requests/day free)
     */
    protected function tryIpapiCo(string $ip): ?array
    {
        try {
            Log::info('🌍 [GEOLOCATION] Trying ipapi.co...', ['ip' => $ip]);

            $response = Http::timeout(10)->get("https://ipapi.co/{$ip}/json/");

            Log::info('🌍 [GEOLOCATION] ipapi.co response', [
                'ip' => $ip,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check for API error (rate limit, quota exceeded)
                if (isset($data['error']) && $data['error'] === true) {
                    Log::warning('🌍 [GEOLOCATION] ipapi.co API error', [
                        'ip' => $ip,
                        'reason' => $data['reason'] ?? 'Unknown',
                        'message' => $data['message'] ?? 'No message',
                    ]);
                    return null;
                }

                // Check if essential data is present
                if (!empty($data['country_code'])) {
                    return $data;
                }

                Log::warning('🌍 [GEOLOCATION] ipapi.co missing country_code', [
                    'ip' => $ip,
                    'data' => $data,
                ]);
            } else {
                Log::warning('🌍 [GEOLOCATION] ipapi.co HTTP error', [
                    'ip' => $ip,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('🌍 [GEOLOCATION] ipapi.co exception', [
                'ip' => $ip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    /**
     * Try ip-api.com API (fallback - 45 requests/min, unlimited daily)
     */
    protected function tryIpApi(string $ip): ?array
    {
        try {
            Log::info('🌍 [GEOLOCATION] Trying ip-api.com fallback...', ['ip' => $ip]);

            // ip-api.com uses different field names, we normalize them
            $response = Http::timeout(10)->get("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,currency,isp,org");

            Log::info('🌍 [GEOLOCATION] ip-api.com response', [
                'ip' => $ip,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 200),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check for API error
                if (($data['status'] ?? '') === 'fail') {
                    Log::warning('🌍 [GEOLOCATION] ip-api.com API error', [
                        'ip' => $ip,
                        'message' => $data['message'] ?? 'Unknown error',
                    ]);
                    return null;
                }

                // Normalize field names to match ipapi.co format
                return [
                    'ip' => $ip,
                    'country_code' => $data['countryCode'] ?? null,
                    'country_name' => $data['country'] ?? null,
                    'region' => $data['regionName'] ?? $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'postal' => $data['zip'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                    'timezone' => $data['timezone'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'org' => $data['org'] ?? null,
                    'isp' => $data['isp'] ?? null,
                ];
            } else {
                Log::warning('🌍 [GEOLOCATION] ip-api.com HTTP error', [
                    'ip' => $ip,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('🌍 [GEOLOCATION] ip-api.com exception', [
                'ip' => $ip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    /**
     * Try ipwho.is API (unlimited, no key required)
     */
    protected function tryIpWhois(string $ip): ?array
    {
        try {
            Log::info('🌍 [GEOLOCATION] Trying ipwho.is...', ['ip' => $ip]);

            $response = Http::timeout(10)->get("https://ipwho.is/{$ip}");

            Log::info('🌍 [GEOLOCATION] ipwho.is response', [
                'ip' => $ip,
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check for API error
                if (($data['success'] ?? true) === false) {
                    Log::warning('🌍 [GEOLOCATION] ipwho.is API error', [
                        'ip' => $ip,
                        'message' => $data['message'] ?? 'Unknown error',
                    ]);
                    return null;
                }

                // Normalize field names
                return [
                    'ip' => $ip,
                    'country_code' => $data['country_code'] ?? null,
                    'country_name' => $data['country'] ?? null,
                    'region' => $data['region'] ?? null,
                    'city' => $data['city'] ?? null,
                    'postal' => $data['postal'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'timezone' => $data['timezone']['id'] ?? null,
                    'currency' => $data['currency']['code'] ?? null,
                    'isp' => $data['connection']['isp'] ?? null,
                ];
            } else {
                Log::warning('🌍 [GEOLOCATION] ipwho.is HTTP error', [
                    'ip' => $ip,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('🌍 [GEOLOCATION] ipwho.is exception', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Try freeipapi.com API (60 req/min)
     */
    protected function tryFreeIpApi(string $ip): ?array
    {
        try {
            Log::info('🌍 [GEOLOCATION] Trying freeipapi.com...', ['ip' => $ip]);

            $response = Http::timeout(10)->get("https://freeipapi.com/api/json/{$ip}");

            Log::info('🌍 [GEOLOCATION] freeipapi.com response', [
                'ip' => $ip,
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Normalize field names
                return [
                    'ip' => $ip,
                    'country_code' => $data['countryCode'] ?? null,
                    'country_name' => $data['countryName'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['cityName'] ?? null,
                    'postal' => $data['zipCode'] ?? null,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null,
                    'timezone' => $data['timeZone'] ?? null,
                    'currency' => $data['currency']['code'] ?? null,
                ];
            } else {
                Log::warning('🌍 [GEOLOCATION] freeipapi.com HTTP error', [
                    'ip' => $ip,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('🌍 [GEOLOCATION] freeipapi.com exception', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Enrich location data with African market insights
     */
    protected function enrichLocationData(array $basicData): array
    {
        $countryCode = $basicData['country_code'] ?? null;
        $city = $basicData['city'] ?? null;

        $enriched = [
            'ip' => $basicData['ip'] ?? '',
            'country_code' => $countryCode,
            'country_name' => $basicData['country_name'] ?? '',
            'region' => $basicData['region'] ?? '',
            'city' => $city,
            'postal' => $basicData['postal'] ?? '',
            'latitude' => $basicData['latitude'] ?? null,
            'longitude' => $basicData['longitude'] ?? null,
            'timezone' => $basicData['timezone'] ?? '',
            'currency' => $basicData['currency'] ?? '',
            'languages' => $basicData['languages'] ?? '',

            // African market specific data
            'is_african_market' => false,
            'african_region' => null,
            'target_currency' => null,
            'mobile_money_providers' => [],
            'market_potential' => null,
            'recommended_pricing' => null,
            'economic_indicators' => null,
        ];

        // Enrich with African market data
        if ($countryCode && isset($this->africanCountries[$countryCode])) {
            $countryData = $this->africanCountries[$countryCode];

            $enriched['is_african_market'] = true;
            $enriched['african_region'] = $countryData['region'];
            $enriched['target_currency'] = $countryData['currency'];
            $enriched['mobile_money_providers'] = $countryData['mobile_money'];
            $enriched['primary_language'] = $countryData['language'];

            // Add city-specific economic data
            if ($city && isset($this->cityEconomicData[$city])) {
                $enriched['economic_indicators'] = $this->cityEconomicData[$city];
                $enriched['market_potential'] = $this->calculateMarketPotential($countryData, $this->cityEconomicData[$city]);
                $enriched['recommended_pricing'] = $this->getRecommendedPricing($countryData['currency'], $this->cityEconomicData[$city]);
            }
        }

        return $enriched;
    }

    /**
     * Calculate market potential score (1-10)
     */
    protected function calculateMarketPotential(array $countryData, array $cityData): float
    {
        $score = 0;

        // GDP per capita weight (40%)
        $gdpScore = min(10, ($cityData['gdp_per_capita'] / 4000) * 10);
        $score += $gdpScore * 0.4;

        // Internet penetration weight (30%)
        $internetScore = ($cityData['internet_penetration'] / 100) * 10;
        $score += $internetScore * 0.3;

        // Mobile penetration weight (20%)
        $mobileScore = min(10, ($cityData['mobile_penetration'] / 150) * 10);
        $score += $mobileScore * 0.2;

        // Business environment weight (10%)
        $businessScore = $cityData['business_environment'];
        $score += $businessScore * 0.1;

        return round($score, 1);
    }

    /**
     * Get recommended pricing for the market
     */
    protected function getRecommendedPricing(string $currency, array $cityData): array
    {
        $basePriceUSD = 97; // Base price in USD

        // Adjust based on local purchasing power
        $purchasingPowerMultiplier = $cityData['gdp_per_capita'] / 2000; // Normalize to ~2000 USD
        $adjustedPrice = $basePriceUSD * $purchasingPowerMultiplier;

        // Currency specific adjustments
        $localPrice = match ($currency) {
            'XOF' => $adjustedPrice * 580,  // CFA Franc BCEAO
            'XAF' => $adjustedPrice * 580,  // CFA Franc BEAC
            'MAD' => $adjustedPrice * 10,   // Moroccan Dirham
            'TND' => $adjustedPrice * 3,    // Tunisian Dinar
            'DZD' => $adjustedPrice * 135,  // Algerian Dinar
            'MGA' => $adjustedPrice * 4000, // Malagasy Ariary
            'CDF' => $adjustedPrice * 2000, // Congolese Franc
            default => $adjustedPrice,
        };

        return [
            'original_usd' => $basePriceUSD,
            'recommended_local' => round($localPrice),
            'currency' => $currency,
            'discount_percentage' => round((1 - $purchasingPowerMultiplier) * 100),
            'purchasing_power_note' => $this->getPurchasingPowerNote($purchasingPowerMultiplier),
        ];
    }

    /**
     * Get purchasing power note for pricing strategy
     */
    protected function getPurchasingPowerNote(float $multiplier): string
    {
        return match (true) {
            $multiplier >= 1.5 => 'Marché premium - Prix standard possible',
            $multiplier >= 1.0 => 'Marché standard - Prix légèrement ajusté',
            $multiplier >= 0.7 => 'Marché émergent - Prix réduit recommandé',
            $multiplier >= 0.4 => 'Marché en développement - Prix fortement réduit',
            default => 'Marché à faible pouvoir d\'achat - Stratégie freemium recommandée',
        };
    }

    /**
     * Update lead location information
     */
    public function updateLeadLocation(Lead $lead, ?string $ip = null): Lead
    {
        $ip = $ip ?: request()->ip();

        Log::info('🌍 [GEOLOCATION] updateLeadLocation called', [
            'lead_id' => $lead->id,
            'ip' => $ip,
        ]);

        if (!$ip || $ip === '127.0.0.1') {
            Log::info('🌍 [GEOLOCATION] Skipping localhost IP', ['ip' => $ip]);
            return $lead;
        }

        $locationData = $this->getLocationFromIP($ip);

        // Ne remplis que si c'est vide pour respecter les données saisies manuellement dans le formulaire
        $lead->country ??= ($locationData['country_code'] ?? null);
        $lead->city ??= ($locationData['city'] ?? null);
        $lead->region ??= ($locationData['region'] ?? null);
        $lead->timezone ??= ($locationData['timezone'] ?? null);
        $lead->latitude ??= ($locationData['latitude'] ?? null);
        $lead->longitude ??= ($locationData['longitude'] ?? null);
        $lead->ip_address = $ip;

        $lead->custom_fields = array_merge($lead->custom_fields ?? [], [
            'location_data' => $locationData,
            'market_insights' => $this->getMarketInsights($locationData),
        ]);

        $lead->save();

        return $lead->refresh();
    }

    /**
     * Get market insights for dashboard
     */
    public function getMarketInsights(array $locationData): array
    {
        if (!$locationData['is_african_market']) {
            return ['type' => 'international', 'priority' => 'medium'];
        }

        $insights = [
            'type' => 'african_market',
            'priority' => 'high',
            'region' => $locationData['african_region'],
            'currency' => $locationData['target_currency'],
            'mobile_money' => $locationData['mobile_money_providers'][0] ?? null,
            'recommended_approach' => $this->getRecommendedApproach($locationData),
        ];

        if ($locationData['market_potential']) {
            $insights['market_potential'] = $locationData['market_potential'];
            $insights['potential_level'] = match (true) {
                $locationData['market_potential'] >= 8 => 'excellent',
                $locationData['market_potential'] >= 6 => 'good',
                $locationData['market_potential'] >= 4 => 'moderate',
                default => 'emerging',
            };
        }

        return $insights;
    }

    /**
     * Get recommended marketing approach
     */
    protected function getRecommendedApproach(array $locationData): string
    {
        $currency = $locationData['target_currency'];
        $region = $locationData['african_region'];

        return match (true) {
            $currency === 'XOF' && $region === 'West Africa' => 'french_west_africa_strategy',
            $currency === 'XAF' && $region === 'Central Africa' => 'french_central_africa_strategy',
            $currency === 'MAD' => 'maghreb_strategy',
            $locationData['primary_language'] === 'French' => 'francophone_africa_strategy',
            default => 'general_africa_strategy',
        };
    }

    /**
     * Get basic location data as fallback
     */
    protected function getBasicLocationData(string $ip): array
    {
        return [
            'ip' => $ip,
            'country_code' => null,
            'country_name' => 'Unknown',
            'city' => null,
            'region' => null,
            'postal' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'currency' => null,
            'languages' => null,

            // African market defaults
            'is_african_market' => false,
            'african_region' => null,
            'target_currency' => null,
            'mobile_money_providers' => [],
            'primary_language' => null,
            'market_potential' => null,
            'recommended_pricing' => null,
            'economic_indicators' => null,

            'detection_method' => 'fallback',
        ];
    }

    /**
     * Get analytics for African markets
     */
    public function getAfricanMarketAnalytics(\Illuminate\Database\Eloquent\Builder $leadsQuery): array
    {
        $leads = $leadsQuery->whereJsonContains('custom_fields->location_data->is_african_market', true)->get();

        $analytics = [
            'total_african_leads' => $leads->count(),
            'by_country' => [],
            'by_region' => [],
            'by_currency' => [],
            'market_potential_avg' => 0,
            'top_cities' => [],
            'mobile_money_distribution' => [],
        ];

        foreach ($leads as $lead) {
            $locationData = $lead->custom_fields['location_data'] ?? [];

            // By country
            $country = $locationData['country_name'] ?? 'Unknown';
            $analytics['by_country'][$country] = ($analytics['by_country'][$country] ?? 0) + 1;

            // By region
            $region = $locationData['african_region'] ?? 'Unknown';
            $analytics['by_region'][$region] = ($analytics['by_region'][$region] ?? 0) + 1;

            // By currency
            $currency = $locationData['target_currency'] ?? 'Unknown';
            $analytics['by_currency'][$currency] = ($analytics['by_currency'][$currency] ?? 0) + 1;

            // Cities
            $city = $locationData['city'] ?? 'Unknown';
            if ($city !== 'Unknown') {
                $analytics['top_cities'][$city] = ($analytics['top_cities'][$city] ?? 0) + 1;
            }

            // Mobile money
            if (!empty($locationData['mobile_money_providers'])) {
                foreach ($locationData['mobile_money_providers'] as $provider) {
                    $analytics['mobile_money_distribution'][$provider] = ($analytics['mobile_money_distribution'][$provider] ?? 0) + 1;
                }
            }
        }

        // Sort and limit results
        arsort($analytics['by_country']);
        arsort($analytics['by_region']);
        arsort($analytics['by_currency']);
        arsort($analytics['top_cities']);
        arsort($analytics['mobile_money_distribution']);

        $analytics['top_cities'] = array_slice($analytics['top_cities'], 0, 10, true);

        return $analytics;
    }

    /**
     * Get conversion recommendations based on location
     */
    public function getConversionRecommendations(array $locationData): array
    {
        if (!$locationData['is_african_market']) {
            return ['type' => 'standard'];
        }

        $recommendations = [
            'payment_methods' => $locationData['mobile_money_providers'] ?? [],
            'currency' => $locationData['target_currency'],
            'language' => $locationData['primary_language'] ?? 'French',
        ];

        if ($locationData['recommended_pricing']) {
            $recommendations['pricing'] = $locationData['recommended_pricing'];
        }

        if ($locationData['market_potential']) {
            $recommendations['urgency_level'] = match (true) {
                $locationData['market_potential'] >= 8 => 'high_urgency',
                $locationData['market_potential'] >= 6 => 'medium_urgency',
                default => 'low_urgency',
            };
        }

        return $recommendations;
    }
}
