<?php

namespace App\Models\PSGC_new;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Q12026 extends Model
{
    protected $table = "q1-2026";
    protected $primaryKey = "id";
    public $timestamps = true;

    /**
     * Geographic Level Constants
     */
    const REGION = "Reg";
    const PROVINCE = "Prov";
    const CITY = "City";
    const MUNICIPALITY = "Mun";
    const SUB_MUNICIPALITY = "SubMun";
    const BARANGAY = "Bgy";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "psgc_code",
        "name",
        "correspondence_code",
        "geographic_level",
        "old_name",
        "city_classification",
        "income_classification",
        "urban_rural",
        "population",
        "region_code",
        "province_code",
        "mun_city_code",
        "barangay_code",
        "mun_city_identifier",
        "barangay_identifier",
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = ["created_at", "updated_at"];

    /**
     * Scope to filter by geographic level.
     */
    public function scopeByGeographicLevel($query, $level)
    {
        return $query->where("geographic_level", $level);
    }

    /**
     * Scope to filter regions.
     */
    public function scopeRegions($query)
    {
        return $query->where("geographic_level", "Reg");
    }

    /**
     * Scope to filter provinces.
     */
    public function scopeProvinces($query)
    {
        return $query->where("geographic_level", "Prov");
    }

    /**
     * Scope to filter cities.
     */
    public function scopeCities($query)
    {
        return $query->where("geographic_level", "City");
    }

    /**
     * Scope to filter municipalities.
     */
    public function scopeMunicipalities($query)
    {
        return $query->where("geographic_level", "Mun");
    }

    /**
     * Scope to filter sub-municipalities.
     */
    public function scopeSubMunicipalities($query)
    {
        return $query->where("geographic_level", "SubMun");
    }

    /**
     * Scope to filter barangays.
     */
    public function scopeBarangays($query)
    {
        return $query->where("geographic_level", "Bgy");
    }

    /**
     * Scope to filter by region code.
     */
    public function scopeByRegionCode($query, $regionCode)
    {
        return $query->where("region_code", $regionCode);
    }

    /**
     * Scope to filter by province code.
     */
    public function scopeByProvinceCode($query, $provinceCode)
    {
        return $query->where("province_code", $provinceCode);
    }

    /**
     * Scope to filter by municipality/city code.
     */
    public function scopeByMunCityCode($query, $munCityCode)
    {
        return $query->where("mun_city_code", $munCityCode);
    }

    /**
     * Scope to filter by barangay code.
     */
    public function scopeByBarangayCode($query, $barangayCode)
    {
        return $query->where("barangay_code", $barangayCode);
    }

    /**
     * Scope to filter by PSGC code.
     */
    public function scopeByPsgcCode($query, $psgcCode)
    {
        return $query->where("psgc_code", $psgcCode);
    }

    /**
     * Scope to search by name.
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where("name", "like", "%" . $name . "%");
    }

    /**
     * Scope to filter by city classification.
     */
    public function scopeByCityClassification($query, $classification)
    {
        return $query->where("city_classification", $classification);
    }

    /**
     * Scope to filter by income classification.
     */
    public function scopeByIncomeClassification($query, $classification)
    {
        return $query->where("income_classification", $classification);
    }

    /**
     * Scope to filter by urban/rural status.
     */
    public function scopeByUrbanRural($query, $type)
    {
        return $query->where("urban_rural", $type);
    }

    /**
     * Scope to get distinct values for a column.
     */
    public function scopeDistinctValues($query, $column)
    {
        return $query->whereNotNull($column)->distinct()->pluck($column);
    }

    /**
     * Get all unique geographic levels.
     */
    public static function getGeographicLevels()
    {
        return static::whereNotNull("geographic_level")
            ->distinct()
            ->pluck("geographic_level")
            ->toArray();
    }

    /**
     * Get all unique city classifications.
     */
    public static function getCityClassifications()
    {
        return static::whereNotNull("city_classification")
            ->distinct()
            ->pluck("city_classification")
            ->toArray();
    }

    /**
     * Get all unique income classifications.
     */
    public static function getIncomeClassifications()
    {
        return static::whereNotNull("income_classification")
            ->distinct()
            ->pluck("income_classification")
            ->toArray();
    }

    /**
     * Get all unique urban/rural classifications.
     */
    public static function getUrbanRuralClassifications()
    {
        return static::whereNotNull("urban_rural")
            ->distinct()
            ->pluck("urban_rural")
            ->toArray();
    }

    // ==================== PSGC CODE EXTRACTION HELPERS ====================

    /**
     * Extract region code from PSGC code (positions 1-2)
     * Example: "1380100001" -> "13"
     */
    public static function extractRegionCode($psgcCode)
    {
        return substr($psgcCode, 0, 2);
    }

