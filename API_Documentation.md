# SiOmar Reservation System - API Documentation

## Overview
This document provides comprehensive documentation for all APIs in the SiOmar Reservation System, including authentication, unit management, reservations, and admin features.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the bearer token in the Authorization header:
```
Authorization: Bearer {your-token}
```

## Response Format
All API responses follow this standard format:
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ... }
}
```

## Error Responses
Error responses include:
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

---

## 🔐 Authentication APIs

### Register User
```http
POST /api/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1234567890"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "profile_image": null
        },
        "token": "1|abc123..."
    }
}
```

### Login User
```http
POST /api/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "profile_image": null
        },
        "token": "1|abc123..."
    }
}
```

### Forgot Password
```http
POST /api/forgot-password
```

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

### Reset Password
```http
POST /api/reset-password
```

**Request Body:**
```json
{
    "token": "reset-token",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password reset successfully"
}
```

### Logout
```http
POST /api/logout
```
*Requires authentication*

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Update Profile
```http
POST /api/profile
```
*Requires authentication*

**Request Body:**
```json
{
    "name": "John Doe",
    "phone": "+1234567890",
    "profile_image": "base64_encoded_image_or_file"
}
```

---

## 🏠 Unit APIs

### Get All Units
```http
GET /api/units
```

**Query Parameters:**
- `search` (optional): Search by unit name
- `type` (optional): Filter by unit type ID
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `amenities` (optional): Filter by amenities (comma-separated IDs)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Luxury Apartment",
            "description": "Beautiful apartment with sea view",
            "unit_number": "A101",
            "unit_type": {
                "id": 1,
                "name": "Apartment"
            },
            "price_per_night": 150.00,
            "cleaning_fee": 50.00,
            "security_deposit": 200.00,
            "max_guests": 4,
            "amenities": [
                {
                    "id": 1,
                    "name": "WiFi"
                }
            ],
            "primary_image": {
                "id": 1,
                "image_url": "http://example.com/images/unit1.jpg"
            },
            "images": [
                {
                    "id": 1,
                    "image_url": "http://example.com/images/unit1.jpg"
                }
            ]
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 10
    }
}
```

### Get Unit Details
```http
GET /api/units/{unit_id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Luxury Apartment",
        "description": "Beautiful apartment with sea view",
        "unit_number": "A101",
        "unit_type": {
            "id": 1,
            "name": "Apartment"
        },
        "price_per_night": 150.00,
        "cleaning_fee": 50.00,
        "security_deposit": 200.00,
        "max_guests": 4,
        "amenities": [
            {
                "id": 1,
                "name": "WiFi"
            }
        ],
        "primary_image": {
            "id": 1,
            "image_url": "http://example.com/images/unit1.jpg"
        },
        "images": [
            {
                "id": 1,
                "image_url": "http://example.com/images/unit1.jpg"
            }
        ],
        "cancellation_policy": {
            "id": 1,
            "name": "Flexible",
            "description": "Free cancellation up to 24 hours before check-in"
        }
    }
}
```

### Calculate Unit Pricing
```http
POST /api/units/{unit_id}/calculate-pricing
```

**Request Body:**
```json
{
    "check_in_date": "2025-01-15",
    "check_out_date": "2025-01-20",
    "number_of_guests": 2
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "unit_id": 1,
        "check_in_date": "2025-01-15",
        "check_out_date": "2025-01-20",
        "number_of_nights": 5,
        "price_per_night": 150.00,
        "subtotal": 750.00,
        "cleaning_fee": 50.00,
        "security_deposit": 200.00,
        "grand_total": 1000.00,
        "monthly_pricing": [
            {
                "month": "2025-01",
                "price_per_night": 150.00,
                "nights": 5
            }
        ]
    }
}
```

### Check Unit Availability
```http
GET /api/units/{unit_id}/availability
```
*Requires authentication*

**Query Parameters:**
- `check_in_date` (required): Start date (YYYY-MM-DD)
- `check_out_date` (required): End date (YYYY-MM-DD)

