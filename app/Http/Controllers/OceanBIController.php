<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;

class OceanBIController extends Controller
{
    private function db(): PDO
    {
        $path = database_path('ocean_plastic_dw.db');
        return new PDO('sqlite:' . $path);
    }

    // ── MIS Page ──
    public function mis()
    {
        return view('ocean.mis');
    }

    // ── DSS Page ──
    public function dss()
    {
        return view('ocean.dss');
    }

    // ── API: MIS-01 Tren Produksi ──
    public function apiProduction()
    {
        $db = $this->db();
        $rows = $db->query('SELECT year, plastic_production FROM fact_production_trend ORDER BY year')
                   ->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }

    // ── API: MIS-02 Komposisi Waste (regional, per tahun) ──
    public function apiWasteFate()
    {
        $db = $this->db();
        $rows = $db->query('SELECT entity, year, recycled_share, incinerated_share, mismanaged_share, landfilled_share
                            FROM fact_waste_fate ORDER BY entity, year')
                   ->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }

    // ── API: MIS-03 Top 10 Mismanaged Per Capita ──
    public function apiTopMismanaged()
    {
        $db = $this->db();
        $rows = $db->query("
            SELECT c.entity, c.code, f.mismanaged_per_capita, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE f.mismanaged_per_capita IS NOT NULL AND c.is_country = 1
            ORDER BY f.mismanaged_per_capita DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }

    // ── API: MIS-04 KPI ──
    public function apiKpi()
    {
        $db = $this->db();

        $totalProd = $db->query('SELECT MAX(plastic_production) FROM fact_production_trend')->fetchColumn();
        $avgRecycled = $db->query("SELECT AVG(recycled_share) FROM fact_waste_fate WHERE entity='World'")->fetchColumn();
        $criticalCount = $db->query("SELECT COUNT(*) FROM fact_ocean_pollution WHERE risk_category='Critical'")->fetchColumn();
        $highRisk = $db->query("SELECT COUNT(*) FROM fact_ocean_pollution WHERE risk_category IN ('Critical','High')")->fetchColumn();

        // Top polluter (country-level only)
        $topPolluter = $db->query("
            SELECT c.entity, f.ocean_pollution_share
            FROM fact_ocean_pollution f JOIN dim_country c ON f.country_id=c.country_id
            WHERE f.ocean_pollution_share IS NOT NULL AND c.is_country=1
            ORDER BY f.ocean_pollution_share DESC LIMIT 1
        ")->fetch(PDO::FETCH_ASSOC);

        // Production trend for sparkline (last 10 years)
        $sparkline = $db->query('SELECT year, plastic_production FROM fact_production_trend ORDER BY year DESC LIMIT 10')
                        ->fetchAll(PDO::FETCH_ASSOC);
        $sparkline = array_reverse($sparkline);

        // Avg recycled global (world 2019)
        $worldFate = $db->query("SELECT recycled_share, mismanaged_share, landfilled_share
                                  FROM fact_waste_fate WHERE entity='World' AND year=2019")
                        ->fetch(PDO::FETCH_ASSOC);

        return response()->json([
            'total_production_2019' => $totalProd,
            'avg_recycled' => $worldFate ? round($worldFate['recycled_share'], 2) : null,
            'critical_count' => (int)$criticalCount,
            'high_risk_count' => (int)$highRisk,
            'top_polluter' => $topPolluter,
            'world_mismanaged' => $worldFate ? round($worldFate['mismanaged_share'], 2) : null,
            'world_landfilled' => $worldFate ? round($worldFate['landfilled_share'], 2) : null,
            'sparkline' => $sparkline,
        ]);
    }

    // ── API: GEO-01 Geographic data ──
    public function apiGeo()
    {
        $db = $this->db();
        $indicator = request('indicator', 'ocean_pollution_share');

        $allowed = ['ocean_pollution_share','risk_score','mismanaged_per_capita','recycled_share'];
        if (!in_array($indicator, $allowed)) $indicator = 'ocean_pollution_share';

        $rows = $db->query("
            SELECT c.entity, c.code, f.ocean_pollution_share, f.risk_score,
                   f.mismanaged_per_capita, f.recycled_share, f.risk_category,
                   f.mismanaged_share, f.landfilled_share, f.incinerated_share
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.$indicator IS NOT NULL
            ORDER BY f.$indicator DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        return response()->json([
            'indicator' => $indicator,
            'data' => $rows,
        ]);
    }

    // ── API: DSS-01 Risk Distribution ──
    public function apiRiskDist()
    {
        $db = $this->db();
        $rows = $db->query("
            SELECT r.risk_category, r.color_hex, r.risk_desc,
                   COUNT(f.fact_id) as country_count
            FROM dim_risk r
            LEFT JOIN fact_ocean_pollution f ON f.risk_category = r.risk_category
                AND f.country_id IN (SELECT country_id FROM dim_country WHERE is_country=1)
            GROUP BY r.risk_category
            ORDER BY r.threshold_min DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }

    // ── API: DSS-02 Multivariate Analysis ──
    public function apiMultivariate()
    {
        $db = $this->db();
        $rows = $db->query("
            SELECT c.entity, f.risk_score, f.ocean_pollution_share,
                   f.mismanaged_per_capita, f.recycled_share, f.mismanaged_share,
                   f.incinerated_share, f.landfilled_share, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.risk_score IS NOT NULL
            ORDER BY f.risk_score DESC
            LIMIT 20
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Also get averages per risk category
        $avgByCategory = $db->query("
            SELECT risk_category,
                   ROUND(AVG(ocean_pollution_share),4) as avg_ocean,
                   ROUND(AVG(mismanaged_share),4) as avg_mismanaged,
                   ROUND(AVG(recycled_share),4) as avg_recycled,
                   ROUND(AVG(risk_score),4) as avg_risk
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id=c.country_id
            WHERE c.is_country=1 AND risk_category IS NOT NULL
            GROUP BY risk_category
        ")->fetchAll(PDO::FETCH_ASSOC);

        return response()->json([
            'top20' => $rows,
            'avg_by_category' => $avgByCategory,
        ]);
    }

    // ── API: DSS-03 Top 10 Ocean Polluters ──
    public function apiTopOcean()
    {
        $db = $this->db();
        $rows = $db->query("
            SELECT c.entity, c.code, f.ocean_pollution_share, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE f.ocean_pollution_share IS NOT NULL AND c.is_country = 1
            ORDER BY f.ocean_pollution_share DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }

    // ── API: DSS-04 Priority Countries ──
    public function apiPriority()
    {
        $db = $this->db();
        $rows = $db->query("
            SELECT c.entity, f.ocean_pollution_share, f.mismanaged_share,
                   f.mismanaged_per_capita, f.recycled_share, f.incinerated_share,
                   f.landfilled_share, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.risk_score IS NOT NULL
            ORDER BY f.risk_score DESC
            LIMIT 15
        ")->fetchAll(PDO::FETCH_ASSOC);
        return response()->json($rows);
    }
}