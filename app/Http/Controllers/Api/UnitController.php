<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Services\CacheService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use ApiResponseTrait;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        try {
            $units = $this->cacheService->getActiveUnits();
            
            return response()->json([
                'success' => true,
                'data' => UnitResource::collection($units),
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch units.', 500);
        }
    }

    public function show(Unit $unit)
    {
        try {
            $unit->load(['images', 'amenities', 'unitType', 'reviews.user', 'monthPrices']);
            
            return response()->json([
                'success' => true,
                'data' => new UnitResource($unit),
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch unit details.', 500);
        }
    }

    public function checkAvailability(Request $request, Unit $unit)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'check_in' => 'required|date|after:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1|max:' . $unit->max_guests,
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            // Check if unit is available for the given dates
            $conflictingReservations = $unit->reservations()
                ->where(function ($query) use ($request) {
                    $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                          ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('check_in', '<=', $request->check_in)
                                ->where('check_out', '>=', $request->check_out);
                          });
                })
                ->where('status', '!=', 'cancelled')
                ->exists();

            $isAvailable = !$conflictingReservations && $unit->status === 'available' && $unit->is_active;

            // Calculate pricing for the date range
            $startDate = \Carbon\Carbon::parse($request->check_in);
            $endDate = \Carbon\Carbon::parse($request->check_out);
            $pricingBreakdown = $unit->calculateTotalPriceForRange($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => [
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->name,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'guests' => $request->guests,
                    'is_available' => $isAvailable,
                    'max_guests' => $unit->max_guests,
                    'pricing' => $pricingBreakdown,
                ],
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check availability.', 500);
        }
    }

    public function calculatePricing(Request $request, Unit $unit)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'check_in' => 'required|date|after:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1|max:' . $unit->max_guests,
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            $startDate = \Carbon\Carbon::parse($request->check_in);
            $endDate = \Carbon\Carbon::parse($request->check_out);
            $pricingBreakdown = $unit->calculateTotalPriceForRange($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => [
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->name,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'guests' => $request->guests,
                    'nights' => $pricingBreakdown['nights'],
                    'pricing_breakdown' => $pricingBreakdown,
                ],
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate pricing.', 500);
        }
    }

    /**
     * Search units with various filters
     */
    public function search(Request $request)
    {
        try {
            $query = Unit::with(['unitType', 'images', 'amenities'])
                ->where('is_active', true);

            // Search by name or description
            if ($request->has('q') && !empty($request->q)) {
                $searchTerm = $request->q;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('unitType', function ($typeQuery) use ($searchTerm) {
                          $typeQuery->where('name', 'LIKE', "%{$searchTerm}%");
                      });
                });
            }

            // Filter by unit type
            if ($request->has('unit_type_id')) {
                $query->where('unit_type_id', $request->unit_type_id);
            }

            // Filter by amenities
            if ($request->has('amenities') && is_array($request->amenities)) {
                $query->whereHas('amenities', function ($amenityQuery) use ($request) {
                    $amenityQuery->whereIn('id', $request->amenities);
                });
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('daily_price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('daily_price', '<=', $request->max_price);
            }

            // Filter by guest capacity
            if ($request->has('min_guests')) {
                $query->where('max_guests', '>=', $request->min_guests);
            }

            if ($request->has('max_guests')) {
                $query->where('max_guests', '<=', $request->max_guests);
            }

            // Filter by availability dates
            if ($request->has('check_in') && $request->has('check_out')) {
                $query->whereDoesntHave('reservations', function ($reservationQuery) use ($request) {
                    $reservationQuery->where(function ($q) use ($request) {
                        $q->whereBetween('check_in_date', [$request->check_in, $request->check_out])
                          ->orWhereBetween('check_out_date', [$request->check_in, $request->check_out])
                          ->orWhere(function ($subQ) use ($request) {
                              $subQ->where('check_in_date', '<=', $request->check_in)
                                   ->where('check_out_date', '>=', $request->check_out);
                          });
                    })->whereIn('status', ['pending', 'confirmed', 'active']);
                });
            }

            // Sort results
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['name', 'daily_price', 'max_guests', 'created_at', 'rating'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('name', 'asc');
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

            $units = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => UnitResource::collection($units),
                'pagination' => [
                    'current_page' => $units->currentPage(),
                    'last_page' => $units->lastPage(),
                    'per_page' => $units->perPage(),
                    'total' => $units->total(),
                ],
                'filters' => [
                    'search_term' => $request->get('q'),
                    'unit_type_id' => $request->get('unit_type_id'),
                    'amenities' => $request->get('amenities'),
                    'min_price' => $request->get('min_price'),
                    'max_price' => $request->get('max_price'),
                    'min_guests' => $request->get('min_guests'),
                    'max_guests' => $request->get('max_guests'),
                    'check_in' => $request->get('check_in'),
                    'check_out' => $request->get('check_out'),
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to search units.', 500);
        }
    }
}
