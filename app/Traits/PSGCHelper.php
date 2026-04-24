<?php

namespace App\Traits;

class PSGCHelper
{
    /**
     * PSGC Code Structure (10 digits):
     * Positions 1-2:   Region code
     * Positions 3-5:   Province code
     * Positions 6-7:   Municipality/City code
     * Positions 8-10:  Barangay code
     *
     * Identifiers:
     * Positions 3-7:   City/Municipality identifier
     * Positions 3-10:  Barangay identifier
     */

    /**
     * Extract region code from PSGC code (positions 1-2)
     * Example: "1380100001" -> "13"
     */
    public static function extractRegionCode($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 2) {
            return null;
        }
        return substr($psgcCode, 0, 2);
    }

    /**
     * Extract province code from PSGC code (positions 3-5)
     * Example: "1380100001" -> "801"
     */
    public static function extractProvinceCode($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 5) {
            return null;
        }
        return substr($psgcCode, 2, 3);
    }

    /**
     * Extract municipality/city code from PSGC code (positions 6-7)
     * Example: "1380100001" -> "00"
     */
    public static function extractMunCityCode($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 7) {
            return null;
        }
        return substr($psgcCode, 5, 2);
    }

    /**
     * Extract barangay code from PSGC code (positions 8-10)
     * Example: "1380100001" -> "001"
     */
    public static function extractBarangayCode($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 10) {
            return null;
        }
        return substr($psgcCode, 7, 3);
    }

    /**
     * Extract municipality/city identifier from PSGC code (positions 3-7)
     * Example: "1380100001" -> "80100"
     */
    public static function extractMunCityIdentifier($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 7) {
            return null;
        }
        return substr($psgcCode, 2, 5);
    }

    /**
     * Extract barangay identifier from PSGC code (positions 3-10)
     * Example: "1380100001" -> "80100001"
     */
    public static function extractBarangayIdentifier($psgcCode)
    {
        if (!$psgcCode || strlen($psgcCode) < 10) {
            return null;
        }
        return substr($psgcCode, 2, 8);
    }

    /**
     * Get the geographic level based on PSGC code structure
     */
    public static function getGeographicLevelFromCode($psgcCode)
    {
        if (!$psgcCode) {
            return null;
        }

        $length = strlen($psgcCode);

        // Region level (only region code filled, rest zeros)
        if ($length >= 2) {
            $regionCode = self::extractRegionCode($psgcCode);
            if ($regionCode === substr($psgcCode, 0, 2)) {
                // Check if rest are zeros
                $restOfCode = substr($psgcCode, 2);
                if ($restOfCode === str_repeat('0', strlen($restOfCode))) {
                    return 'Reg';
                }
            }
        }

        // Province level (region + province filled, rest zeros)
        if ($length >= 5) {
            $provinceCode = self::extractProvinceCode($psgcCode);
            if ($provinceCode !== null) {
                $restOfCode = substr($psgcCode, 5);
                if ($restOfCode === str_repeat('0', strlen($restOfCode))) {
                    return 'Prov';
                }
            }
        }

        // Municipality/City level (region + province + mun_city filled, rest zeros)
        if ($length >= 7) {
            $munCityCode = self::extractMunCityCode($psgcCode);
            if ($munCityCode !== null) {
                $restOfCode = substr($psgcCode, 7);
                if ($restOfCode === str_repeat('0', strlen($restOfCode))) {
                    return 'Mun/City';
                }
            }
        }

        // Barangay level (all codes filled)
        if ($length >= 10) {
            $barangayCode = self::extractBarangayCode($psgcCode);
            if ($barangayCode !== null && $barangayCode !== '000') {
                return 'Bgy';
            }
        }

        return null;
    }

    /**
     * Check if PSGC code is complete (10 digits)
     */
    public static function isCompletePsgcCode($psgcCode)
    {
        return $psgcCode && strlen($psgcCode) === 10 && is_numeric($psgcCode);
    }

    /**
     * Validate PSGC code format (must be 10 digits)
     */
    public static function isValidPsgcCode($psgcCode)
    {
        return $psgcCode && strlen($psgcCode) === 10 && is_numeric($psgcCode);
    }

    /**
     * Check if a child PSGC code belongs to a parent PSGC code
     */
    public static function isChildOf($childPsgcCode, $parentPsgcCode)
    {
        if (!self::isValidPsgcCode($childPsgcCode) || !self::isValidPsgcCode($parentPsgcCode)) {
            return false;
        }

        // Check region code (positions 1-2)
        if (substr($childPsgcCode, 0, 2) !== substr($parentPsgcCode, 0, 2)) {
            return false;
        }

        // Check province code (positions 3-5)
        if (substr($childPsgcCode, 2, 3) !== substr($parentPsgcCode, 2, 3)) {
            return false;
        }

        // Check municipality/city code (positions 6-7)
        if (substr($childPsgcCode, 5, 2) !== substr($parentPsgcCode, 5, 2)) {
            return false;
        }

        // If all match, child belongs to parent
        return true;
    }

    /**
     * Build complete PSGC code from its parts
     */
    public static function buildPsgcCode($regionCode, $provinceCode = '000', $munCityCode = '00', $barangayCode = '000')
    {
        return str_pad($regionCode, 2, '0', STR_PAD_LEFT) .
               str_pad($provinceCode, 3, '0', STR_PAD_LEFT) .
               str_pad($munCityCode, 2, '0', STR_PAD_LEFT) .
               str_pad($barangayCode, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get parent PSGC code (one level up)
     */
    public static function getParentPsgcCode($psgcCode)
    {
        if (!self::isValidPsgcCode($psgcCode)) {
            return null;
        }

        $regionCode = self::extractRegionCode($psgcCode);
        $provinceCode = self::extractProvinceCode($psgcCode);
        $munCityCode = self::extractMunCityCode($psgcCode);

        // If it's a barangay, return municipality/city level
        if ($munCityCode !== '00') {
            return self::buildPsgcCode($regionCode, $provinceCode, $munCityCode);
        }

        // If it's a municipality/city, return province level
        if ($provinceCode !== '000') {
            return self::buildPsgcCode($regionCode, $provinceCode);
        }

        // If it's a province, return region level
        return self::buildPsgcCode($regionCode);
    }

    /**
     * Get all hierarchy codes for a PSGC code (from region to the record itself)
     */
    public static function getHierarchyCodes($psgcCode)
    {
        if (!self::isValidPsgcCode($psgcCode)) {
            return [];
        }

        $hierarchy = [];
        $currentCode = $psgcCode;

        while ($currentCode !== null && strlen($currentCode) === 10) {
            $hierarchy[] = $currentCode;
            $currentCode = self::getParentPsgcCode($currentCode);

            // Prevent infinite loop
            if (in_array($currentCode, $hierarchy)) {
                break;
            }
        }

        return array_reverse($hierarchy);
    }

    /**
     * Format PSGC code with hyphens for readability
     * Example: "1380100001" -> "13-801-00-001"
     */
    public static function formatPsgcCodeForReadability($psgcCode)
    {
        if (!self::isValidPsgcCode($psgcCode)) {
            return $psgcCode;
        }

        return substr($psgcCode, 0, 2) . '-' .
               substr($psgcCode, 2, 3) . '-' .
               substr($psgcCode, 5, 2) . '-' .
               substr($psgcCode, 7, 3);
    }
}