**Response:**
```json
{
    "success": true,
    "data": {
        "available": true,
        "message": "Unit is available for the selected dates"
    }
}
```

### Get Unit Reserved Days
```http
GET /api/units/{unit_id}/reserved-days
```
*Public API - No authentication required*

**Query Parameters:**
- `start_date` (optional): Start date for range (default: today)
- `end_date` (optional): End date for range (default: 6 months from today)

**Response:**
```json
{
    "success": true,
    "data": [
        "2025-01-15",
        "2025-01-16",
        "2025-01-17"
    ]
}
```

---

## 📅 Reservation APIs

### Get User Reservations
```http
GET /api/reservations
```
*Requires authentication*

**Query Parameters:**
- `status` (optional): Filter by status (pending, confirmed, active, completed, cancelled)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "reservation_number": "RES-20250115-ABC123",
            "check_in_date": "2025-01-15",
            "check_out_date": "2025-01-20",
            "number_of_guests": 2,
            "total_amount": 1000.00,
            "status": "pending",
            "payment_status": "pending",
            "guest_name": "John Doe",
            "guest_phone": "+1234567890",
            "guest_email": "john@example.com",
            "transfer_amount": 500.00,
            "transfer_date": "2025-01-10",
            "deposit_verified": false,
            "unit": {
                "id": 1,
                "name": "Luxury Apartment",
                "unit_type": {
                    "id": 1,
                    "name": "Apartment"
                },
                "primary_image": {
                    "id": 1,
                    "image_url": "http://example.com/images/unit1.jpg"
                }
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 5
    }
}
```

### Create Reservation
```http
POST /api/reservations
```
*Requires authentication*

**Request Body:**
```json
{
    "unit_id": 1,
    "check_in_date": "2025-01-15",
    "check_out_date": "2025-01-20",
    "number_of_guests": 2,
    "guest_name": "John Doe",
    "guest_phone": "+1234567890",
    "guest_email": "john@example.com",
    "special_requests": "Optional special requests",
    "transfer_amount": 500.00,
    "transfer_image": "base64_encoded_image_or_file",
    "transfer_date": "2025-01-10"
}
```

**Field Descriptions:**
- `unit_id` (required): ID of the unit to reserve
- `check_in_date` (required): Check-in date (YYYY-MM-DD)
- `check_out_date` (required): Check-out date (YYYY-MM-DD)
- `number_of_guests` (optional): Number of guests for the reservation
- `guest_name` (required): Name of the guest
- `guest_phone` (required): Phone number of the guest
- `guest_email` (required): Email address of the guest
- `special_requests` (optional): Any special requests or notes
- `transfer_amount` (optional): Amount transferred for payment
- `transfer_image` (optional): Transfer receipt image file
- `transfer_date` (optional): Date when the transfer was made (YYYY-MM-DD)

**Response:**
```json
{
    "success": true,
    "message": "Reservation created successfully and is pending admin approval. You will be notified once it is approved or rejected.",
    "data": {
        "id": 1,
        "reservation_number": "RES-20250115-ABC123",
        "check_in_date": "2025-01-15",
        "check_out_date": "2025-01-20",
        "number_of_guests": 2,
        "total_amount": 1000.00,
        "status": "pending",
        "payment_status": "pending",
        "guest_name": "John Doe",
        "guest_phone": "+1234567890",
        "guest_email": "john@example.com",
        "transfer_amount": 500.00,
        "transfer_date": "2025-01-10",
        "unit": {
            "id": 1,
            "name": "Luxury Apartment",
            "unit_type": {
                "id": 1,
                "name": "Apartment"
            },
            "primary_image": {
                "id": 1,
                "image_url": "http://example.com/images/unit1.jpg"
            }
        }
    }
}
```

### Get Reservation Details
```http
GET /api/reservations/{reservation_id}
```
*Requires authentication*

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "reservation_number": "RES-20250115-ABC123",
        "check_in_date": "2025-01-15",
        "check_out_date": "2025-01-20",
        "number_of_guests": 2,
        "total_amount": 1000.00,
        "cleaning_fee": 50.00,
        "security_deposit": 200.00,
        "status": "pending",
        "payment_status": "pending",
        "guest_name": "John Doe",
        "guest_phone": "+1234567890",
        "guest_email": "john@example.com",
        "special_requests": "Optional special requests",
        "transfer_amount": 500.00,
        "transfer_date": "2025-01-10",
        "transfer_image_url": "http://example.com/storage/transfers/receipt.jpg",
        "deposit_verified": false,
        "minimum_deposit_amount": 500.00,
        "deposit_percentage": 50.00,
        "admin_notes": "Admin notes here",
        "unit": {
            "id": 1,
            "name": "Luxury Apartment",
            "unit_type": {
                "id": 1,
                "name": "Apartment"
            },
            "primary_image": {
                "id": 1,
                "image_url": "http://example.com/images/unit1.jpg"
            }
        },
        "cancellation_policy": {
            "id": 1,
            "name": "Flexible",
            "description": "Free cancellation up to 24 hours before check-in"
        }
    }
}
```