    /**
     * Extract province code from PSGC code (positions 3-5)
     * Example: "1380100001" -> "801"
     */
    public static function extractProvinceCode($psgcCode)
    {
        return substr($psgcCode, 2, 3);
    }

    /**
     * Extract municipality/city code from PSGC code (positions 6-7)
     * Example: "1380100001" -> "00"
     */
    public static function extractMunCityCode($psgcCode)
    {
        return substr($psgcCode, 5, 2);
    }

    /**
     * Extract barangay code from PSGC code (positions 8-10)
     * Example: "1380100001" -> "001"
     */
    public static function extractBarangayCode($psgcCode)
    {
        return substr($psgcCode, 7, 3);
    }

    /**
     * Get the parent region of this record
     */
    public function getParentRegion()
    {
        if (!$this->psgc_code || strlen($this->psgc_code) < 2) {
            return null;
        }

        $regionCode = self::extractRegionCode($this->psgc_code);
        return self::regions()->where("region_code", $regionCode)->first();
    }

    /**
     * Get the parent province of this record
     */
    public function getParentProvince()
    {
        if (!$this->psgc_code || strlen($this->psgc_code) < 5) {
            return null;
        }

        $provinceCode = self::extractProvinceCode($this->psgc_code);
        return self::provinces()
            ->where("province_code", $provinceCode)
            ->first();
    }

    /**
     * Get the parent city/municipality of this record (if it's a barangay)
     */
    public function getParentMunCity()
    {
        if (!$this->psgc_code || strlen($this->psgc_code) < 7) {
            return null;
        }

        $munCityCode = self::extractMunCityCode($this->psgc_code);
        $regionCode = self::extractRegionCode($this->psgc_code);

        return self::where(function ($query) {
            $query
                ->where("geographic_level", self::CITY)
                ->orWhere("geographic_level", self::MUNICIPALITY);
        })
            ->where("mun_city_code", $munCityCode)
            ->where("region_code", $regionCode)
            ->first();
    }

    /**
     * Get all children records (provinces, cities, municipalities, or barangays depending on level)
     */
    public function getChildren()
    {
        switch ($this->geographic_level) {
            case self::REGION:
                return self::provinces()
                    ->byRegionCode($this->region_code)
                    ->get();

            case self::PROVINCE:
                return self::where(function ($query) {
                    $query
                        ->where("geographic_level", self::CITY)
                        ->orWhere("geographic_level", self::MUNICIPALITY);
                })
                    ->byProvinceCode($this->province_code)
                    ->get();

            case self::CITY:
            case self::MUNICIPALITY:
                return self::barangays()
                    ->byMunCityCode($this->mun_city_code)
                    ->get();

            default:
                return collect();
        }
    }

    /**
     * Check if a given PSGC code is a child of this record
     */
    public function isChildOf($parentPsgcCode)
    {
        if (!$this->psgc_code || strlen($this->psgc_code) < 2) {
            return false;
        }

        // Check region code (positions 1-2)
        $parentRegion = self::extractRegionCode($parentPsgcCode);
        $thisRegion = self::extractRegionCode($this->psgc_code);

        if ($thisRegion !== $parentRegion) {
            return false;
        }

        // If parent is at least a province level, check province code (positions 3-5)
        if (strlen($parentPsgcCode) >= 5) {
            $parentProvince = self::extractProvinceCode($parentPsgcCode);
            $thisProvince = self::extractProvinceCode($this->psgc_code);
            if ($thisProvince !== $parentProvince) {
                return false;
            }
        }

        // If parent is at least municipality/city level, check municipality/city code (positions 6-7)
        if (strlen($parentPsgcCode) >= 7) {
            $parentMunCity = self::extractMunCityCode($parentPsgcCode);
            $thisMunCity = self::extractMunCityCode($this->psgc_code);
            if ($thisMunCity !== $parentMunCity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Scope to get records by region extracted from PSGC code
     */
    public function scopeByPsgcRegion($query, $psgcCode)
    {
        $regionCode = self::extractRegionCode($psgcCode);
        return $query->byRegionCode($regionCode);
    }

    /**
     * Scope to get records by province extracted from PSGC code
     */
    public function scopeByPsgcProvince($query, $psgcCode)
    {
        $provinceCode = self::extractProvinceCode($psgcCode);
        return $query->byProvinceCode($provinceCode);
    }

    /**
     * Scope to get records by municipality/city extracted from PSGC code
     */
    public function scopeByPsgcMunCity($query, $psgcCode)
    {
        $munCityCode = self::extractMunCityCode($psgcCode);
        return $query->byMunCityCode($munCityCode);
    }
}
