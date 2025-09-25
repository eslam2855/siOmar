# Enhanced Reservation System Documentation

## Overview

The SiOmar API has been enhanced with a comprehensive reservation management system that includes configurable cancellation policies, detailed guest information tracking, and a complete reservation lifecycle management system.

## Key Features

### 1. **Cancellation Policy System**
- **Configurable policies**: Different cancellation windows and refund percentages
- **Per-unit policies**: Each unit can have its own cancellation policy
- **Automatic enforcement**: System automatically checks cancellation eligibility
- **Refund calculation**: Automatic refund amount calculation based on policy

### 2. **Enhanced Reservation Status Flow**
- **Pending** → **Confirmed** → **Active** → **Completed** → **Cancelled**
- **Payment status tracking**: Pending, Paid, Failed, Refunded, Partially Refunded
- **Automatic status updates**: Based on check-in/check-out dates

### 3. **Guest Information Management**
- **Detailed guest data**: Name, phone, email
- **Special requests**: Text field for additional requirements

### 4. **Transfer Payment System**
- **Transfer receipt upload**: Users can upload transfer receipt images
- **Transfer amount tracking**: Track the amount transferred by users
- **Transfer date recording**: Record when the transfer was made

### 5. **Admin Deposit Management**
- **Minimum deposit requirements**: Set minimum deposit amounts for reservations
- **Admin notes**: Add notes about reservation requirements
- **Deposit verification**: Verify and approve deposits
- **Deposit status tracking**: Track deposit verification status

## Database Schema

### Cancellation Policies Table
```sql
CREATE TABLE cancellation_policies (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    cancellation_hours INTEGER NOT NULL,
    refund_percentage DECIMAL(5,2) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    is_default BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Enhanced Reservations Table
```sql
ALTER TABLE reservations ADD COLUMN (
    cancellation_policy_id BIGINT FOREIGN KEY REFERENCES cancellation_policies(id),
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded', 'partially_refunded') DEFAULT 'pending',
    guest_name VARCHAR(255),
    guest_phone VARCHAR(20),
    guest_email VARCHAR(255),
    special_requests TEXT,
    early_check_in_time TIME,
    late_check_out_time TIME,
    early_check_in_requested BOOLEAN DEFAULT false,
    late_check_out_requested BOOLEAN DEFAULT false,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason VARCHAR(500),
    refund_amount DECIMAL(10,2),
    refunded_at TIMESTAMP NULL,
    admin_notes TEXT,
    transfer_image VARCHAR(255),
    transfer_amount DECIMAL(10,2),
    transfer_date TIMESTAMP NULL,
    minimum_deposit_amount DECIMAL(10,2),
    deposit_verified BOOLEAN DEFAULT false,
    deposit_verified_at TIMESTAMP NULL,
    confirmed_at TIMESTAMP NULL,
    activated_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL
);
```

## API Endpoints

### 1. **Create Reservation** (`POST /api/reservations`)

**Request (multipart/form-data):**
```
unit_id: 1 (required)
check_in_date: "2025-09-20" (required)
check_out_date: "2025-09-25" (required)
guest_name: "John Doe" (required)
guest_phone: "+1234567890" (required)
guest_email: "john@example.com" (required)
special_requests: "Extra towels and late check-in" (optional)
transfer_image: [file] (optional)
transfer_amount: 5000 (optional)
transfer_date: "2025-08-24" (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Reservation created successfully and is pending admin approval.",
    "data": {
        "id": 1,
        "reservation_number": "RES-20250818-ABC123",
        "unit": { /* unit details */ },
        "cancellation_policy": {
            "id": 1,
            "name": "Flexible",
            "description": "Free cancellation up to 24 hours before check-in.",
            "cancellation_window": "24 hours",
            "refund_percentage": 100.00
        },
        "check_in_date": "2025-09-20",
        "check_out_date": "2025-09-25",
        "nights": 5,
        "guest_information": {
            "name": "John Doe",
            "phone": "+1234567890",
            "email": "john@example.com"
        },
        "special_requests": "Extra towels and late check-in",
        "admin_notes": null,
        "pricing": {
            "total_amount": "11180.00",
            "cleaning_fee": "0.00",
            "security_deposit": "0.00",
            "refund_amount": null
        },
        "status": {
            "current": "pending",
            "payment": "pending",
            "can_cancel": true,
            "badge_color": "yellow",
            "payment_badge_color": "yellow"
        },
        "transfer_payment": {
            "transfer_amount": null,
            "transfer_date": null,
            "transfer_image_url": null,
            "minimum_deposit_amount": null,
            "deposit_verified": false,
            "deposit_verified_at": null,
            "deposit_status": "pending",
            "is_deposit_sufficient": true
        },
        "cancellation": {
            "cancelled_at": null,
            "cancellation_reason": null,
            "refunded_at": null
        },
        "timestamps": {
            "confirmed_at": null,
            "activated_at": null,
            "completed_at": null,
            "created_at": "2025-08-18T10:00:00.000000Z",
            "updated_at": "2025-08-18T10:00:00.000000Z"
        }
    }
}
```

### 2. **Get Reservations** (`GET /api/reservations`)

**Response:**
```json
{
    "success": true,
    "data": [
        /* Array of reservation objects */
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 50
    }
}
```

### 3. **Get Reservation Details** (`GET /api/reservations/{id}`)

Returns detailed information about a specific reservation.

### 4. **Cancel Reservation** (`POST /api/reservations/{id}/cancel`)

**Request:**
```json
{
    "reason": "Change of plans"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Reservation cancelled successfully.",
    "data": {
        /* Updated reservation object with cancellation details */
        "status": {
            "current": "cancelled",
            "can_cancel": false
        },
        "cancellation": {
            "cancelled_at": "2025-08-18T10:30:00.000000Z",
            "cancellation_reason": "Change of plans",
            "refunded_at": "2025-08-18T10:30:00.000000Z"
        },
        "pricing": {
            "refund_amount": "11180.00"
        }
    }
}
```

### 5. **Get Cancellation Policies** (`GET /api/cancellation-policies`)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Flexible",
            "description": "Free cancellation up to 24 hours before check-in. Full refund available.",
            "cancellation_hours": 24,
            "cancellation_window": "24 hours",
            "refund_percentage": 100.00,
            "is_active": true,
            "is_default": true
        },
        {
            "id": 2,
            "name": "Standard",
            "description": "Free cancellation up to 48 hours before check-in. 80% refund available.",
            "cancellation_hours": 48,
            "cancellation_window": "2 days",
            "refund_percentage": 80.00,
            "is_active": true,
            "is_default": false
        }
    ]
}
```