### Update Reservation
```http
PUT /api/reservations/{reservation_id}
```
*Requires authentication*

**Request Body:**
```json
{
    "check_in_date": "2025-01-15",
    "check_out_date": "2025-01-20",
    "number_of_guests": 3,
    "guest_name": "John Doe",
    "guest_phone": "+1234567890",
    "guest_email": "john@example.com",
    "special_requests": "Updated special requests",
    "transfer_amount": 600.00,
    "transfer_image": "base64_encoded_image_or_file",
    "transfer_date": "2025-01-12"
}
```

**Field Descriptions:**
- `check_in_date` (optional): New check-in date (YYYY-MM-DD)
- `check_out_date` (optional): New check-out date (YYYY-MM-DD)
- `number_of_guests` (optional): Number of guests for the reservation
- `guest_name` (optional): Name of the guest
- `guest_phone` (optional): Phone number of the guest
- `guest_email` (optional): Email address of the guest
- `special_requests` (optional): Any special requests or notes
- `transfer_amount` (optional): Amount transferred for payment
- `transfer_image` (optional): Transfer receipt image file
- `transfer_date` (optional): Date when the transfer was made (YYYY-MM-DD)

**Note:** Only pending reservations can be updated. If dates are changed, the system will recalculate the total amount and check availability.

### Cancel Reservation
```http
POST /api/reservations/{reservation_id}/cancel
```
*Requires authentication*

**Request Body:**
```json
{
    "reason": "Optional cancellation reason"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Reservation cancelled successfully",
    "data": {
        "id": 1,
        "status": "cancelled",
        "cancellation_reason": "Optional cancellation reason"
    }
}
```

### Upload Transfer Image
```http
POST /api/reservations/{reservation_id}/upload-transfer
```
*Requires authentication*

**Request Body:**
```json
{
    "transfer_image": "base64_encoded_image_or_file",
    "transfer_amount": 500.00,
    "transfer_date": "2025-01-10"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Transfer receipt uploaded successfully",
    "data": {
        "id": 1,
        "transfer_amount": 500.00,
        "transfer_date": "2025-01-10",
        "transfer_image_url": "http://example.com/storage/transfers/receipt.jpg"
    }
}
```

### Calculate Bulk Pricing
```http
POST /api/reservations/bulk-pricing
```
*Requires authentication*

**Request Body:**
```json
{
    "unit_id": 1,
    "check_in_date": "2025-01-15",
    "check_out_date": "2025-01-20"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "unit_id": 1,
        "check_in_date": "2025-01-15",
        "check_out_date": "2025-01-20",
        "number_of_nights": 5,
        "price_per_night": 150.00,
        "subtotal": 750.00,
        "cleaning_fee": 50.00,
        "security_deposit": 200.00,
        "grand_total": 1000.00
    }
}
```

