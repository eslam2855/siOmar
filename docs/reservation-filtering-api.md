# Reservation Filtering API Documentation

## Overview

The reservation API now supports comprehensive filtering capabilities to help users easily find their reservations based on various criteria.

## Endpoints

### 1. Get User Reservations with Filters

**Endpoint:** `GET /api/reservations`

**Authentication:** Required (Bearer Token)

**Query Parameters:**

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `filter` | string | Filter by temporal status: `upcoming`, `current`, `finished` | `?filter=upcoming` |
| `status` | string | Filter by reservation status: `pending`, `confirmed`, `active`, `completed`, `cancelled` | `?status=confirmed` |
| `date_from` | date | Filter reservations from this date (Y-m-d format) | `?date_from=2024-01-01` |
| `date_to` | date | Filter reservations until this date (Y-m-d format) | `?date_to=2024-12-31` |
| `search` | string | Search in reservation number, guest name, email, or unit name | `?search=RES-2024` |
| `sort_by` | string | Sort field: `created_at`, `check_in_date`, `check_out_date`, `total_amount`, `status` | `?sort_by=check_in_date` |
| `sort_order` | string | Sort order: `asc` or `desc` | `?sort_order=asc` |
| `per_page` | integer | Number of items per page (1-50) | `?per_page=20` |

### 2. Get Reservation Statistics

**Endpoint:** `GET /api/reservations/statistics`

**Authentication:** Required (Bearer Token)

**Response:** Returns counts and totals for different reservation categories.

## Filter Types

### 1. Temporal Filters (`filter` parameter)

- **`upcoming`**: Reservations with check-in date in the future and status `pending` or `confirmed`
- **`current`**: Currently ongoing reservations (status `active` or within check-in/check-out date range)
- **`finished`**: Completed or cancelled reservations, or those with check-out date in the past

### 2. Status Filters (`status` parameter)

- **`pending`**: Reservations awaiting confirmation
- **`confirmed`**: Reservations confirmed but not yet active
- **`active`**: Currently ongoing reservations
- **`completed`**: Finished reservations
- **`cancelled`**: Cancelled reservations

## Usage Examples

### Get Upcoming Reservations
```bash
GET /api/reservations?filter=upcoming
```

### Get Current Reservations Sorted by Check-in Date
```bash
GET /api/reservations?filter=current&sort_by=check_in_date&sort_order=asc
```

### Search for Specific Reservations
```bash
GET /api/reservations?search=John&status=confirmed
```

### Get Reservations in Date Range
```bash
GET /api/reservations?date_from=2024-01-01&date_to=2024-03-31
```

### Get Finished Reservations with Pagination
```bash
GET /api/reservations?filter=finished&per_page=20&sort_by=check_out_date&sort_order=desc
```

### Get Reservation Statistics
```bash
GET /api/reservations/statistics
```

## Response Format

### Reservations List Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "reservation_number": "RES-20241201-ABC123",
            "check_in_date": "2024-12-15",
            "check_out_date": "2024-12-20",
            "status": "confirmed",
            "total_amount": 500.00,
            "unit": {
                "id": 1,
                "name": "Luxury Villa",
                "unit_type": {
                    "name": "Villa"
                }
            },
            "can_cancel": true,
            "badge_colors": {
                "status": "blue",
                "payment": "green"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 50
    },
    "filters": {
        "applied_filter": "upcoming",
        "applied_status": null,
        "date_from": null,
        "date_to": null,
        "search": null,
        "sort_by": "created_at",
        "sort_order": "desc"
    }
}
```

### Statistics Response
```json
{
    "success": true,
    "data": {
        "total": 25,
        "upcoming": 5,
        "current": 2,
        "finished": 18,
        "by_status": {
            "pending": 3,
            "confirmed": 2,
            "active": 2,
            "completed": 15,
            "cancelled": 3
        },
        "total_spent": 12500.00,
        "upcoming_spent": 2500.00
    }
}
```

## Error Responses

### Validation Error
```json
{
    "success": false,
    "message": "The filter must be one of: upcoming, current, finished",
    "errors": {
        "filter": ["The filter must be one of: upcoming, current, finished"]
    }
}
```

### Unauthorized
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

## Best Practices

1. **Use specific filters** to reduce response time and data transfer
2. **Combine filters** for more precise results (e.g., `filter=upcoming&sort_by=check_in_date`)
3. **Use pagination** for large datasets (`per_page` parameter)
4. **Cache statistics** on the client side as they don't change frequently
5. **Handle errors gracefully** and provide user-friendly error messages

## Rate Limiting

- **Reservations list**: 300 requests per minute per user
- **Statistics**: 300 requests per minute per user

## Notes

- All dates should be in `Y-m-d` format
- Search is case-insensitive and searches across multiple fields
- The `finished` filter includes both completed and cancelled reservations
- Statistics are calculated in real-time and may take longer for users with many reservations