### 6. **Upload Transfer Receipt** (`POST /api/reservations/{id}/upload-transfer`)

Upload transfer receipt image and payment details.

**Request (multipart/form-data):**
```
transfer_image: [file] (required, image file)
transfer_amount: 5000 (required, numeric)
transfer_date: "2025-08-24" (required, date)
```

**Response:**
```json
{
    "success": true,
    "message": "Transfer receipt uploaded successfully.",
    "data": {
        /* Updated reservation object with transfer details */
        "transfer_payment": {
            "transfer_amount": 5000,
            "transfer_date": "2025-08-24T00:00:00.000000Z",
            "transfer_image_url": "http://example.com/storage/transfers/1234567890_receipt.jpg",
            "minimum_deposit_amount": 5000,
            "deposit_verified": false,
            "deposit_verified_at": null,
            "deposit_status": "pending_verification",
            "is_deposit_sufficient": true
        }
    }
}
```

### 7. **Update Admin Notes** (Admin Only) (`POST /api/reservations/{id}/admin-notes`)

Update admin notes and minimum deposit amount.

**Request:**
```json
{
    "admin_notes": "You should send 50% from total reservation to approve your reservation.",
    "minimum_deposit_amount": 5000
}
```

**Response:**
```json
{
    "success": true,
    "message": "Admin notes updated successfully.",
    "data": {
        /* Updated reservation object */
    }
}
```

### 8. **Verify Deposit** (Admin Only) (`POST /api/reservations/{id}/verify-deposit`)

Verify deposit payment.

**Response:**
```json
{
    "success": true,
    "message": "Deposit verified successfully.",
    "data": {
        /* Updated reservation object with verified deposit */
        "transfer_payment": {
            "deposit_verified": true,
            "deposit_verified_at": "2025-08-24T10:30:00.000000Z",
            "deposit_status": "sufficient"
        }
    }
}
```

### 9. **Update Reservation Status** (Admin Only) (`POST /api/reservations/{id}/status`)

**Request:**
```json
{
    "status": "confirmed",
    "admin_notes": "Payment received, reservation confirmed"
}
```