### Get Cancellation Policies
```http
GET /api/cancellation-policies
```
*Requires authentication*

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Flexible",
            "description": "Free cancellation up to 24 hours before check-in",
            "cancellation_hours": 24,
            "refund_percentage": 100
        },
        {
            "id": 2,
            "name": "Moderate",
            "description": "Free cancellation up to 7 days before check-in",
            "cancellation_hours": 168,
            "refund_percentage": 100
        }
    ]
}
```

---

## 👨‍💼 Admin APIs
*All admin APIs require admin role*

### Update Reservation Status
```http
POST /api/reservations/{reservation_id}/status
```

**Request Body:**
```json
{
    "status": "confirmed",
    "admin_notes": "Optional admin notes"
}
```

**Available Statuses:**
- `pending` - Initial status when reservation is created
- `confirmed` - Admin has confirmed the reservation
- `active` - Guest has checked in
- `completed` - Guest has checked out
- `cancelled` - Reservation has been cancelled

**Response:**
```json
{
    "success": true,
    "message": "Reservation status updated successfully",
    "data": {
        "id": 1,
        "status": "confirmed",
        "admin_notes": "Optional admin notes"
    }
}
```

### Update Admin Notes
```http
POST /api/reservations/{reservation_id}/admin-notes
```

**Request Body:**
```json
{
    "admin_notes": "Admin notes content"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Admin notes updated successfully",
    "data": {
        "id": 1,
        "admin_notes": "Admin notes content"
    }
}
```

### Verify Deposit
```http
POST /api/reservations/{reservation_id}/verify-deposit
```

**Request Body:**
```json
{
    "verified": true,
    "admin_notes": "Optional verification notes"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Deposit verified successfully",
    "data": {
        "id": 1,
        "deposit_verified": true,
        "deposit_verified_at": "2025-01-10T10:00:00Z"
    }
}
```

### Update Transfer Details
```http
POST /api/reservations/{reservation_id}/transfer-details
```

**Request Body:**
```json
{
    "transfer_amount": 500.00,
    "transfer_date": "2025-01-10",
    "transfer_image": "base64_encoded_image_or_file"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Transfer details updated successfully",
    "data": {
        "id": 1,
        "transfer_amount": 500.00,
        "transfer_date": "2025-01-10",
        "transfer_image_url": "http://example.com/storage/transfers/receipt.jpg"
    }
}
```

### Bulk Cancel Reservations
```http
POST /api/bulk/cancel-reservations
```

**Request Body:**
```json
{
    "reservation_ids": [1, 2, 3],
    "reason": "Optional bulk cancellation reason"
}
```

**Response:**
```json
{
    "success": true,
    "message": "3 reservations cancelled successfully",
    "data": {
        "cancelled_count": 3,
        "failed_count": 0
    }
}
```

---

## 📊 Analytics APIs
*All analytics APIs require admin role*

### Get Dashboard Analytics
```http
GET /api/analytics/dashboard
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_reservations": 150,
        "pending_reservations": 25,
        "confirmed_reservations": 50,
        "active_reservations": 30,
        "completed_reservations": 40,
        "cancelled_reservations": 5,
        "total_revenue": 15000.00,
        "monthly_revenue": 5000.00,
        "occupancy_rate": 85.5
    }
}
```

### Get Revenue Analytics
```http
GET /api/analytics/revenue
```

**Query Parameters:**
- `start_date` (optional): Start date for range
- `end_date` (optional): End date for range
- `group_by` (optional): Group by day, week, month (default: month)

**Response:**
```json
{
    "success": true,
    "data": {
        "total_revenue": 15000.00,
        "periods": [
            {
                "period": "2025-01",
                "revenue": 5000.00,
                "reservations": 25
            }
        ]
    }
}
```

### Get Reservation Analytics
```http
GET /api/analytics/reservations
```

**Query Parameters:**
- `start_date` (optional): Start date for range
- `end_date` (optional): End date for range

**Response:**
```json
{
    "success": true,
    "data": {
        "total_reservations": 150,
        "status_breakdown": {
            "pending": 25,
            "confirmed": 50,
            "active": 30,
            "completed": 40,
            "cancelled": 5
        },
        "monthly_trends": [
            {
                "month": "2025-01",
                "reservations": 25,
                "revenue": 5000.00
            }
        ]
    }
}
```

### Get Unit Analytics
```http
GET /api/analytics/units
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_units": 10,
        "available_units": 8,
        "occupied_units": 2,
        "unit_performance": [
            {
                "unit_id": 1,
                "unit_name": "Luxury Apartment",
                "reservations": 15,
                "revenue": 3000.00,
                "occupancy_rate": 85.5
            }
        ]
    }
}
```

### Get Recent Activities
```http
GET /api/analytics/recent-activities
```

**Query Parameters:**
- `limit` (optional): Number of activities to return (default: 20)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "description": "New reservation created: #RES-20250115-ABC123",
            "event": "created",
            "log_name": "reservations",
            "causer": {
                "id": 1,
                "name": "John Doe"
            },
            "subject": {
                "id": 1,
                "reservation_number": "RES-20250115-ABC123"
            },
            "created_at": "2025-01-15T10:00:00Z"
        }
    ]
}
```

