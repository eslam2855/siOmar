# SiOmar API Postman Collection

This Postman collection provides comprehensive testing capabilities for the SiOmar reservation management system API, including the new filtering features.

## 📋 **Collection Overview**

The collection is organized into the following folders:

- **Authentication** - User registration, login, password management
- **User Profile** - Profile management and password changes
- **Units** - Unit browsing, details, pricing, availability, and search
- **Reservations** - Complete reservation management with filtering
- **Settings** - Application settings and configuration
- **Admin Operations** - Admin-only reservation management
- **Bulk Operations** - Bulk actions for multiple reservations
- **Analytics** - Dashboard and reporting endpoints
- **Sliders** - Home page slider management
- **Notifications** - User notification management and push tokens
- **Admin Notifications** - Admin notification creation and management
- **Health Check** - API health monitoring

## 🚀 **Quick Start**

### 1. Import the Collection

1. Open Postman
2. Click "Import" button
3. Select the `SiOmar_API_Collection.json` file
4. The collection will be imported with all requests

### 2. Set Up Environment Variables

The collection uses two environment variables:

- `base_url` - Your API base URL (default: `http://localhost:8000`)
- `auth_token` - Your authentication token (will be set after login)

### 3. Authentication Flow

1. **Register a new user** using the "Register User" request
2. **Login** using the "Login User" request
3. **Copy the token** from the response
4. **Set the `auth_token` variable** in your environment

## 🔍 **New Reservation Filtering Features**

### Temporal Filters

- **Get Upcoming Reservations** - `?filter=upcoming`
- **Get Current Reservations** - `?filter=current`
- **Get Finished Reservations** - `?filter=finished`

### Status Filters

- **Get by Status** - `?status=confirmed|pending|active|completed|cancelled`

### Date Range Filters

- **Get by Date Range** - `?date_from=2024-01-01&date_to=2024-12-31`

### Search Filters

- **Search Reservations** - `?search=RES-2024` (searches reservation number, guest name, email, unit name)

### Sorting and Pagination

- **Sort by Field** - `?sort_by=check_in_date&sort_order=asc`
- **Pagination** - `?per_page=20`

### Statistics

- **Get Reservation Statistics** - Returns counts and totals for different categories

## 📝 **Usage Examples**

### 1. Get User's Upcoming Reservations
```
GET {{base_url}}/api/reservations?filter=upcoming
```

### 2. Get Current Reservations Sorted by Check-in Date
```
GET {{base_url}}/api/reservations?filter=current&sort_by=check_in_date&sort_order=asc
```

### 3. Search for Specific Reservations
```
GET {{base_url}}/api/reservations?search=John&status=confirmed
```

### 4. Get Reservations in Date Range
```
GET {{base_url}}/api/reservations?date_from=2024-01-01&date_to=2024-03-31
```

### 5. Get Reservation Statistics
```
GET {{base_url}}/api/reservations/statistics
```

### 6. Search Units
```
GET {{base_url}}/api/search/units?q=villa&min_price=100&max_price=500&min_guests=2
```

### 7. Get Settings
```
GET {{base_url}}/api/settings
GET {{base_url}}/api/settings/reservation
GET {{base_url}}/api/settings/system
GET {{base_url}}/api/settings/legal
```

### 8. Get Legal Documents
```
GET {{base_url}}/api/privacy-policy
GET {{base_url}}/api/terms-of-service
```

### 9. Update Legal Documents (Admin)
```
PUT {{base_url}}/api/privacy-policy
PUT {{base_url}}/api/terms-of-service
```

### 10. Notification Management
```
# Get user notifications
GET {{base_url}}/api/notifications

# Get unread notifications only
GET {{base_url}}/api/notifications?unread_only=true

# Mark notification as read
POST {{base_url}}/api/notifications/1/read

# Mark all notifications as read
POST {{base_url}}/api/notifications/read-all

# Get unread count
GET {{base_url}}/api/notifications/unread/count

# Register push token
POST {{base_url}}/api/push-tokens/register

# Create notification (admin only)
POST {{base_url}}/api/notifications/create
```

## 🔧 **Request Examples**

### Create a New Reservation
```json
POST {{base_url}}/api/reservations
{
    "unit_id": 1,
    "check_in_date": "2024-12-15",
    "check_out_date": "2024-12-20",
    "number_of_guests": 2,
    "guest_name": "John Doe",
    "guest_phone": "+1234567890",
    "guest_email": "john@example.com",
    "special_requests": "Early check-in preferred"
}
```

