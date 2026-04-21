<?php

namespace App\Services\AlgoFeed;

use App\Services\AlgoFeed\Engines\EnvironmentStateEngine;
use App\Services\AlgoFeed\Engines\EligibilityEngine;
use App\Services\AlgoFeed\Engines\ContextEngine;
use App\Services\AlgoFeed\Engines\StructuralImportanceEngine;
use App\Services\AlgoFeed\Engines\OpportunityEngine;
use App\Services\AlgoFeed\Engines\ActionHorizonEngine;
use App\Services\AlgoFeed\Engines\RouteEngine;
use App\Services\AlgoFeed\Engines\RoleAssignmentEngine;
use App\Services\AlgoFeed\Engines\SequencePlannerEngine;
use App\Services\AlgoFeed\Engines\SlotRankingEngine;
use App\Services\AlgoFeed\Engines\MomentFramingEngine;
use App\Services\AlgoFeed\Engines\FeedAssemblyEngine;
use App\Services\AlgoFeed\Engines\CardCompositionEngine;
use Illuminate\Support\Facades\Log;

/**
 * AlgoFeedEngine — Full 15-Module Feed Generation Pipeline
 *
 * Modules:
 *   ENV  — Environment State Layer
 *   M1   — Eligibility Engine
 *   M2   — Context Engine
 *   M3   — Structural Importance Engine
 *   M4   — Opportunity Engine
 *   M5   — Action Horizon Engine
 *   M7   — Route Engine
 *   M6   — Role Assignment Engine
 *   M8   — Sequence Planner Engine
 *   M9   — Slot Ranking Engine
 *   M10  — Moment Framing Engine
 *   M11  — Feed Assembly & Memory Layer
 *   M12  — Card Composition Engine (PRIMARY_ENTITY / ENTITY_WITH_CONTEXT / COLLECTION / HYBRID)
 */
class AlgoFeedEngine
{
    protected int    $locationId;
    protected array  $context;
    protected string $sessionId;
    protected ?int   $userId;
    protected ?int   $tripId;

    protected CandidateLoader          $loader;
    protected EnvironmentStateEngine   $envEngine;
    protected EligibilityEngine        $eligibilityEngine;
    protected ContextEngine            $contextEngine;
    protected StructuralImportanceEngine $structuralEngine;
    protected OpportunityEngine        $opportunityEngine;
    protected ActionHorizonEngine      $horizonEngine;
    protected RouteEngine              $routeEngine;
    protected RoleAssignmentEngine     $roleEngine;
    protected SequencePlannerEngine    $sequenceEngine;
    protected SlotRankingEngine        $slotRankingEngine;
    protected MomentFramingEngine      $momentEngine;
    protected FeedAssemblyEngine       $assemblyEngine;
    protected CardCompositionEngine    $cardCompositionEngine;

    public function __construct(int $locationId, array $context = [])
    {
        $this->locationId = $locationId;
        $this->context    = $context;
        $this->sessionId  = $context['session_id'] ?? uniqid('feed_', true);
        $this->userId     = $context['user_id']  ?? null;
        $this->tripId     = $context['trip_id']  ?? null;

        $this->loader            = new CandidateLoader($locationId);
        $this->envEngine         = new EnvironmentStateEngine();
        $this->eligibilityEngine = new EligibilityEngine();
        $this->contextEngine     = new ContextEngine();
        $this->structuralEngine  = new StructuralImportanceEngine();
        $this->opportunityEngine = new OpportunityEngine();
        $this->horizonEngine     = new ActionHorizonEngine();
        $this->routeEngine       = new RouteEngine();
        $this->roleEngine        = new RoleAssignmentEngine();
        $this->sequenceEngine    = new SequencePlannerEngine();
        $this->slotRankingEngine = new SlotRankingEngine();
        $this->momentEngine          = new MomentFramingEngine();
        $this->assemblyEngine        = new FeedAssemblyEngine();
        $this->cardCompositionEngine = new CardCompositionEngine();
    }

