<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Trait HasSettings
 *
 * Provides JSON settings functionality with helper methods.
 */
trait HasSettings
{
    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->settings ?? [];

        // Support dot notation for nested settings
        $keys = explode('.', $key);
        $value = $settings;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set a specific setting value.
     */
    public function setSetting(string $key, mixed $value): self
    {
        $settings = $this->settings ?? [];

        // Support dot notation for nested settings
        $keys = explode('.', $key);
        $temp = &$settings;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $temp[$segment] = $value;
            } else {
                if (!isset($temp[$segment]) || !is_array($temp[$segment])) {
                    $temp[$segment] = [];
                }
                $temp = &$temp[$segment];
            }
        }

        $this->settings = $settings;
        $this->save();

        return $this;
    }

    /**
     * Remove a specific setting.
     */
    public function removeSetting(string $key): self
    {
        $settings = $this->settings ?? [];

        $keys = explode('.', $key);
        $temp = &$settings;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                unset($temp[$segment]);
            } else {
                if (!isset($temp[$segment])) {
                    return $this;
                }
                $temp = &$temp[$segment];
            }
        }

        $this->settings = $settings;
        $this->save();

        return $this;
    }

    /**
     * Check if a setting exists.
     */
    public function hasSetting(string $key): bool
    {
        return $this->getSetting($key) !== null;
    }

    /**
     * Merge settings with existing ones.
     */
    public function mergeSettings(array $newSettings): self
    {
        $this->settings = array_merge_recursive($this->settings ?? [], $newSettings);
        $this->save();

        return $this;
    }
}