**Available Status Values:**
- `confirmed`: Admin confirms the reservation
- `active`: Guest has checked in
- `completed`: Guest has checked out
- `cancelled`: Reservation cancelled by admin

### 10. **Get Unit Reserved Days** (`GET /api/units/{id}/reserved-days`)

Get reserved days for a specific unit within a date range.

**Request:**
```
GET /api/units/1/reserved-days?start_date=2025-08-01&end_date=2025-12-31
```

**Response:**
```json
{
    "success": true,
    "data": {
        "unit_id": 1,
        "unit_name": "Luxury Apartment",
        "start_date": "2025-08-01",
        "end_date": "2025-12-31",
        "total_reserved_days": 15,
        "reserved_days": [
            {
                "date": "2025-09-20",
                "status": "confirmed",
                "guest_name": "John Doe"
            },
            {
                "date": "2025-09-21",
                "status": "confirmed",
                "guest_name": "John Doe"
            }
        ],
        "reservations": [
            {
                "check_in_date": "2025-09-20",
                "check_out_date": "2025-09-25",
                "status": "confirmed",
                "guest_name": "John Doe"
            }
        ]
    }
}
```

### 11. **Update Transfer Details** (Admin Only) (`POST /api/reservations/{id}/transfer-details`)

Update transfer details for a reservation.

**Request (multipart/form-data):**
```
transfer_amount: 6000 (optional)
transfer_date: "2025-08-25" (optional)
transfer_image: [file] (optional)
```

**Response:**
```json
{
    "success": true,
    "message": "Transfer details updated successfully.",
    "data": {
        /* Updated reservation object */
    }
}
```

### 7. **Calculate Bulk Pricing** (`POST /api/reservations/bulk-pricing`)

Calculate total cost for multiple date ranges in a single request.

**Request:**
```json
{
    "unit_id": 1,
    "date_ranges": [
        {
            "check_in_date": "2025-09-20",
            "check_out_date": "2025-09-25"
        },
        {
            "check_in_date": "2025-10-15",
            "check_out_date": "2025-10-20"
        },
        {
            "check_in_date": "2025-11-10",
            "check_out_date": "2025-11-15"
        }
    ],
    "include_fees": true
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "summary": {
            "unit_id": 1,
            "unit_name": "Luxury Apartment",
            "total_ranges": 3,
            "available_ranges": 2,
            "unavailable_ranges": 1,
            "total_nights": 10,
            "total_cost": 22360,
            "total_cleaning_fees": 0,
            "total_security_deposits": 0,
            "grand_total": 22360,
            "average_daily_rate": 2236,
            "cancellation_policy": {
                "id": 1,
                "name": "Flexible",
                "description": "Free cancellation up to 24 hours before check-in.",
                "cancellation_window": "24 hours",
                "refund_percentage": 100.00
            }
        },
        "date_ranges": [
            {
                "range_index": 0,
                "check_in_date": "2025-09-20",
                "check_out_date": "2025-09-25",
                "nights": 5,
                "is_available": true,
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
                    ]
                },
                "total_cost": 11180,
                "daily_average": 2236
            },
            {
                "range_index": 1,
                "check_in_date": "2025-10-15",
                "check_out_date": "2025-10-20",
                "nights": 5,
                "is_available": false,
                "pricing_breakdown": {
                    "total_price": 12770,
                    "cleaning_fee": "0.00",
                    "security_deposit": "0.00",
                    "grand_total": 12770,
                    "breakdown": [
                        {
                            "month": "2025-10",
                            "formatted_month": "October 2025",
                            "daily_price": 2554,
                            "nights": 5,
                            "subtotal": 12770
                        }
                    ]
                },
                "total_cost": 12770,
                "daily_average": 2554
            }
        ]
    }
}
```

**Use Cases:**
- **Multiple Trip Planning**: Calculate costs for several planned trips
- **Availability Checking**: Check availability across multiple date ranges
- **Cost Comparison**: Compare costs for different time periods
- **Bulk Booking**: Calculate total cost for extended stays or multiple bookings
- **Seasonal Analysis**: Compare pricing across different seasons

## Cancellation Policy Types

### 1. **Flexible** (Default)
- **Cancellation Window**: 24 hours before check-in
- **Refund**: 100%
- **Best for**: Last-minute bookings

### 2. **Standard**
- **Cancellation Window**: 48 hours before check-in
- **Refund**: 80%
- **Best for**: Regular bookings