    /**
     * Run the full 14-module pipeline and return the rendered feed.
     */
    public function generate(): array
    {
        try {
            // ── ENV: Build environment state snapshot ──────────────────────────────
            $envState = $this->envEngine->compute($this->locationId, $this->context);

            // Merge computed env into context for downstream engines
            $ctx = array_merge($this->context, $envState, [
                'session_id' => $this->sessionId,
                'user_id'    => $this->userId,
                'trip_id'    => $this->tripId,
            ]);

            // ── LOAD: Normalize candidates from Sight/Experience/Restaurant ────────
            $t0 = microtime(true);
            $candidates = $this->loader->load();
            $timings = ['load' => round(microtime(true) - $t0, 3)];

            if ($candidates->isEmpty()) {
                return ['feed' => [], 'session_id' => $this->sessionId, 'meta' => ['count' => 0]];
            }

            // ── M1: Eligibility Engine — hard gates ────────────────────────────────
            $t = microtime(true);
            $candidates = $this->eligibilityEngine->run($candidates, $ctx);
            $timings['M1_eligibility'] = round(microtime(true) - $t, 3);

            $eligible = $candidates->where('is_eligible', true)->values();

            if ($eligible->isEmpty()) {
                return ['feed' => [], 'session_id' => $this->sessionId, 'meta' => ['count' => 0]];
            }

            // ── M3: Structural Importance Engine — no upstream dependencies ──────────
            $t = microtime(true);
            $eligible = $this->structuralEngine->run($eligible, $ctx);
            $timings['M3_structural'] = round(microtime(true) - $t, 3);

            // ── PRE-CAP: Limit to top 200 candidates to keep remaining engines fast ─
            $totalEligible = $eligible->count();
            if ($totalEligible > 200) {
                $eligible = $eligible->sortByDesc('structural_importance_score')->take(200)->values();
            }
            $timings['eligible_count'] = $totalEligible;
            $timings['capped_count']   = $eligible->count();

            // ── M7: Route Engine — run early so distance_km is set for all below ───
            $t = microtime(true);
            $eligible = $this->routeEngine->run($eligible, $ctx);
            $timings['M7_route'] = round(microtime(true) - $t, 3);

            // ── M2: Context Engine — compute context fit scores ────────────────────
            $t = microtime(true);
            $eligible = $this->contextEngine->run($eligible, $ctx);
            $timings['M2_context'] = round(microtime(true) - $t, 3);

            // ── M4: Opportunity Engine — uses distance_km (now populated) ─────────
            $t = microtime(true);
            $eligible = $this->opportunityEngine->run($eligible, $ctx);
            $timings['M4_opportunity'] = round(microtime(true) - $t, 3);

            // ── M5: Action Horizon Engine — uses distance_km + opportunity scores ──
            $t = microtime(true);
            $eligible = $this->horizonEngine->run($eligible, $ctx);
            $timings['M5_horizon'] = round(microtime(true) - $t, 3);

            // ── M6: Role Assignment Engine — uses all upstream scores ─────────────
            $t = microtime(true);
            $eligible = $this->roleEngine->run($eligible, $ctx);
            $timings['M6_roles'] = round(microtime(true) - $t, 3);

            // ── M8: Sequence Planner — build slot skeleton ────────────────────────
            $t = microtime(true);
            $sequencePlan = $this->sequenceEngine->plan($eligible, $ctx);
            $timings['M8_sequence'] = round(microtime(true) - $t, 3);

            // ── M9: Slot Ranking Engine — fill each slot with best candidate ───────
            $t = microtime(true);
            $rankedSlots = $this->slotRankingEngine->rank($eligible, $sequencePlan, $ctx);
            $timings['M9_slot_ranking'] = round(microtime(true) - $t, 3);

            // ── M10: Moment Framing Engine — "why now" labels ─────────────────────
            $t = microtime(true);
            $framedSlots = $this->momentEngine->frame($rankedSlots, $ctx);
            $timings['M10_moment'] = round(microtime(true) - $t, 3);

            // ── M11: Feed Assembly & Memory Layer — diversity + final selection ────
            $t = microtime(true);
            $feed = $this->assemblyEngine->assemble($framedSlots, $eligible, $ctx);
            $timings['M11_assembly'] = round(microtime(true) - $t, 3);

            // ── M12: Card Composition Engine — UI-ready card structures ───────────
            $t = microtime(true);
            $feed = $this->cardCompositionEngine->compose($feed, $ctx);
            $timings['M12_composition'] = round(microtime(true) - $t, 3);

            Log::info('AlgoFeedEngine pipeline timings', $timings);

            return [
                'feed'       => $feed,
                'session_id' => $this->sessionId,
                'meta'       => [
                    'count'        => count($feed),
                    'location_id'  => $this->locationId,
                    'daypart'      => $ctx['daypart'] ?? null,
                    'weather_type' => $ctx['weather_type'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('AlgoFeedEngine::generate failed', [
                'location_id' => $this->locationId,
                'session_id'  => $this->sessionId,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);
            return ['feed' => [], 'session_id' => $this->sessionId, 'meta' => ['error' => $e->getMessage()]];
        }
    }

}
