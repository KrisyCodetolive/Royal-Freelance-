<?php

namespace App\Helpers;

class BlockStyleHelper
{
    /**
     * Generate complete CSS style string from styles array
     */
    public static function generateStyle(array $styles, array $defaults = []): string
    {
        $merged = array_merge($defaults, $styles);
        
        $styleMap = [
            // Colors
            'color' => 'color',
            'backgroundColor' => 'background-color',
            
            // Typography
            'fontSize' => 'font-size',
            'fontWeight' => 'font-weight',
            'fontFamily' => 'font-family',
            'letterSpacing' => 'letter-spacing',
            'lineHeight' => 'line-height',
            'textAlign' => 'text-align',
            'textTransform' => 'text-transform',
            
            // Spacing
            'padding' => 'padding',
            'paddingTop' => 'padding-top',
            'paddingRight' => 'padding-right',
            'paddingBottom' => 'padding-bottom',
            'paddingLeft' => 'padding-left',
            'margin' => 'margin',
            'marginTop' => 'margin-top',
            'marginRight' => 'margin-right',
            'marginBottom' => 'margin-bottom',
            'marginLeft' => 'margin-left',
            
            // Borders
            'border' => 'border',
            'borderWidth' => 'border-width',
            'borderStyle' => 'border-style',
            'borderColor' => 'border-color',
            'borderRadius' => 'border-radius',
            
            // Effects
            'boxShadow' => 'box-shadow',
            'opacity' => 'opacity',
            'transform' => 'transform',
            'transition' => 'transition',
            
            // Layout
            'width' => 'width',
            'maxWidth' => 'max-width',
            'minWidth' => 'min-width',
            'height' => 'height',
            'maxHeight' => 'max-height',
            'minHeight' => 'min-height',
            'display' => 'display',
            'position' => 'position',
            'zIndex' => 'z-index',
            
            // Flexbox
            'flexDirection' => 'flex-direction',
            'justifyContent' => 'justify-content',
            'alignItems' => 'align-items',
            'gap' => 'gap',
        ];
        
        $cssProperties = [];
        
        foreach ($styleMap as $key => $cssProperty) {
            if (isset($merged[$key]) && $merged[$key] !== '' && $merged[$key] !== null) {
                $cssProperties[] = "$cssProperty: {$merged[$key]}";
            }
        }
        
        return implode('; ', $cssProperties);
    }
    
    /**
     * Get animation data attribute
     */
    public static function getAnimationAttribute(array $styles): string
    {
        $animation = $styles['animation'] ?? '';
        return $animation ? "data-animation=\"{$animation}\"" : '';
    }
    
    /**
     * Generate wrapper classes for block
     */
    public static function getWrapperClasses(array $styles): string
    {
        $classes = ['block-content'];
        
        // Add responsive classes if needed
        if (isset($styles['responsive']) && $styles['responsive']) {
            $classes[] = 'responsive-block';
        }
        
        return implode(' ', $classes);
    }
    
    /**
     * Parse spacing value (supports shortcuts like "20px 10px")
     */
    public static function parseSpacing(string $value): string
    {
        return $value;
    }
}