### 3. **Moderate**
- **Cancellation Window**: 72 hours before check-in
- **Refund**: 60%
- **Best for**: Peak season bookings

### 4. **Strict**
- **Cancellation Window**: 1 week before check-in
- **Refund**: 50%
- **Best for**: Luxury units

### 5. **Non-refundable**
- **Cancellation Window**: 0 hours (no cancellation)
- **Refund**: 0%
- **Best for**: Special offers

## Reservation Status Flow

### 1. **Pending** (Initial State)
- Reservation created but not yet confirmed
- Payment status: Pending
- Can be cancelled by user (subject to policy)
- Admin can confirm or cancel

### 2. **Confirmed** (Admin Approved)
- Admin has confirmed the reservation
- Payment status: Paid
- Can be cancelled by user (subject to policy)
- Can be activated when guest checks in

### 3. **Active** (Guest Checked In)
- Guest has checked in
- Cannot be cancelled
- Can be completed when guest checks out

### 4. **Completed** (Guest Checked Out)
- Guest has checked out
- Final state for successful reservations
- Cannot be modified

### 5. **Cancelled** (Cancelled by User or Admin)
- Reservation has been cancelled
- Refund processed if applicable
- Cannot be reactivated

## Payment Status Tracking

### 1. **Pending**
- Initial payment status
- Payment not yet processed

### 2. **Paid**
- Payment successfully received
- Reservation confirmed

### 3. **Failed**
- Payment processing failed
- Reservation may be cancelled

### 4. **Refunded**
- Full refund processed
- Cancellation completed

### 5. **Partially Refunded**
- Partial refund processed
- Based on cancellation policy

## Admin Features

### 1. **Status Management**
- Confirm pending reservations
- Activate confirmed reservations
- Complete active reservations
- Cancel any reservation

### 2. **Notes System**
- Add admin notes to reservations
- Track internal communications
- Document special circumstances

### 3. **Cancellation Policy Management**
- Create custom policies
- Assign policies to units
- Set default policies

## Validation Rules

### Reservation Creation
- `unit_id`: Must exist and be available
- `check_in_date`: Must be after today
- `check_out_date`: Must be after check-in date
- `guest_name`: Required, max 255 characters
- `guest_phone`: Required, max 20 characters
- `guest_email`: Required, valid email format

### Cancellation
- User can only cancel their own reservations
- Must be within cancellation window (if policy exists)
- Cannot cancel completed reservations

## Error Handling

### Common Error Responses

**Unit Not Available:**
```json
{
    "success": false,
    "message": "Unit is not available for the selected dates"
}
```

**Cannot Cancel:**
```json
{
    "success": false,
    "message": "This reservation cannot be cancelled at this time."
}
```

**Unauthorized:**
```json
{
    "success": false,
    "message": "Unauthorized access to reservation"
}
```

## Best Practices

### 1. **Cancellation Policy Selection**
- Choose policies based on unit type and season
- Consider market conditions and competition
- Balance flexibility with revenue protection

### 2. **Status Management**
- Update status promptly when guests check in/out
- Use admin notes for important information
- Monitor payment status regularly

### 3. **Guest Communication**
- Send confirmation emails when status changes
- Provide clear cancellation policy information
- Respond to special requests promptly

### 4. **Data Management**
- Regular backup of reservation data
- Archive completed reservations
- Monitor cancellation patterns

## Migration and Setup

### Running Migrations
```bash
php artisan migrate
```

### Seeding Default Data
```bash
php artisan db:seed --class=CancellationPolicySeeder
```

### Assigning Policies to Units
```sql
-- Assign default policy to existing units
UPDATE units 
SET cancellation_policy_id = (SELECT id FROM cancellation_policies WHERE is_default = true LIMIT 1)
WHERE cancellation_policy_id IS NULL;
```

## Future Enhancements

### 1. **Automated Notifications**
- Email notifications for status changes
- SMS reminders for check-in/check-out
- Cancellation confirmation emails

### 2. **Advanced Pricing**
- Dynamic pricing based on demand
- Seasonal rate adjustments
- Last-minute booking discounts

### 3. **Guest Portal**
- Self-service reservation management
- Online check-in/check-out
- Review submission after stay

### 4. **Analytics Dashboard**
- Reservation performance metrics
- Cancellation rate analysis
- Revenue optimization insights
