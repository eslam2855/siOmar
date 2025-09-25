<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# SiOmar - Unit Reservation System

A Laravel-based reservation system for units (panglo, studio, shalet) with mobile app API support and admin approval workflow.

## Why Separate Admin from API Routes?

### 🎯 **Key Reasons for Separation**

1. **Security & Access Control**
   - **API routes**: Designed for mobile app users (customers) with limited permissions
   - **Web routes**: Designed for admin users (business owners) with full system access
   - Different authentication methods (API tokens vs web sessions)

2. **Different Use Cases**
   - **Mobile API**: Simple, fast JSON responses for mobile app consumption
   - **Admin Dashboard**: Rich web interface with complex UI, forms, and real-time updates

3. **Scalability & Maintenance**
   - API routes can be versioned independently
   - Admin functionality can evolve without affecting mobile app
   - Easier to maintain and debug

4. **User Experience**
   - Mobile users get optimized API responses
   - Admin users get full-featured web dashboard
   - Better performance for both user types

5. **Development Workflow**
   - Mobile app developers work with API endpoints
   - Web developers work with admin dashboard
   - Clear separation of responsibilities

### 🏗️ **Architecture Benefits**

- **Clean Code**: Each system has its own controllers and routes
- **Security**: Admin functionality is isolated from public API
- **Performance**: Optimized responses for each use case
- **Maintainability**: Easier to update and extend each system
- **Testing**: Can test API and admin functionality independently

## Architecture Overview

This system follows a **separation of concerns** approach:

### 🏗️ **System Architecture**

1. **API Routes** (`/api/*`) - For mobile app users
   - User registration/login
   - Unit browsing and availability checking
   - Reservation creation and management
   - User profile management

2. **Web Routes** (`/admin/*`) - For admin dashboard
   - Admin dashboard with statistics
   - Reservation approval/rejection
   - Unit and user management
   - System administration

3. **Database** - Shared between both systems
   - Units, reservations, users, etc.
   - Role-based access control

## Database Structure

### Core Tables

#### 1. `unit_types`
- `id` - Primary key
- `name` - Unit type name (panglo, studio, shalet)
- `description` - Description of the unit type
- `max_capacity` - Maximum number of guests
- `is_active` - Whether the unit type is active
- `created_at`, `updated_at` - Timestamps

#### 2. `units`
- `id` - Primary key
- `unit_type_id` - Foreign key to unit_types
- `name` - Unit name
- `unit_number` - Unique unit number
- `description` - Unit description
- `size_sqm` - Size in square meters
- `bedrooms` - Number of bedrooms
- `bathrooms` - Number of bathrooms
- `max_guests` - Maximum number of guests
- `status` - Unit status (available, occupied, maintenance, reserved)
- `latitude`, `longitude` - GPS coordinates
- `address` - Unit address
- `is_active` - Whether the unit is active
- `created_at`, `updated_at` - Timestamps

#### 3. `amenities`
- `id` - Primary key
- `name` - Amenity name
- `icon` - Icon for UI display
- `description` - Amenity description
- `is_active` - Whether the amenity is active
- `created_at`, `updated_at` - Timestamps

#### 4. `unit_amenities`
- `id` - Primary key
- `unit_id` - Foreign key to units
- `amenity_id` - Foreign key to amenities
- `created_at`, `updated_at` - Timestamps

#### 5. `pricing`
- `id` - Primary key
- `unit_id` - Foreign key to units
- `cleaning_fee` - Cleaning fee
- `security_deposit` - Security deposit
- `is_active` - Whether the pricing is active
- `valid_from`, `valid_to` - Validity period
- `created_at`, `updated_at` - Timestamps

#### 6. `unit_month_prices`
- `id` - Primary key
- `unit_id` - Foreign key to units
- `year_month` - Month in YYYY-MM format
- `daily_price` - Daily rate for this month
- `currency` - Currency code (default: EGP)
- `is_active` - Whether this price is active
- `created_at`, `updated_at` - Timestamps

#### 7. `reservations`
- `id` - Primary key
- `user_id` - Foreign key to users
- `unit_id` - Foreign key to units
- `reservation_number` - Unique reservation number
- `check_in_date` - Check-in date
- `check_out_date` - Check-out date
- `number_of_guests` - Number of guests
- `total_amount` - Total amount
- `cleaning_fee` - Cleaning fee
- `security_deposit` - Security deposit
- `status` - Reservation status (pending, confirmed, checked_in, checked_out, cancelled)
- `special_requests` - Special requests
- `cancellation_reason` - Cancellation reason
- `confirmed_at`, `checked_in_at`, `checked_out_at`, `cancelled_at` - Status timestamps
- `created_at`, `updated_at` - Timestamps

