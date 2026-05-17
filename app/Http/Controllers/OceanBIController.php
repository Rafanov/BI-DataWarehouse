<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use PDOException;

class OceanBIController extends Controller
{
    private function db(): PDO
    {
        $path = database_path('ocean_plastic_dw.db');

        if (!file_exists($path)) {
            abort(500, "Database tidak ditemukan: $path — pastikan ocean_plastic_dw.db ada di folder database/");
        }

        return new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private function jsonResponse($data)
    {
        return response()->json($data)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'no-cache');
    }

    // ── MIS Page ──
    public function mis() { return view('ocean.mis'); }

    // ── DSS Page ──
    public function dss() { return view('ocean.dss'); }

    // ── API: MIS-01 Tren Produksi ──
    public function apiProduction()
    {
        $rows = $this->db()
            ->query('SELECT year, plastic_production FROM fact_production_trend ORDER BY year')
            ->fetchAll();
        return $this->jsonResponse($rows);
    }

    // ── API: MIS-02 Komposisi Waste ──
    public function apiWasteFate()
    {
        $rows = $this->db()
            ->query('SELECT entity, year, recycled_share, incinerated_share, mismanaged_share, landfilled_share
                     FROM fact_waste_fate ORDER BY entity, year')
            ->fetchAll();
        return $this->jsonResponse($rows);
    }

    // ── API: MIS-03 Top 10 Mismanaged Per Capita ──
    public function apiTopMismanaged()
    {
        $rows = $this->db()->query("
            SELECT c.entity, c.code, f.mismanaged_per_capita, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE f.mismanaged_per_capita IS NOT NULL AND c.is_country = 1
            ORDER BY f.mismanaged_per_capita DESC
            LIMIT 10
        ")->fetchAll();
        return $this->jsonResponse($rows);
    }

    // ── API: MIS-04 KPI ──
    public function apiKpi()
    {
        $db = $this->db();

        $totalProd = $db->query('SELECT MAX(plastic_production) FROM fact_production_trend')->fetchColumn();

        $topPolluter = $db->query("
            SELECT c.entity, f.ocean_pollution_share
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE f.ocean_pollution_share IS NOT NULL AND c.is_country = 1
            ORDER BY f.ocean_pollution_share DESC LIMIT 1
        ")->fetch();

        $sparkline = array_reverse($db->query(
            'SELECT year, plastic_production FROM fact_production_trend ORDER BY year DESC LIMIT 10'
        )->fetchAll());

        $worldFate = $db->query(
            "SELECT recycled_share, mismanaged_share, landfilled_share
             FROM fact_waste_fate WHERE entity='World' AND year=2019"
        )->fetch();

        return $this->jsonResponse([
            'total_production_2019' => $totalProd,
            'avg_recycled'    => $worldFate ? round($worldFate['recycled_share'], 2) : null,
            'top_polluter'    => $topPolluter ?: null,
            'world_mismanaged'=> $worldFate ? round($worldFate['mismanaged_share'], 2) : null,
            'world_landfilled'=> $worldFate ? round($worldFate['landfilled_share'], 2) : null,
            'sparkline'       => $sparkline,
        ]);
    }

    // ── API: GEO-01 Geographic data ──
    public function apiGeo(Request $request)
    {
        $indicator = $request->get('indicator', 'ocean_pollution_share');
        $allowed   = ['ocean_pollution_share', 'risk_score', 'mismanaged_per_capita', 'recycled_share'];

        if (!in_array($indicator, $allowed)) {
            $indicator = 'ocean_pollution_share';
        }

        $rows = $this->db()->query("
            SELECT c.entity, c.code,
                   f.ocean_pollution_share, f.risk_score, f.mismanaged_per_capita,
                   f.recycled_share, f.risk_category,
                   f.mismanaged_share, f.landfilled_share, f.incinerated_share
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.$indicator IS NOT NULL
            ORDER BY f.$indicator DESC
        ")->fetchAll();

        return $this->jsonResponse(['indicator' => $indicator, 'data' => $rows]);
    }

    // ── API: DSS-01 Risk Distribution ──
    public function apiRiskDist()
    {
        $rows = $this->db()->query("
            SELECT r.risk_category, r.color_hex, r.risk_desc,
                   COUNT(f.fact_id) as country_count
            FROM dim_risk r
            LEFT JOIN fact_ocean_pollution f
                ON f.risk_category = r.risk_category
               AND f.country_id IN (SELECT country_id FROM dim_country WHERE is_country = 1)
            GROUP BY r.risk_category
            ORDER BY r.threshold_min DESC
        ")->fetchAll();
        return $this->jsonResponse($rows);
    }

    // ── API: DSS-02 Multivariate Analysis ──
    public function apiMultivariate()
    {
        $db = $this->db();

        $top20 = $db->query("
            SELECT c.entity, f.risk_score, f.ocean_pollution_share,
                   f.mismanaged_per_capita, f.recycled_share, f.mismanaged_share,
                   f.incinerated_share, f.landfilled_share, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.risk_score IS NOT NULL
            ORDER BY f.risk_score DESC
            LIMIT 20
        ")->fetchAll();

        $avgByCategory = $db->query("
            SELECT f.risk_category,
                   ROUND(AVG(f.ocean_pollution_share), 4) as avg_ocean,
                   ROUND(AVG(f.mismanaged_share), 4)      as avg_mismanaged,
                   ROUND(AVG(f.recycled_share), 4)         as avg_recycled,
                   ROUND(AVG(f.risk_score), 4)             as avg_risk
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.risk_category IS NOT NULL
            GROUP BY f.risk_category
        ")->fetchAll();

        return $this->jsonResponse(['top20' => $top20, 'avg_by_category' => $avgByCategory]);
    }

    // ── API: DSS-03 Top 10 Ocean Polluters ──
    public function apiTopOcean()
    {
        $rows = $this->db()->query("
            SELECT c.entity, c.code, f.ocean_pollution_share, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE f.ocean_pollution_share IS NOT NULL AND c.is_country = 1
            ORDER BY f.ocean_pollution_share DESC
            LIMIT 10
        ")->fetchAll();
        return $this->jsonResponse($rows);
    }

    // ── API: DSS-04 Priority Countries ──
    public function apiPriority()
    {
        $rows = $this->db()->query("
            SELECT c.entity, f.ocean_pollution_share, f.mismanaged_share,
                   f.mismanaged_per_capita, f.recycled_share, f.incinerated_share,
                   f.landfilled_share, f.risk_score, f.risk_category
            FROM fact_ocean_pollution f
            JOIN dim_country c ON f.country_id = c.country_id
            WHERE c.is_country = 1 AND f.risk_score IS NOT NULL
            ORDER BY f.risk_score DESC
            LIMIT 15
        ")->fetchAll();
        return $this->jsonResponse($rows);
    }
}