# Password Reset API Documentation

## Overview
The password reset functionality allows users to reset their passwords through email verification. The API provides three main endpoints for password management.

## API Endpoints

### 1. Forgot Password (Send Reset Link)
**POST** `/api/forgot-password`

Sends a password reset token to the user's email address.

**Headers:**
- `Content-Type: application/json`
- `Accept: application/json`

**Request Body:**
```json
{
    "email": "user@example.com"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

**Response (Error - Email not found):**
```json
{
    "success": false,
    "message": "The selected email is invalid."
}
```

### 2. Reset Password
**POST** `/api/reset-password`

Resets the user's password using the token received via email.

**Headers:**
- `Content-Type: application/json`
- `Accept: application/json`

**Request Body:**
```json
{
    "email": "user@example.com",
    "token": "reset_token_from_email",
    "password": "new_password123",
    "password_confirmation": "new_password123"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Password reset successfully"
}
```

**Response (Error - Invalid token):**
```json
{
    "success": false,
    "message": "Unable to reset password. Please check your token and try again."
}
```

**Response (Error - Validation):**
```json
{
    "success": false,
    "message": "The password confirmation does not match."
}
```

### 3. Change Password (Authenticated Users)
**POST** `/api/change-password`

Allows authenticated users to change their password by providing their current password.

**Headers:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`
- `Accept: application/json`

**Request Body:**
```json
{
    "current_password": "old_password123",
    "password": "new_password123",
    "password_confirmation": "new_password123"
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Password changed successfully"
}
```

**Response (Error - Wrong current password):**
```json
{
    "success": false,
    "message": "Current password is incorrect"
}
```

## Password Requirements

- **Minimum length**: 8 characters
- **Confirmation**: Must match the password field
- **Current password**: Required for authenticated password changes

## Email Configuration

The password reset emails are sent using Laravel's mail system. Make sure your email configuration is properly set up in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourapp.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Email Template

The password reset email includes:
- A clear subject line
- Explanation of why the email was sent
- The reset token (for mobile app use)
- Expiration information
- Security notice

## Token Expiration

Password reset tokens expire after 60 minutes by default. This can be configured in `config/auth.php`:

```php
'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60, // minutes
        'throttle' => 60, // seconds
    ],
],
```

## Security Features

1. **Token-based reset**: Secure tokens are generated for each reset request
2. **Email verification**: Tokens are only sent to registered email addresses
3. **Token expiration**: Tokens automatically expire after a set time
4. **Rate limiting**: Built-in protection against abuse
5. **Current password verification**: Required for authenticated password changes

## Usage Examples

### Using cURL - Forgot Password
```bash
curl -X POST http://localhost:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

### Using cURL - Reset Password
```bash
curl -X POST http://localhost:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "user@example.com",
    "token": "reset_token_here",
    "password": "new_password123",
    "password_confirmation": "new_password123"
  }'
```

### Using cURL - Change Password
```bash
curl -X POST http://localhost:8000/api/change-password \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "current_password": "old_password123",
    "password": "new_password123",
    "password_confirmation": "new_password123"
  }'
```

### Using JavaScript/Fetch - Forgot Password
```javascript
fetch('/api/forgot-password', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'user@example.com'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Using JavaScript/Fetch - Reset Password
```javascript
fetch('/api/reset-password', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        email: 'user@example.com',
        token: 'reset_token_here',
        password: 'new_password123',
        password_confirmation: 'new_password123'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Using JavaScript/Fetch - Change Password
```javascript
fetch('/api/change-password', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    body: JSON.stringify({
        current_password: 'old_password123',
        password: 'new_password123',
        password_confirmation: 'new_password123'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Error Handling

All endpoints return consistent error responses with a single error message:

- **422 Unprocessable Entity**: Validation errors
- **400 Bad Request**: Business logic errors (invalid tokens, wrong passwords)
- **401 Unauthorized**: Missing or invalid authentication token

## Implementation Notes

1. **Database**: Uses the `password_reset_tokens` table for storing reset tokens
2. **Notifications**: Custom `ApiPasswordResetNotification` for mobile-friendly emails
3. **Security**: Tokens are hashed and have expiration times
4. **Validation**: Comprehensive validation for all inputs
5. **Consistency**: Follows the same error response pattern as other APIs