#### 8. `reviews`
- `id` - Primary key
- `user_id` - Foreign key to users
- `unit_id` - Foreign key to units
- `reservation_id` - Foreign key to reservations (optional)
- `rating` - Rating (1-5 stars)
- `review_text` - Review text
- `is_approved` - Whether the review is approved
- `reviewed_at` - Review timestamp
- `created_at`, `updated_at` - Timestamps

#### 9. `unit_images`
- `id` - Primary key
- `unit_id` - Foreign key to units
- `image_path` - Image file path
- `caption` - Image caption
- `order` - Display order
- `is_primary` - Whether this is the primary image
- `is_active` - Whether the image is active
- `created_at`, `updated_at` - Timestamps

#### 10. `permissions` (Spatie Laravel Permission)
- `id` - Primary key
- `name` - Permission name
- `guard_name` - Guard name
- `created_at`, `updated_at` - Timestamps

#### 11. `roles` (Spatie Laravel Permission)
- `id` - Primary key
- `name` - Role name (admin, user)
- `guard_name` - Guard name
- `created_at`, `updated_at` - Timestamps

## API Endpoints (Mobile App)

### Authentication

#### Register User
```
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Logout
```
POST /api/logout
Authorization: Bearer {token}
```

#### Get Profile
```
GET /api/profile
Authorization: Bearer {token}
```

#### Update Profile
```
PUT /api/profile
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Updated",
    "email": "john.updated@example.com"
}
```

### Units

#### List Units
```
GET /api/units?unit_type_id=1&status=available&max_guests=2&min_price=100&max_price=500
Authorization: Bearer {token}
```

#### Get Unit Details
```
GET /api/units/{unit_id}
Authorization: Bearer {token}
```

#### Check Unit Availability
```
GET /api/units/{unit_id}/availability?check_in_date=2024-01-15&check_out_date=2024-01-20
Authorization: Bearer {token}
```

### Reservations

#### List User Reservations
```
GET /api/reservations
Authorization: Bearer {token}
```

#### Create Reservation
```
POST /api/reservations
Authorization: Bearer {token}
Content-Type: application/json

{
    "unit_id": 1,
    "check_in_date": "2024-01-15",
    "check_out_date": "2024-01-20",
    "guest_name": "John Doe",
    "guest_phone": "+1234567890",
    "guest_email": "john@example.com",
    "special_requests": "Early check-in if possible"
}
```

#### Get Reservation Details
```
GET /api/reservations/{reservation_id}
Authorization: Bearer {token}
```

#### Update Reservation
```
PUT /api/reservations/{reservation_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "check_in_date": "2024-01-16",
    "check_out_date": "2024-01-21",
    "guest_name": "John Doe",
    "guest_phone": "+1234567890",
    "guest_email": "john@example.com",
    "special_requests": "Updated special requests"
}
```

#### Cancel Reservation
```
DELETE /api/reservations/{reservation_id}
Authorization: Bearer {token}
```

## Web Routes (Admin Dashboard)

### Admin Dashboard
```
GET /admin
```

### Reservations Management
```
GET /admin/reservations
GET /admin/reservations/pending
GET /admin/reservations/{reservation}
POST /admin/reservations/{reservation}/approve
POST /admin/reservations/{reservation}/reject
```

### Units Management
```
GET /admin/units
```

### Users Management
```
GET /admin/users
```

## Admin Approval Workflow

1. **User creates reservation** - Status automatically set to 'pending'
2. **Admin reviews reservation** - Admin can view all pending reservations via web dashboard
3. **Admin approves/rejects** - Admin can approve or reject with reason via web dashboard
4. **User notified** - User receives notification of approval/rejection
5. **Reservation confirmed** - Only approved reservations are confirmed

### Reservation Status Flow:
- `pending` → `confirmed` (admin approval)
- `pending` → `cancelled` (admin rejection)
- `confirmed` → `checked_in` (user checks in)
- `checked_in` → `checked_out` (user checks out)

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Start the development server: `php artisan serve`

## Default Users

After seeding, the following users are created:

- **Admin User**: `admin@example.com` / `password`
- **Regular User**: `test@example.com` / `password`

## Features

- **Unit Management**: Support for panglo, studio, and shalet units
- **Reservation System**: Complete booking management with admin approval workflow
- **Admin Dashboard**: Comprehensive web-based admin interface for managing reservations
- **Role-Based Access**: Admin and user roles with appropriate permissions
- **Pricing**: Flexible pricing with seasonal rates, weekend rates, and discounts
- **Amenities**: Configurable amenities for each unit
- **Reviews & Ratings**: User reviews and ratings system
- **Image Management**: Multiple images per unit with primary image support
- **Mobile API**: RESTful API for mobile app integration
- **Authentication**: Token-based authentication using Laravel Sanctum
- **Admin Approval**: All reservations require admin approval before confirmation

## Mobile App Integration

The API is designed for mobile app integration with:
- JWT-like token authentication
- RESTful endpoints
- JSON responses
- Proper error handling
- Pagination support
- Filtering and search capabilities
- Admin approval workflow

## Seeder Strategy - Avoiding Duplicates

### 🎯 **Seeder Helper Trait**

We've created a `SeederHelper` trait that provides consistent methods for handling duplicates across all seeders:

```php
// Located in: app/SeederHelper.php

