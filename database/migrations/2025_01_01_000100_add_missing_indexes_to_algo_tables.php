<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add missing indexes to all AlgoFeed system tables for optimal query performance.
     * This migration adds:
     * - Spatial bounding box indexes (lat/lng) for distance queries
     * - Composite indexes for common query patterns
     * - Score column indexes for sorting/filtering
     */
    public function up(): void
    {
        // ============================================
        // ENTITY TABLES - Core AlgoFeed Tables
        // ============================================

        // entity_structural_base - Critical for distance queries and candidate loading
        DB::statement('ALTER TABLE entity_structural_base ADD INDEX idx_location_coords (location_id, lat, lng)');
        DB::statement('ALTER TABLE entity_structural_base ADD INDEX idx_category (category)');
        DB::statement('ALTER TABLE entity_structural_base ADD INDEX idx_popularity (popularity_score DESC)');
        DB::statement('ALTER TABLE entity_structural_base ADD INDEX idx_landmark (is_landmark_manual, aggregate_rating DESC)');

        // entity_structural_importance - Used for anchor detection and importance scoring
        DB::statement('ALTER TABLE entity_structural_importance ADD INDEX idx_intrinsic_score (intrinsic_importance_score DESC)');
        DB::statement('ALTER TABLE entity_structural_importance ADD INDEX idx_anchor_score (composite_anchor_score DESC)');
        DB::statement('ALTER TABLE entity_structural_importance ADD INDEX idx_trip_value (trip_value_score DESC)');
        DB::statement('ALTER TABLE entity_structural_importance ADD INDEX idx_location_class (location_id, structural_class)');
        DB::statement('ALTER TABLE entity_structural_importance ADD INDEX idx_cluster (destination_cluster_id)');

        // entity_context_affinity - Already has good indexes, adding a few more
        DB::statement('ALTER TABLE entity_context_affinity ADD INDEX idx_role_score (entity_context_role, context_affinity_score DESC)');
        DB::statement('ALTER TABLE entity_context_affinity ADD INDEX idx_active (is_active, context_affinity_score DESC)');

        // entity_opportunity_profile - For time/weather matching
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_sunrise (sunrise_relevance_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_sunset (sunset_relevance_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_morning (morning_relevance_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_evening (evening_relevance_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_night (night_relevance_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_dinner (dinner_fit_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_lunch (lunch_fit_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_indoor (indoor_suitability_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_profile ADD INDEX idx_outdoor (outdoor_dependency_score DESC)');

        // entity_operational_windows - For time-based eligibility
        DB::statement('ALTER TABLE entity_operational_windows ADD INDEX idx_active_window (active_flag, window_type, window_start)');

        // entity_opportunity_state - Session-based opportunity scoring
        DB::statement('ALTER TABLE entity_opportunity_state ADD INDEX idx_opportunity_score (opportunity_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_state ADD INDEX idx_opportunity_active (opportunity_active, opportunity_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_state ADD INDEX idx_temporal_score (temporal_opportunity_score DESC)');
        DB::statement('ALTER TABLE entity_opportunity_state ADD INDEX idx_need_state_score (need_state_opportunity_score DESC)');

        // entity_action_horizon_priors - For horizon assignment
        DB::statement('ALTER TABLE entity_action_horizon_priors ADD INDEX idx_booking_urgency (base_booking_urgency_score DESC)');
        DB::statement('ALTER TABLE entity_action_horizon_priors ADD INDEX idx_time_window (base_time_window_strength_score DESC)');

        // entity_time_affinity - For time-based scoring (was missing indexes)
        DB::statement('ALTER TABLE entity_time_affinity ADD INDEX idx_sunrise_time (sunrise_score DESC)');
        DB::statement('ALTER TABLE entity_time_affinity ADD INDEX idx_morning_time (morning_score DESC)');
        DB::statement('ALTER TABLE entity_time_affinity ADD INDEX idx_sunset_time (sunset_score DESC)');
        DB::statement('ALTER TABLE entity_time_affinity ADD INDEX idx_evening_time (evening_score DESC)');
        DB::statement('ALTER TABLE entity_time_affinity ADD INDEX idx_night_time (night_score DESC)');

        // entity_weather_affinity - For weather-based scoring (was missing indexes)
        DB::statement('ALTER TABLE entity_weather_affinity ADD INDEX idx_clear_weather (clear_score DESC)');
        DB::statement('ALTER TABLE entity_weather_affinity ADD INDEX idx_rainy_weather (rainy_score DESC)');
        DB::statement('ALTER TABLE entity_weather_affinity ADD INDEX idx_hot_weather (hot_score DESC)');
        DB::statement('ALTER TABLE entity_weather_affinity ADD INDEX idx_cold_weather (cold_score DESC)');

        // entity_horizon_metadata - For horizon decisions (was missing indexes)
        DB::statement('ALTER TABLE entity_horizon_metadata ADD INDEX idx_effort (effort_level)');
        DB::statement('ALTER TABLE entity_horizon_metadata ADD INDEX idx_reservation (reservation_need_level)');
        DB::statement('ALTER TABLE entity_horizon_metadata ADD INDEX idx_scarcity (scarcity_level)');
        DB::statement('ALTER TABLE entity_horizon_metadata ADD INDEX idx_landmark_level (landmark_level)');
        DB::statement('ALTER TABLE entity_horizon_metadata ADD INDEX idx_planning (planning_relevance_level)');

        // entity_geo_profile - For geo queries
        DB::statement('ALTER TABLE entity_geo_profile ADD INDEX idx_geo_hash_8 (geo_hash_8)');
        DB::statement('ALTER TABLE entity_geo_profile ADD INDEX idx_walkability (walkability_score DESC)');
        DB::statement('ALTER TABLE entity_geo_profile ADD INDEX idx_transit (nearest_transit_distance_m)');

        // entity_route_edges - For route planning
        DB::statement('ALTER TABLE entity_route_edges ADD INDEX idx_distance (straight_line_distance_m)');
        DB::statement('ALTER TABLE entity_route_edges ADD INDEX idx_walk_duration (route_duration_walk_min)');
        DB::statement('ALTER TABLE entity_route_edges ADD INDEX idx_drive_duration (route_duration_drive_min)');
        DB::statement('ALTER TABLE entity_route_edges ADD INDEX idx_same_cluster (same_local_cluster, straight_line_distance_m)');
        DB::statement('ALTER TABLE entity_route_edges ADD INDEX idx_coherence (route_coherence_prior DESC)');

        // ============================================
        // FEED TABLES - Session & Output Tables
        // ============================================

        // feed_entities - Core feed entity lookup
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_anchor (anchor_attraction, popularity_score DESC)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_food (food, rating DESC)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_experience (experience, popularity_score DESC)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_item_type_pop (item_type, popularity_score DESC)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_night_compat (night_compatible)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_rain_friendly (rain_friendly)');
        DB::statement('ALTER TABLE feed_entities ADD INDEX idx_hero (hero_potential, popularity_score DESC)');

        // feed_session_state - Session management
        DB::statement('ALTER TABLE feed_session_state ADD INDEX idx_user (user_id)');
        DB::statement('ALTER TABLE feed_session_state ADD INDEX idx_trip (trip_id)');
        DB::statement('ALTER TABLE feed_session_state ADD INDEX idx_anchor (current_anchor_id, current_anchor_type)');
        DB::statement('ALTER TABLE feed_session_state ADD INDEX idx_updated (updated_at)');

        // feed_exposure_memory - Already has good indexes, adding more
        DB::statement('ALTER TABLE feed_exposure_memory ADD INDEX idx_last_shown (last_shown_at)');
        DB::statement('ALTER TABLE feed_exposure_memory ADD INDEX idx_entity_type (entity_type, entity_id)');
        DB::statement('ALTER TABLE feed_exposure_memory ADD INDEX idx_clicked (was_clicked, clicked_at)');
        DB::statement('ALTER TABLE feed_exposure_memory ADD INDEX idx_saved (was_saved, saved_at)');

        // feed_slot_candidates - Slot ranking
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_slot_score (slot_score_final DESC)');
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_eligible_score (is_eligible, slot_score_final DESC)');
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_anchor_area (anchor_id, area_id)');
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_do_now (do_now_score DESC)');
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_do_soon (do_soon_score DESC)');
        DB::statement('ALTER TABLE feed_slot_candidates ADD INDEX idx_trip_important (trip_important_score DESC)');

        // feed_candidate_memory_features - Memory scoring
        DB::statement('ALTER TABLE feed_candidate_memory_features ADD INDEX idx_novelty (novelty_score DESC)');
        DB::statement('ALTER TABLE feed_candidate_memory_features ADD INDEX idx_repetition (repetition_risk_score DESC)');
        DB::statement('ALTER TABLE feed_candidate_memory_features ADD INDEX idx_cooldown (cooldown_penalty_score DESC)');

        // trip_feed_candidates - Trip-specific candidates
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_entity (entity_type, entity_id)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_landmark_flag (is_landmark, open_now_score DESC)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_restaurant_flag (is_restaurant, open_now_score DESC)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_open_now (open_now_score DESC)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_distance (distance_meters)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_detour (detour_cost_minutes)');
        DB::statement('ALTER TABLE trip_feed_candidates ADD INDEX idx_route_alignment (route_alignment_score DESC)');

        // environment_state - Environment context
        DB::statement('ALTER TABLE environment_state ADD INDEX idx_weather (weather_type)');
        DB::statement('ALTER TABLE environment_state ADD INDEX idx_location_daypart (location_id, daypart)');
        DB::statement('ALTER TABLE environment_state ADD INDEX idx_updated (last_updated_at)');

        // ============================================
        // SOURCE TABLES - Core Sight/Restaurant/Experience
        // ============================================

        // Sight - Critical for distance queries (if not already indexed)
        try {
            DB::statement('ALTER TABLE Sight ADD INDEX idx_lat_lng (Latitude, Longitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE Sight ADD INDEX idx_mustsee_lat_lng (IsMustSee, Latitude, Longitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE Sight ADD INDEX idx_location_lat_lng (LocationId, Latitude, Longitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE Sight ADD INDEX idx_tier (tier, ReviewCount DESC)');
        } catch (\Exception $e) {}

        // Restaurant - For food recommendations
        try {
            DB::statement('ALTER TABLE Restaurant ADD INDEX idx_lat_lng (Latitude, Longitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE Restaurant ADD INDEX idx_location_lat_lng (LocationId, Latitude, Longitude)');
        } catch (\Exception $e) {}

        // Experience - For experience recommendations
        try {
            DB::statement('ALTER TABLE Experience ADD INDEX idx_lat_lng (Latitude, Longitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE Experience ADD INDEX idx_location_lat_lng (LocationId, Latitude, Longitude)');
        } catch (\Exception $e) {}

        // TPHotel - For hotel recommendations
        try {
            DB::statement('ALTER TABLE TPHotel ADD INDEX idx_lat_lng (Latitude, longnitude)');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE TPHotel ADD INDEX idx_location_lat_lng (location_id, Latitude, longnitude)');
        } catch (\Exception $e) {}

        // Location - For nearby city queries
        try {
            DB::statement('ALTER TABLE Location ADD INDEX idx_lat_lng (Lat, Longitude)');
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        // Entity tables
        DB::statement('ALTER TABLE entity_structural_base DROP INDEX idx_location_coords');
        DB::statement('ALTER TABLE entity_structural_base DROP INDEX idx_category');
        DB::statement('ALTER TABLE entity_structural_base DROP INDEX idx_popularity');
        DB::statement('ALTER TABLE entity_structural_base DROP INDEX idx_landmark');

        DB::statement('ALTER TABLE entity_structural_importance DROP INDEX idx_intrinsic_score');
        DB::statement('ALTER TABLE entity_structural_importance DROP INDEX idx_anchor_score');
        DB::statement('ALTER TABLE entity_structural_importance DROP INDEX idx_trip_value');
        DB::statement('ALTER TABLE entity_structural_importance DROP INDEX idx_location_class');
        DB::statement('ALTER TABLE entity_structural_importance DROP INDEX idx_cluster');

        DB::statement('ALTER TABLE entity_context_affinity DROP INDEX idx_role_score');
        DB::statement('ALTER TABLE entity_context_affinity DROP INDEX idx_active');

        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_sunrise');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_sunset');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_morning');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_evening');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_night');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_dinner');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_lunch');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_indoor');
        DB::statement('ALTER TABLE entity_opportunity_profile DROP INDEX idx_outdoor');

        DB::statement('ALTER TABLE entity_operational_windows DROP INDEX idx_active_window');

        DB::statement('ALTER TABLE entity_opportunity_state DROP INDEX idx_opportunity_score');
        DB::statement('ALTER TABLE entity_opportunity_state DROP INDEX idx_opportunity_active');
        DB::statement('ALTER TABLE entity_opportunity_state DROP INDEX idx_temporal_score');
        DB::statement('ALTER TABLE entity_opportunity_state DROP INDEX idx_need_state_score');

        DB::statement('ALTER TABLE entity_action_horizon_priors DROP INDEX idx_booking_urgency');
        DB::statement('ALTER TABLE entity_action_horizon_priors DROP INDEX idx_time_window');

        DB::statement('ALTER TABLE entity_time_affinity DROP INDEX idx_sunrise_time');
        DB::statement('ALTER TABLE entity_time_affinity DROP INDEX idx_morning_time');
        DB::statement('ALTER TABLE entity_time_affinity DROP INDEX idx_sunset_time');
        DB::statement('ALTER TABLE entity_time_affinity DROP INDEX idx_evening_time');
        DB::statement('ALTER TABLE entity_time_affinity DROP INDEX idx_night_time');

        DB::statement('ALTER TABLE entity_weather_affinity DROP INDEX idx_clear_weather');
        DB::statement('ALTER TABLE entity_weather_affinity DROP INDEX idx_rainy_weather');
        DB::statement('ALTER TABLE entity_weather_affinity DROP INDEX idx_hot_weather');
        DB::statement('ALTER TABLE entity_weather_affinity DROP INDEX idx_cold_weather');

        DB::statement('ALTER TABLE entity_horizon_metadata DROP INDEX idx_effort');
        DB::statement('ALTER TABLE entity_horizon_metadata DROP INDEX idx_reservation');
        DB::statement('ALTER TABLE entity_horizon_metadata DROP INDEX idx_scarcity');
        DB::statement('ALTER TABLE entity_horizon_metadata DROP INDEX idx_landmark_level');
        DB::statement('ALTER TABLE entity_horizon_metadata DROP INDEX idx_planning');

        DB::statement('ALTER TABLE entity_geo_profile DROP INDEX idx_geo_hash_8');
        DB::statement('ALTER TABLE entity_geo_profile DROP INDEX idx_walkability');
        DB::statement('ALTER TABLE entity_geo_profile DROP INDEX idx_transit');

        DB::statement('ALTER TABLE entity_route_edges DROP INDEX idx_distance');
        DB::statement('ALTER TABLE entity_route_edges DROP INDEX idx_walk_duration');
        DB::statement('ALTER TABLE entity_route_edges DROP INDEX idx_drive_duration');
        DB::statement('ALTER TABLE entity_route_edges DROP INDEX idx_same_cluster');
        DB::statement('ALTER TABLE entity_route_edges DROP INDEX idx_coherence');

        // Feed tables
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_anchor');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_food');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_experience');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_item_type_pop');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_night_compat');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_rain_friendly');
        DB::statement('ALTER TABLE feed_entities DROP INDEX idx_hero');

        DB::statement('ALTER TABLE feed_session_state DROP INDEX idx_user');
        DB::statement('ALTER TABLE feed_session_state DROP INDEX idx_trip');
        DB::statement('ALTER TABLE feed_session_state DROP INDEX idx_anchor');
        DB::statement('ALTER TABLE feed_session_state DROP INDEX idx_updated');

        DB::statement('ALTER TABLE feed_exposure_memory DROP INDEX idx_last_shown');
        DB::statement('ALTER TABLE feed_exposure_memory DROP INDEX idx_entity_type');
        DB::statement('ALTER TABLE feed_exposure_memory DROP INDEX idx_clicked');
        DB::statement('ALTER TABLE feed_exposure_memory DROP INDEX idx_saved');

        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_slot_score');
        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_eligible_score');
        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_anchor_area');
        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_do_now');
        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_do_soon');
        DB::statement('ALTER TABLE feed_slot_candidates DROP INDEX idx_trip_important');

        DB::statement('ALTER TABLE feed_candidate_memory_features DROP INDEX idx_novelty');
        DB::statement('ALTER TABLE feed_candidate_memory_features DROP INDEX idx_repetition');
        DB::statement('ALTER TABLE feed_candidate_memory_features DROP INDEX idx_cooldown');

        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_entity');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_landmark_flag');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_restaurant_flag');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_open_now');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_distance');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_detour');
        DB::statement('ALTER TABLE trip_feed_candidates DROP INDEX idx_route_alignment');

        DB::statement('ALTER TABLE environment_state DROP INDEX idx_weather');
        DB::statement('ALTER TABLE environment_state DROP INDEX idx_location_daypart');
        DB::statement('ALTER TABLE environment_state DROP INDEX idx_updated');
    }
};
