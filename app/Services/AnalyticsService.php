<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get comprehensive dashboard analytics
     */
    public static function getDashboardAnalytics(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastYear = $now->copy()->subYear();

        return [
            'overview' => self::getOverviewStats(),
            'revenue' => self::getRevenueAnalytics(),
            'reservations' => self::getReservationAnalytics(),
            'units' => self::getUnitAnalytics(),
            'trends' => self::getTrendAnalytics(),
            'performance' => self::getPerformanceMetrics(),
        ];
    }

    /**
     * Get overview statistics
     */
    public static function getOverviewStats(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $currentMonthReservations = Reservation::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $lastMonthReservations = Reservation::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $reservationGrowth = $lastMonthReservations > 0 
            ? (($currentMonthReservations - $lastMonthReservations) / $lastMonthReservations) * 100 
            : 0;

        return [
            'total_reservations' => Reservation::count(),
            'total_revenue' => Reservation::where('status', 'confirmed')->sum('total_amount'),
            'total_units' => Unit::count(),
            'total_users' => User::count(),
            'current_month_reservations' => $currentMonthReservations,
            'reservation_growth_percentage' => round($reservationGrowth, 2),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'active_reservations' => Reservation::where('status', 'active')->count(),
            'confirmed_reservations' => Reservation::where('status', 'confirmed')->count(),
            'completed_reservations' => Reservation::where('status', 'completed')->count(),
            'cancelled_reservations' => Reservation::where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Get revenue analytics
     */
    public static function getRevenueAnalytics(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Monthly revenue for the last 12 months
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $revenue = Reservation::where('status', 'confirmed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'year' => $date->year,
                'month_number' => $date->month,
            ];
        }

        // Current month vs last month
        $currentMonthRevenue = Reservation::where('status', 'confirmed')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('total_amount');

        $lastMonthRevenue = Reservation::where('status', 'confirmed')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('total_amount');

        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return [
            'monthly_revenue' => $monthlyRevenue,
            'current_month_revenue' => $currentMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'revenue_growth_percentage' => round($revenueGrowth, 2),
            'total_revenue' => Reservation::where('status', 'confirmed')->sum('total_amount'),
            'average_reservation_value' => Reservation::where('status', 'confirmed')->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Get reservation analytics
     */
    public static function getReservationAnalytics(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        // Status distribution
        $statusDistribution = Reservation::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Monthly reservations for the last 12 months
        $monthlyReservations = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $count = Reservation::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $monthlyReservations[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
                'year' => $date->year,
                'month_number' => $date->month,
            ];
        }

        // Cancellation rate
        $totalReservations = Reservation::count();
        $cancelledReservations = Reservation::where('status', 'cancelled')->count();
        $cancellationRate = $totalReservations > 0 ? ($cancelledReservations / $totalReservations) * 100 : 0;

        return [
            'status_distribution' => $statusDistribution,
            'monthly_reservations' => $monthlyReservations,
            'cancellation_rate' => round($cancellationRate, 2),
            'average_reservation_duration' => self::getAverageReservationDuration(),
            'peak_booking_months' => self::getPeakBookingMonths(),
        ];
    }

    /**
     * Get unit analytics
     */
    public static function getUnitAnalytics(): array
    {
        // Unit performance
        $unitPerformance = Unit::withCount(['reservations' => function ($query) {
            $query->where('status', 'confirmed');
        }])
        ->withSum(['reservations' => function ($query) {
            $query->where('status', 'confirmed');
        }], 'total_amount')
        ->orderBy('reservations_count', 'desc')
        ->limit(10)
        ->get()
        ->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'reservations_count' => $unit->reservations_count,
                'total_revenue' => $unit->reservations_sum_total_amount ?? 0,
                'average_rating' => $unit->getAverageOverallRating(),
            ];
        });

        // Unit type distribution
        $unitTypeDistribution = Unit::with('unitType')
            ->select('unit_type_id', DB::raw('count(*) as count'))
            ->groupBy('unit_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'unit_type' => $item->unitType->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        return [
            'top_performing_units' => $unitPerformance,
            'unit_type_distribution' => $unitTypeDistribution,
            'total_units' => Unit::count(),
            'available_units' => Unit::where('status', 'available')->count(),
            'occupied_units' => Unit::where('status', 'occupied')->count(),
        ];
    }

    /**
     * Get trend analytics
     */
    public static function getTrendAnalytics(): array
    {
        $now = Carbon::now();
        
        // Weekly trends for the last 8 weeks
        $weeklyTrends = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $now->copy()->subWeeks($i)->endOfWeek();
            
            $reservations = Reservation::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $revenue = Reservation::where('status', 'confirmed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');
            
            $weeklyTrends[] = [
                'week' => $weekStart->format('M d'),
                'reservations' => $reservations,
                'revenue' => $revenue,
            ];
        }

        return [
            'weekly_trends' => $weeklyTrends,
        ];
    }

    /**
     * Get performance metrics
     */
    public static function getPerformanceMetrics(): array
    {
        // Occupancy rate
        $totalUnits = Unit::count();
        $occupiedUnits = Reservation::where('status', 'active')
            ->where('check_in_date', '<=', Carbon::now())
            ->where('check_out_date', '>=', Carbon::now())
            ->count();
        
        $occupancyRate = $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0;

        // Average booking lead time
        $avgLeadTime = Reservation::where('status', 'confirmed')
            ->whereNotNull('created_at')
            ->whereNotNull('check_in_date')
            ->get()
            ->avg(function ($reservation) {
                return Carbon::parse($reservation->created_at)->diffInDays($reservation->check_in_date);
            }) ?? 0;

        return [
            'occupancy_rate' => round($occupancyRate, 2),
            'average_booking_lead_time' => round($avgLeadTime, 1),
            'conversion_rate' => self::getConversionRate(),
        ];
    }

    /**
     * Get average reservation duration
     */
    private static function getAverageReservationDuration(): float
    {
        return Reservation::where('status', 'confirmed')
            ->get()
            ->avg(function ($reservation) {
                return $reservation->check_in_date->diffInDays($reservation->check_out_date);
            }) ?? 0;
    }

    /**
     * Get peak booking months
     */
    private static function getPeakBookingMonths(): array
    {
        return Reservation::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('count', 'desc')
        ->limit(3)
        ->get()
        ->map(function ($item) {
            return [
                'month' => Carbon::create()->month($item->month)->format('F'),
                'count' => $item->count,
            ];
        })
        ->toArray();
    }

    /**
     * Get conversion rate (pending to confirmed)
     */
    private static function getConversionRate(): float
    {
        $totalPending = Reservation::where('status', 'pending')->count();
        $totalConfirmed = Reservation::where('status', 'confirmed')->count();
        
        $total = $totalPending + $totalConfirmed;
        return $total > 0 ? ($totalConfirmed / $total) * 100 : 0;
    }
}