trait SeederHelper
{
    /**
     * Create or update a model with the given data
     */
    protected function createOrUpdate($modelClass, array $searchAttributes, array $data)
    
    /**
     * Create a model only if it doesn't exist
     */
    protected function createIfNotExists($modelClass, array $searchAttributes, array $data)
    
    /**
     * Create multiple records without duplicates
     */
    protected function createMultipleWithoutDuplicates($modelClass, array $records, string $uniqueField = 'name')
}
```

### 🏗️ **Seeder Implementation**

#### 1. **UnitTypeSeeder**
```php
class UnitTypeSeeder extends Seeder
{
    use SeederHelper;

    public function run(): void
    {
        $unitTypes = [
            ['name' => 'Panglo', 'description' => '...', 'max_capacity' => 2],
            ['name' => 'Studio', 'description' => '...', 'max_capacity' => 2],
            ['name' => 'Shalet', 'description' => '...', 'max_capacity' => 4],
        ];

        $this->createMultipleWithoutDuplicates(UnitType::class, $unitTypes, 'name');
    }
}
```

#### 2. **AmenitySeeder**
```php
class AmenitySeeder extends Seeder
{
    use SeederHelper;

    public function run(): void
    {
        $amenities = [
            ['name' => 'WiFi', 'icon' => 'wifi', 'description' => '...'],
            ['name' => 'Air Conditioning', 'icon' => 'ac', 'description' => '...'],
            // ... more amenities
        ];

        $this->createMultipleWithoutDuplicates(Amenity::class, $amenities, 'name');
    }
}
```

#### 3. **RoleAndPermissionSeeder**
```php
class RoleAndPermissionSeeder extends Seeder
{
    use SeederHelper;

    public function run(): void
    {
        // Create permissions
        $permissions = ['view units', 'create units', 'edit units', ...];
        
        foreach ($permissions as $permission) {
            $this->createIfNotExists(Permission::class, ['name' => $permission], ['name' => $permission]);
        }

        // Create roles
        $adminRole = $this->createIfNotExists(Role::class, ['name' => 'admin'], ['name' => 'admin']);
        $userRole = $this->createIfNotExists(Role::class, ['name' => 'user'], ['name' => 'user']);
    }
}
```

#### 4. **DatabaseSeeder**
```php
class DatabaseSeeder extends Seeder
{
    use SeederHelper;

    public function run(): void
    {
        // Call other seeders
        $this->call([
            RoleAndPermissionSeeder::class,
            UnitTypeSeeder::class,
            AmenitySeeder::class,
        ]);

        // Create users
        $admin = $this->createIfNotExists(
            User::class,
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        
        // Assign roles if not already assigned
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
```

### 🎯 **Key Benefits**

1. **No Duplicates**: Using `firstOrCreate` and `updateOrCreate` prevents duplicate entries
2. **Consistent Approach**: All seeders use the same helper methods
3. **Safe to Re-run**: Seeders can be run multiple times without issues
4. **Maintainable**: Easy to add new seeders using the same pattern
5. **Type Safe**: Uses proper type hints and model classes

### 🚀 **Usage**

```bash
# Run all seeders (safe to run multiple times)
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UnitTypeSeeder

# Refresh database and seed
php artisan migrate:fresh --seed
```

### 🔄 **Seeder Methods**

- **`createIfNotExists`**: Creates only if record doesn't exist
- **`createOrUpdate`**: Creates new or updates existing record
- **`createMultipleWithoutDuplicates`**: Handles multiple records efficiently

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
