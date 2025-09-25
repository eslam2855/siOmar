# 🔄 Complete Reservation Cycle Documentation

## 📋 Overview
This document outlines the complete reservation cycle implemented in the SiOmar system, including all status transitions, admin actions, and automated processes.

## 🎯 Reservation Lifecycle

### **1. Reservation Creation (API)**
- **Status**: `pending`
- **Trigger**: User creates reservation via API
- **Required**: Unit availability check, pricing calculation
- **Optional**: Transfer image, deposit amount

### **2. Deposit Verification (Admin)**
- **Status**: `pending` → `pending` (with deposit_verified = true)
- **Trigger**: Admin verifies transfer/deposit
- **Required**: Transfer amount and image
- **Action**: `verifyDeposit()`

### **3. Reservation Confirmation (Admin)**
- **Status**: `pending` → `confirmed`
- **Trigger**: Admin confirms after deposit verification
- **Required**: `deposit_verified = true`
- **Action**: `confirmReservation()`

### **4. Reservation Activation (Admin/Auto)**
- **Status**: `confirmed` → `active`
- **Trigger**: Admin manually or automatic on check-in date
- **Required**: Check-in date reached
- **Action**: `activateReservation()` or automatic command

### **5. Reservation Completion (Admin/Auto)**
- **Status**: `active` → `completed`
- **Trigger**: Admin manually or automatic on check-out date
- **Required**: Check-out date reached
- **Action**: `completeReservation()` or automatic command

### **6. Reservation Cancellation (Admin)**
- **Status**: `pending`/`confirmed` → `cancelled`
- **Trigger**: Admin cancels reservation
- **Required**: Cancellation reason, optional refund amount
- **Action**: `cancelReservation()`

## 🔧 Admin Actions Available

### **Status Management**
- ✅ `approveReservation()` - Approve pending reservation
- ✅ `confirmReservation()` - Confirm after deposit verification
- ✅ `activateReservation()` - Activate on check-in date
- ✅ `completeReservation()` - Complete after check-out
- ✅ `cancelReservation()` - Cancel with reason and refund
- ✅ `rejectReservation()` - Reject pending reservation

### **Payment Management**
- ✅ `verifyDeposit()` - Verify transfer/deposit
- ✅ `updatePaymentStatus()` - Update payment status
- ✅ `updateTransferDetails()` - Update transfer information

### **Notes & Settings**
- ✅ `updateAdminNotes()` - Update admin notes and deposit settings

## 🤖 Automated Processes

### **Automatic Status Transitions**
```bash
# Process automatic status changes
php artisan reservations:process-status

# Dry run to see what would be processed
php artisan reservations:process-status --dry-run
```

**What it does:**
- Automatically activates confirmed reservations on check-in date
- Automatically completes active reservations on check-out date

### **Scheduled Execution**
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('reservations:process-status')->daily();
}
```

## 📊 Status Flow Diagram

```
[User Creates Reservation]
         ↓
    [pending] ←─────────────┐
         ↓                  │
[Deposit Verification]      │
         ↓                  │
[deposit_verified = true]   │
         ↓                  │
    [confirmed] ←───────────┤
         ↓                  │
[Check-in Date Reached]     │
         ↓                  │
     [active] ←─────────────┤
         ↓                  │
[Check-out Date Reached]    │
         ↓                  │
   [completed]              │
                            │
    [cancelled] ←───────────┘
```

## 🔐 Security Features

### **Authorization**
- All admin actions require admin role
- Status transitions validated by business rules
- Date validations prevent invalid transitions

### **Validation**
- Status transition rules enforced
- Date range validations
- Amount validations for refunds
- File upload validations

## 📱 API Endpoints

### **Public APIs**
- `POST /api/reservations` - Create reservation
- `GET /api/units/{unit}/reserved-days` - Get reserved days

### **Protected APIs**
- `GET /api/reservations` - List user reservations
- `GET /api/reservations/{id}` - Get reservation details
- `POST /api/reservations/{id}/cancel` - Cancel reservation

## 🎨 Admin Interface

### **Reservation Details Page**
- Complete reservation information display
- Status action buttons (context-aware)
- Transfer/deposit management
- Admin notes and settings
- Cancellation modal with refund options

### **Status Action Buttons**
- **Pending**: Approve/Confirm, Reject, Cancel
- **Confirmed**: Activate, Cancel
- **Active**: Complete
- **Completed**: View only
- **Cancelled**: View only

## 📈 Business Logic

### **Deposit Calculation**
- Fixed amount: `minimum_deposit_amount`
- Percentage: `deposit_percentage` of total
- Verification required before confirmation

### **Pricing**
- Monthly pricing system
- Dynamic calculation based on date range
- Cleaning fees and security deposits

### **Cancellation**
- Reason required
- Optional refund amount
- Maximum refund = total amount

## 🚀 Usage Examples

### **Manual Status Change**
```php
// Admin confirms reservation
$reservation->update([
    'status' => 'confirmed',
    'confirmed_at' => now(),
]);
```

### **Automatic Processing**
```bash
# Run daily to process automatic transitions
php artisan reservations:process-status
```

### **Check Reservation Status**
```php
// Check if can be activated
if ($reservation->canBeActivated()) {
    // Show activate button
}

// Check if overdue
if ($reservation->isOverdueForActivation()) {
    // Show warning
}
```

## 🔍 Monitoring & Alerts

### **Overdue Reservations**
- `isOverdueForActivation()` - Confirmed but past check-in
- `isOverdueForCompletion()` - Active but past check-out

### **Status Badges**
- Color-coded status indicators
- Payment status indicators
- Deposit status indicators

## 📝 Notes

### **What's Working**
- ✅ Complete reservation lifecycle
- ✅ Admin status management
- ✅ Automatic transitions
- ✅ Deposit verification
- ✅ Cancellation with refunds
- ✅ Transfer management
- ✅ Admin interface
- ✅ API endpoints
- ✅ Security validations

### **Future Enhancements**
- 🔄 Email notifications
- 🔄 SMS notifications
- 🔄 Revenue reporting
- 🔄 Advanced analytics
- 🔄 Audit trail logging
- 🔄 Bulk operations

## 🎯 Conclusion

The reservation system now has a **complete lifecycle** with:
- **Manual admin controls** for all status transitions
- **Automatic processing** for date-based transitions
- **Comprehensive validation** and security
- **User-friendly interface** with context-aware actions
- **Flexible payment handling** with deposit verification

The system is now **production-ready** for managing the complete reservation workflow from creation to completion.
