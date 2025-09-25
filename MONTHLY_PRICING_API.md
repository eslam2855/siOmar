# Monthly Pricing API Documentation

## Overview

The SiOmar API has been updated with a best practice monthly pricing system that supports daily rates per calendar month. This allows administrators to set different daily prices for different months, providing flexibility in pricing management while maintaining clear daily rate calculations.

## Database Schema

### New Table: `unit_month_prices`

The system now uses a dedicated table for monthly pricing:

```sql
CREATE TABLE unit_month_prices (
    id BIGINT PRIMARY KEY,
    unit_id BIGINT FOREIGN KEY REFERENCES units(id),
    year_month VARCHAR(7) COMMENT 'YYYY-MM format for the month',
    daily_price DECIMAL(10,2) COMMENT 'Daily rate for this month',
    currency VARCHAR(3) DEFAULT 'EGP',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(unit_id, year_month),
    INDEX(unit_id, year_month, is_active)
);
```

### Legacy Pricing Table

The existing `pricing` table is maintained for:
- **Fees**: `cleaning_fee`, `security_deposit`
- **Status tracking**: `is_active`, `valid_from`, `valid_to`

**Note**: All legacy pricing fields (base_price, weekend_price, holiday_price, etc.) have been removed in favor of the new monthly pricing system.

## API Response Structure

### Units Index Endpoint (`GET /api/units`)

Returns all units with monthly pricing:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Unit Name",
            // ... other unit fields ...
            "monthly_pricing": [
                {
                    "month": "2025-08",
                    "formatted_month": "August 2025",
                    "daily_price": "2000.00",
                    "currency": "EGP",
                    "is_active": true
                },
                {
                    "month": "2025-09",
                    "formatted_month": "September 2025",
                    "daily_price": "2236.00",
                    "currency": "EGP",
                    "is_active": true
                },
                {
                    "month": "2025-10",
                    "formatted_month": "October 2025",
                    "daily_price": "2554.00",
                    "currency": "EGP",
                    "is_active": true
                }
            ],
            "pricing": {
                "id": 2,
                "unit_id": 1,
                "monthly_pricing": [...], // Same as above
                "cleaning_fee": "0.00",
                "security_deposit": "0.00",
                "is_active": true
            }
        }
    ]
}
```

### Unit Details Endpoint (`GET /api/units/{unit}`)

Returns the same structure as the index endpoint, plus reviews.

### Pricing Calculation Endpoint (`POST /api/units/{unit}/calculate-pricing`)

Calculates total price for a date range with monthly breakdown:

**Request:**
```json
{
    "check_in": "2025-09-20",
    "check_out": "2025-09-25",
    "guests": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "unit_id": 1,
        "unit_name": "Unit Name",
        "check_in": "2025-09-20",
        "check_out": "2025-09-25",
        "guests": 1,
        "nights": 5,
        "pricing_breakdown": {
            "total_price": 11180,
            "cleaning_fee": "0.00",
            "security_deposit": "0.00",
            "grand_total": 11180,
            "breakdown": [
                {
                    "month": "2025-09",
                    "formatted_month": "September 2025",
                    "daily_price": 2236,
                    "nights": 5,
                    "subtotal": 11180
                }
            ],
            "nights": 5
        }
    }
}
```

## Features Implemented

### 1. Monthly Pricing Structure
- **Daily rates per month**: Each month has its own daily price
- **Automatic month calculation**: System automatically calculates current + next 2 months
- **Fallback pricing**: Uses base price when no monthly price is set
- **Currency support**: Each price can have its own currency

### 2. Pricing Resolution Hierarchy
1. **Month-specific price**: From `unit_month_prices` table
2. **No fallback**: Returns null if no monthly price is set for the requested date

### 3. Date Range Calculations
- **Multi-month support**: Automatically splits calculations across months
- **Per-night breakdown**: Shows how many nights in each month
- **Fee inclusion**: Adds cleaning fee and security deposit to total
- **Transparent pricing**: Shows breakdown per month

### 4. Model Enhancements
- **Unit model**: Added `monthPrices()` relationship and pricing calculation methods
- **UnitMonthPrice model**: Dedicated model for monthly pricing with scopes
- **Automatic formatting**: Month names formatted as "August 2025"

## Admin Interface Requirements

The admin interface should include:

1. **Monthly Pricing Management**:
   - Input fields for daily price per month
   - Month display (e.g., "August 2025")
   - Currency selection
   - Active/inactive toggle

2. **Bulk Operations**:
   - "Copy last month" functionality
   - Bulk update for multiple months
   - "Add month" to extend pricing

3. **Validation**:
   - Prevent overlapping month duplicates
   - Require YYYY-MM format
   - Validate daily price ranges

4. **Legacy Fields**:
   - Hide or mark as deprecated
   - Keep for fees and fallback pricing

## Usage Examples

### Getting Units with Monthly Pricing
```bash
GET /api/units
```

### Getting Unit Details
```bash
GET /api/units/1
```

### Calculating Pricing for Date Range
```bash
POST /api/units/1/calculate-pricing
Content-Type: application/json

{
    "check_in": "2025-09-20",
    "check_out": "2025-09-25",
    "guests": 1
}
```

### Checking Availability (includes pricing)
```bash
GET /api/units/1/availability?check_in=2025-09-20&check_out=2025-09-25&guests=1
```

## Migration and Setup

### Running Migrations
```bash
php artisan migrate
```

### Seeding Sample Data
```bash
php artisan db:seed --class=UnitMonthPriceSeeder
```

### Manual Data Update
```sql
-- Add monthly pricing for existing units
INSERT INTO unit_month_prices (unit_id, year_month, daily_price, currency, is_active)
SELECT 
    id as unit_id,
    '2025-08' as year_month,
    2500 as daily_price, -- Set a default daily price
    'EGP' as currency,
    true as is_active
FROM units;
```

## Best Practices

### 1. Data Management
- **Store only needed months**: Current + next 2 months minimum
- **Regular updates**: Monthly job to add next month's pricing
- **Caching**: Cache monthly prices by `unit:{id}:month:{YYYY-MM}`

### 2. Pricing Strategy
- **Seasonal pricing**: Higher rates for peak seasons
- **Advance booking**: Discounts for early bookings
- **Dynamic pricing**: Adjust based on demand

### 3. Edge Cases
- **Missing month price**: Fallback to base price, flag in response
- **Spanning months**: Automatic split calculation
- **Currency conversion**: Support for multiple currencies
- **Rounding**: Consistent 2-decimal precision

## Notes

- **Backward compatibility**: Legacy pricing fields preserved
- **Performance**: Efficient queries with proper indexing
- **Scalability**: Separate table allows for future enhancements
- **Flexibility**: Easy to add new pricing features (seasonal, dynamic, etc.)
- **Transparency**: Clear breakdown of pricing per month