### Update Reservation Status (Admin)
```json
POST {{base_url}}/api/reservations/1/status
{
    "status": "confirmed"
}
```

### Upload Transfer Image
```
POST {{base_url}}/api/reservations/1/upload-transfer
Form Data:
- transfer_image: [file]
- transfer_amount: 500.00
- transfer_date: 2024-12-01
```

### Update Privacy Policy (Admin)
```json
PUT {{base_url}}/api/privacy-policy
{
    "content": "This is the updated privacy policy content. We collect and use your personal information in accordance with applicable laws and regulations. Your privacy is important to us and we are committed to protecting your personal data."
}
```

### Update Terms of Service (Admin)
```json
PUT {{base_url}}/api/terms-of-service
{
    "content": "These are the updated terms of service. By using our services, you agree to be bound by these terms and conditions. Please read them carefully before making any reservations."
}
```

## 📊 **Response Examples**

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

### Legal Settings Response
```json
{
    "success": true,
    "data": {
        "privacy_policy": "This is the privacy policy content...",
        "terms_of_service": "These are the terms of service...",
        "privacy_policy_last_updated": "2024-12-01",
        "terms_of_service_last_updated": "2024-12-01"
    }
}
```

### Privacy Policy Response
```json
{
    "success": true,
    "data": {
        "content": "This is the privacy policy content. We collect and use your personal information in accordance with applicable laws and regulations.",
        "last_updated": "2024-12-01"
    }
}
```

### Terms of Service Response
```json
{
    "success": true,
    "data": {
        "content": "These are the terms of service. By using our services, you agree to be bound by these terms and conditions.",
        "last_updated": "2024-12-01"
    }
}
```

### Notification Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "type": "reservation",
            "title": "Reservation Created",
            "message": "Your reservation #RES-20241201-ABC123 has been created and is pending approval",
            "data": {
                "reservation_id": 1,
                "reservation_number": "RES-20241201-ABC123",
                "total_amount": 500.00
            },
            "priority": "normal",
            "category": "reservation",
            "is_read": false,
            "read_at": null,
            "created_at": "2024-12-01T10:00:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1
    }
}
```

### Push Token Registration Response
```json
{
    "success": true,
    "message": "Push token registered successfully",
    "data": {
        "id": 1,
        "token": "fcm_token_here",
        "platform": "android",
        "device_id": "device_123",
        "is_active": true,
        "created_at": "2024-12-01T10:00:00.000000Z"
    }
}
```

## 🛠️ **Testing Workflow**

### 1. Basic Testing
1. Run "Health Check" to verify API is running
2. Register a new user
3. Login and get authentication token
4. Test basic CRUD operations

### 2. Filtering Testing
1. Create multiple reservations with different dates and statuses
2. Test each filter type individually
3. Test filter combinations
4. Test sorting and pagination

### 3. Admin Testing
1. Create a user with admin role
2. Test admin-only endpoints
3. Test bulk operations
4. Test analytics endpoints

### 4. Notification Testing
1. Register push tokens for different platforms
2. Create test notifications (admin)
3. Test notification retrieval and marking as read
4. Test unread count functionality
5. Test push token management

## 🔒 **Security Notes**

- All authenticated requests require the `Authorization: Bearer <token>` header
- Admin operations require admin role permissions
- Rate limiting is applied to all endpoints
- File uploads are restricted to specific formats and sizes

## 📈 **Performance Tips**

- Use specific filters to reduce response time
- Implement pagination for large datasets
- Cache statistics on the client side
- Use appropriate HTTP methods (GET for reading, POST for creating, PUT for updating, DELETE for removing)

## 🐛 **Troubleshooting**

### Common Issues

1. **401 Unauthorized**
   - Check if auth_token is set correctly
   - Verify token hasn't expired
   - Ensure proper Bearer token format

2. **422 Validation Error**
   - Check request body format
   - Verify required fields are provided
   - Ensure date formats are correct (Y-m-d)

3. **404 Not Found**
   - Verify base_url is correct
   - Check if resource ID exists
   - Ensure proper endpoint path

4. **429 Too Many Requests**
   - Wait before making more requests
   - Implement proper rate limiting handling

### Debug Mode

Enable debug mode in your Laravel application to get detailed error messages:

```php
// In .env file
APP_DEBUG=true
```

## 📞 **Support**

For issues or questions about the API:

1. Check the API documentation
2. Review the Laravel logs
3. Test with Postman collection
4. Contact the development team

## 🔄 **Updates**

This collection is updated regularly to match the latest API changes. Check for updates when new features are released.
