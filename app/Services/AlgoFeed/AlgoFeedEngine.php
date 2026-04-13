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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AlgoFeedEngine — Full 14-Module Feed Generation Pipeline
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
        $this->momentEngine      = new MomentFramingEngine();
        $this->assemblyEngine    = new FeedAssemblyEngine();
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
            $candidates = $this->loader->load();

            if ($candidates->isEmpty()) {
                return ['feed' => [], 'session_id' => $this->sessionId, 'meta' => ['count' => 0]];
            }

            // ── M1: Eligibility Engine — hard gates ────────────────────────────────
            $candidates = $this->eligibilityEngine->run($candidates, $ctx);

            $eligible = $candidates->where('is_eligible', true);

            if ($eligible->isEmpty()) {
                return ['feed' => [], 'session_id' => $this->sessionId, 'meta' => ['count' => 0]];
            }

            // ── M3: Structural Importance Engine — no upstream dependencies ──────────
            $eligible = $this->structuralEngine->run($eligible, $ctx);

            // ── M7: Route Engine — run early so distance_km is set for all below ───
            $eligible = $this->routeEngine->run($eligible, $ctx);

            // ── M2: Context Engine — compute context fit scores ────────────────────
            $eligible = $this->contextEngine->run($eligible, $ctx);

            // ── M4: Opportunity Engine — uses distance_km (now populated) ─────────
            $eligible = $this->opportunityEngine->run($eligible, $ctx);

            // ── M5: Action Horizon Engine — uses distance_km + opportunity scores ──
            $eligible = $this->horizonEngine->run($eligible, $ctx);

            // ── M6: Role Assignment Engine — uses all upstream scores ─────────────
            $eligible = $this->roleEngine->run($eligible, $ctx);

            // ── M8: Sequence Planner — build slot skeleton ────────────────────────
            $sequencePlan = $this->sequenceEngine->plan($eligible, $ctx);

            // ── M9: Slot Ranking Engine — fill each slot with best candidate ───────
            $rankedSlots = $this->slotRankingEngine->rank($eligible, $sequencePlan, $ctx);

            // ── M10: Moment Framing Engine — "why now" labels ─────────────────────
            $framedSlots = $this->momentEngine->frame($rankedSlots, $ctx);

            // ── M11: Feed Assembly & Memory Layer — diversity + final selection ────
            $feed = $this->assemblyEngine->assemble($framedSlots, $eligible, $ctx);

            // Persist session state for future memory
            $this->persistSessionState($ctx, count($feed));

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

    private function persistSessionState(array $ctx, int $feedCount): void
    {
        try {
            DB::table('feed_session_state')->updateOrInsert(
                ['session_id' => $this->sessionId],
                [
                    'user_id'             => $this->userId,
                    'trip_id'             => $this->tripId,
                    'current_feed_position' => $feedCount,
                    'cards_rendered_count'  => $feedCount,
                    'last_feed_refresh_at'  => now(),
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('AlgoFeedEngine: could not persist session state', ['error' => $e->getMessage()]);
        }
    }
}
