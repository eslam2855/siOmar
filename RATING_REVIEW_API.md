# Rating and Review API Documentation

## Overview

The SiOmar API now includes comprehensive rating and review functionality with support for multiple rating categories and detailed review information.

## Rating Categories

The system supports the following rating categories (1-5 stars each):

- **Overall Rating**: Automatically calculated average of all category ratings
- **Room Rating**: Quality and comfort of the accommodation
- **Service Rating**: Quality of customer service and support
- **Pricing Rating**: Value for money and pricing competitiveness
- **Location Rating**: Convenience and desirability of the location

## API Response Structure

### Units Index Endpoint (`GET /api/units`)

Returns all units with rating statistics (no individual reviews):

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Unit Name",
            // ... other unit fields ...
            "rating_statistics": {
                "averages": {
                    "overall": 4.8,
                    "room": 4.8,
                    "service": 4.8,
                    "pricing": 4.3,
                    "location": 4.3
                },
                "total_reviews": 4
            }
        }
    ]
}
```

### Unit Details Endpoint (`GET /api/units/{unit}`)

Returns detailed unit information including the full reviews list:

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Unit Name",
        // ... other unit fields ...
        "rating_statistics": {
            "averages": {
                "overall": 4.8,
                "room": 4.8,
                "service": 4.8,
                "pricing": 4.3,
                "location": 4.3
            },
            "total_reviews": 4
        },
        "reviews": [
            {
                "id": 1,
                "user": {
                    "id": 4,
                    "name": "John Doe",
                    "profile_image": "path/to/image.jpg"
                },
                "overall_rating": 5,
                "room_rating": 5,
                "service_rating": 4,
                "pricing_rating": 4,
                "location_rating": 5,
                "review_text": "Excellent room with amazing views!",
                "reviewed_at": "2025-08-08T19:34:54.000000Z",
                "created_at": "2025-08-18T19:34:54.000000Z"
            }
        ]
    }
}
```

## Database Schema Changes

### Reviews Table Updates

The `reviews` table has been updated with the following new fields:

- `overall_rating` (integer): Automatically calculated average rating
- `room_rating` (integer, nullable): Room quality rating (1-5)
- `service_rating` (integer, nullable): Service quality rating (1-5)
- `pricing_rating` (integer, nullable): Pricing value rating (1-5)
- `location_rating` (integer, nullable): Location rating (1-5)

The original `rating` field has been renamed to `overall_rating`.

## Features Implemented

### 1. Average Rating Calculation
- Automatic calculation of overall rating from individual category ratings
- Support for partial ratings (users can rate only some categories)
- Rounded to 1 decimal place for consistency

### 2. Rating Statistics
- Average ratings for each category
- Total number of approved reviews
- Available in both unit list and unit details endpoints

### 3. Review Management
- Only approved reviews are included in statistics and lists
- Reviews include user information (name, profile image)
- Timestamps for review creation and review date

### 4. API Response Optimization
- Rating statistics included in unit list for quick overview
- Full review list only included in unit details endpoint
- Efficient database queries with proper relationships

## Usage Examples

### Getting Units with Ratings
```bash
GET /api/units
```

### Getting Unit Details with Reviews
```bash
GET /api/units/1
```

## Migration and Seeding

The system includes:
- Database migration to update the reviews table structure
- Sample data seeder with realistic review examples
- Automatic overall rating calculation on review creation/update

## Notes

- Only approved reviews (`is_approved = true`) are included in statistics
- Overall rating is automatically calculated when individual category ratings are provided
- The system supports backward compatibility with existing review data
- Rating averages are calculated in real-time from the database
