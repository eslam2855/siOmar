# Reservation Notes and Deposit Percentage - Field Locations

## 📍 **Database Structure**

### **Reservations Table Fields:**

| Field Name | Type | Description | Status |
|------------|------|-------------|---------|
| `admin_notes` | TEXT | Admin notes for reservations | ✅ **EXISTS** |
| `special_requests` | TEXT | User special requests | ✅ **EXISTS** |
| `minimum_deposit_amount` | DECIMAL(10,2) | Fixed minimum deposit amount | ✅ **EXISTS** |
| `deposit_percentage` | DECIMAL(5,2) | Percentage of total amount required as deposit | ✅ **ADDED** |
| `transfer_amount` | DECIMAL(10,2) | Actual amount transferred by user | ✅ **EXISTS** |
| `deposit_verified` | BOOLEAN | Whether deposit is verified | ✅ **EXISTS** |
| `deposit_verified_at` | TIMESTAMP | When deposit was verified | ✅ **EXISTS** |

## 🔧 **Model Configuration**

### **Reservation Model (`app/Models/Reservation.php`)**

#### **Fillable Fields:**
```php
protected $fillable = [
    // ... other fields ...
    'admin_notes',
    'minimum_deposit_amount',
    'deposit_percentage',  // ✅ ADDED
    'transfer_amount',
    'transfer_date',
    'deposit_verified',
    'deposit_verified_at',
];
```

#### **Casts:**
```php
protected $casts = [
    // ... other casts ...
    'minimum_deposit_amount' => 'decimal:2',
    'deposit_percentage' => 'decimal:2',  // ✅ ADDED
    'transfer_amount' => 'decimal:2',
    'deposit_verified' => 'boolean',
    'deposit_verified_at' => 'datetime',
];
```

#### **Helper Methods:**
```php
// ✅ EXISTING METHODS
public function getDepositStatus(): string
public function isDepositSufficient(): bool
public function verifyDeposit(): bool
public function getTransferImageUrl(): ?string

// ✅ NEW METHODS ADDED
public function calculateDepositAmount(): ?float
public function getDepositAmount(): ?float
```

## 📊 **API Response Structure**

### **ReservationResource (`app/Http/Resources/ReservationResource.php`)**

#### **Main Response Fields:**
```json
{
    "admin_notes": "Admin notes here",
    "special_requests": "User special requests",
    "transfer_payment": {
        "transfer_amount": 5000,
        "transfer_date": "2025-08-24T00:00:00.000000Z",
        "transfer_image_url": "http://example.com/storage/transfers/...",
        "minimum_deposit_amount": 5000,
        "deposit_percentage": 50,           // ✅ ADDED
        "calculated_deposit_amount": 5590,  // ✅ ADDED
        "required_deposit_amount": 5000,    // ✅ ADDED
        "deposit_verified": false,
        "deposit_verified_at": null,
        "deposit_status": "pending",
        "is_deposit_sufficient": true
    }
}
```

## 🎛️ **Admin Control Endpoints**

### **1. Update Admin Notes & Deposit Settings**
**Endpoint:** `POST /api/reservations/{id}/admin-notes`

**Request Body:**
```json
{
    "admin_notes": "You should send 50% from total reservation to approve your reservation.",
    "minimum_deposit_amount": 5000,
    "deposit_percentage": 50
}
```

**What you can control:**
- ✅ `admin_notes` - Add/edit admin notes
- ✅ `minimum_deposit_amount` - Set fixed deposit amount
- ✅ `deposit_percentage` - Set percentage-based deposit requirement

### **2. Update Transfer Details**
**Endpoint:** `POST /api/reservations/{id}/transfer-details`

**Request Body (multipart/form-data):**
```
transfer_amount: 6000
transfer_date: 2025-08-25
transfer_image: [file upload]
```

### **3. Verify Deposit**
**Endpoint:** `POST /api/reservations/{id}/verify-deposit`

## 🧮 **Deposit Calculation Logic**

### **Priority Order:**
1. **Fixed Amount:** If `minimum_deposit_amount` is set, use it
2. **Percentage:** If `deposit_percentage` is set, calculate: `(total_amount * deposit_percentage) / 100`
3. **No Requirement:** If neither is set, no deposit required

### **Example Calculations:**
```php
// Scenario 1: Fixed amount set
minimum_deposit_amount: 5000
deposit_percentage: 50
total_amount: 11180
// Result: Required deposit = 5000 (fixed amount takes priority)

// Scenario 2: Only percentage set
minimum_deposit_amount: null
deposit_percentage: 50
total_amount: 11180
// Result: Required deposit = 5590 (50% of 11180)

// Scenario 3: Neither set
minimum_deposit_amount: null
deposit_percentage: null
// Result: No deposit required
```

## 📋 **Database Migration**

### **Migration File:** `database/migrations/2025_08_24_194948_add_deposit_percentage_to_reservations_table.php`

```php
public function up(): void
{
    Schema::table('reservations', function (Blueprint $table) {
        if (!Schema::hasColumn('reservations', 'deposit_percentage')) {
            $table->decimal('deposit_percentage', 5, 2)->nullable()->after('minimum_deposit_amount');
        }
    });
}
```

## 🎯 **Usage Examples**

### **Setting Deposit Requirements:**
```json
// Fixed amount only
{
    "admin_notes": "Please send 5000 as deposit",
    "minimum_deposit_amount": 5000
}

// Percentage only
{
    "admin_notes": "Please send 50% of total amount as deposit",
    "deposit_percentage": 50
}

// Both (fixed amount takes priority)
{
    "admin_notes": "Please send 5000 as deposit (or 50% whichever is higher)",
    "minimum_deposit_amount": 5000,
    "deposit_percentage": 50
}
```

### **Response Example:**
```json
{
    "success": true,
    "message": "Admin notes updated successfully.",
    "data": {
        "admin_notes": "Please send 50% of total amount as deposit",
        "transfer_payment": {
            "minimum_deposit_amount": null,
            "deposit_percentage": 50,
            "calculated_deposit_amount": 5590,
            "required_deposit_amount": 5590,
            "deposit_status": "pending",
            "is_deposit_sufficient": false
        }
    }
}
```

## ✅ **Summary**

### **Reservation Notes:**
- **Field:** `admin_notes` in `reservations` table
- **Status:** ✅ **Available and working**
- **Admin Control:** ✅ **Can add/edit via API**

### **Deposit Percentage:**
- **Field:** `deposit_percentage` in `reservations` table
- **Status:** ✅ **Just added and working**
- **Admin Control:** ✅ **Can set via API**
- **Calculation:** ✅ **Automatic calculation based on total amount**

### **Complete Deposit Management:**
- ✅ Fixed amount deposits
- ✅ Percentage-based deposits
- ✅ Automatic calculation
- ✅ Deposit verification
- ✅ Status tracking
- ✅ Admin full control