---

## 🎯 Slider APIs

### Get All Sliders
```http
GET /api/sliders
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Welcome to SiOmar",
            "subtitle": "Luxury accommodations for your perfect stay",
            "description": "Experience the best in hospitality",
            "image_url": "http://example.com/images/slider1.jpg",
            "button_text": "Book Now",
            "button_url": "/units",
            "order": 1,
            "is_active": true
        }
    ]
}
```

---

## ⚙️ Settings APIs

### Get Settings
```http
GET /api/settings
```

**Response:**
```json
{
    "success": true,
    "data": {
        "default_minimum_deposit_amount": 500.00,
        "default_deposit_percentage": 50.00,
        "minimum_reservation_days": 1,
        "default_reservation_notes": "Welcome to our property!"
    }
}
```

---

## 🔍 Search APIs

### Search Units
```http
GET /api/search/units
```

**Query Parameters:**
- `q` (required): Search query
- `type` (optional): Filter by unit type
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `amenities` (optional): Filter by amenities (comma-separated)
- `check_in_date` (optional): Check-in date for availability
- `check_out_date` (optional): Check-out date for availability
- `guests` (optional): Number of guests

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Luxury Apartment",
            "description": "Beautiful apartment with sea view",
            "price_per_night": 150.00,
            "unit_type": {
                "id": 1,
                "name": "Apartment"
            },
            "primary_image": {
                "id": 1,
                "image_url": "http://example.com/images/unit1.jpg"
            },
            "available": true
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 5
    }
}
```

---

## 📱 Mobile App Specific APIs

### Get User Profile
```http
GET /api/profile
```
*Requires authentication*

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "profile_image": "http://example.com/storage/profiles/user1.jpg",
        "created_at": "2025-01-01T00:00:00Z"
    }
}
```

### Update User Profile
```http
POST /api/profile
```
*Requires authentication*

**Request Body:**
```json
{
    "name": "John Doe",
    "phone": "+1234567890",
    "profile_image": "base64_encoded_image_or_file"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "profile_image": "http://example.com/storage/profiles/user1.jpg"
    }
}
```

---

## 🚨 Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid input data |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Validation Error - Invalid data format |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error - Server error |

---

## 📝 Notes

1. **File Uploads**: For image uploads, you can either:
   - Send the image as a file in a multipart form request
   - Send the image as base64 encoded string

2. **Date Format**: All dates should be in ISO 8601 format (YYYY-MM-DD)

3. **Pagination**: Paginated responses include pagination metadata

4. **Authentication**: Most endpoints require a valid bearer token in the Authorization header

5. **Rate Limiting**: API requests are rate-limited to prevent abuse

6. **CORS**: The API supports CORS for cross-origin requests

---

## 🔗 Related Documentation

- [Postman Collection](./SiOmar_Mobile_API.postman_collection.json)
- [Postman Environment](./SiOmar_API_Environment.postman_environment.json)
- [Admin Dashboard Features](./ADMIN_DASHBOARD_FEATURES.md)
- [Reservation System](./ENHANCED_RESERVATION_SYSTEM.md) 