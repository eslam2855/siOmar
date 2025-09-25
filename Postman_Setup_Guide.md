# Postman Collection Setup Guide

## Quick Start

### 1. Import the Collection and Environment

1. Open Postman
2. Click **Import** button
3. Select both files:
   - `SiOmar_Mobile_API.postman_collection.json`
   - `SiOmar_API_Environment.postman_environment.json`
4. The collection and environment will be imported with all endpoints organized

### 2. Environment Variables (Auto-configured)

The environment file includes all necessary variables:

| Variable | Initial Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost:8000` | Base URL for the API |
| `auth_token` | (leave empty) | Authentication token (auto-saved) |
| `user_email` | `test@example.com` | Test user email |
| `user_password` | `password123` | Test user password |
| `unit_id` | `1` | Test unit ID |
| `reservation_id` | `1` | Test reservation ID |

### 3. Test Authentication

1. **Register a new user:**
   - Go to `Authentication > Register User`
   - Update the request body with your details
   - Send the request
   - Token is automatically saved to environment

2. **Login (alternative):**
   - Go to `Authentication > Login User`
   - Update the request body with your credentials
   - Send the request
   - Token is automatically saved to environment

**Note:** The collection includes automatic token management - tokens are saved automatically after login/register.

### 4. Test All Endpoints

Once you have the token set, you can test all endpoints:

- **Authentication:** Register, login, password reset, profile management
- **Units:** Browse units, get details, check availability
- **Reservations:** Create, view, update, cancel reservations

## Collection Structure

### Authentication
- `Register User` - Create new account
- `Login User` - Login and get token
- `Forgot Password` - Send password reset token
- `Reset Password` - Reset password using token
- `Get Profile` - View user profile with image URL
- `Update Profile (Text Only)` - Update user information
- `Update Profile with Image` - Update profile including image upload
- `Change Password` - Change password for authenticated users
- `Logout` - Invalidate token

### Sliders
- `Get All Sliders` - Get home page sliders for mobile app
- `Get Slider Details` - View specific slider information

### Units
- `Get All Units` - Browse available units with filters (no pagination)
- `Get Unit Details` - View specific unit information
- `Check Unit Availability` - Check dates availability

### Reservations
- `Get User Reservations` - View user's reservations (paginated)
- `Create Reservation` - Book a unit
- `Get Reservation Details` - View specific reservation
- `Update Reservation` - Modify existing reservation
- `Cancel Reservation` - Cancel booking

## Testing Workflow

### 1. Authentication Flow
```
Register User → Login User → Get Profile → Update Profile → Change Password → Logout
```

### 2. Password Reset Flow
```
Forgot Password → Check Email → Reset Password → Login with New Password
```

### 3. Profile Management Flow
```
Get Profile → Update Profile (Text Only) → Update Profile with Image → Get Profile
```

### 4. Slider Flow
```
Get All Sliders → Get Slider Details
```

### 5. Unit Browsing Flow
```
Get All Units → Get Unit Details → Check Unit Availability
```

### 6. Reservation Flow
```
Create Reservation → Get Reservation Details → Update Reservation → Cancel Reservation
```

## New Features

### 🔐 Password Reset
- **Forgot Password:** Send reset token to email
- **Reset Password:** Use token to set new password
- **Change Password:** Authenticated users can change password

### 📸 Profile Images
- **Update Profile with Image:** Upload profile pictures
- **Form Data Support:** Proper multipart/form-data handling
- **Image Validation:** File type and size validation

### 📱 Mobile-Optimized
- **No Pagination:** Units endpoint returns all units
- **Single Error Messages:** Consistent error response format
- **Token-based Reset:** Perfect for mobile app integration
- **Home Page Sliders:** Ready-to-use slider content for mobile home page

## Tips

### Automatic Token Management
The collection includes advanced scripts for token management:

1. **Auto-save:** Tokens are automatically extracted and stored after login/register
2. **Auto-use:** Tokens are automatically added to protected endpoints
3. **Console Logging:** Token operations are logged to console

### Environment Switching
You can create multiple environments for different scenarios:

- **Local Development:** `http://localhost:8000`
- **Staging:** `https://staging.siomar.com`
- **Production:** `https://api.siomar.com`

### File Upload Testing
For profile image uploads:
1. Use the "Update Profile with Image" request
2. Click "Select Files" in the form data section
3. Choose an image file (JPEG, PNG, JPG, GIF, max 2MB)

### Error Handling
The collection includes comprehensive test scripts that:
- Validate response status codes (200, 201, 400, 401, 422, 500)
- Check for success/error message structure
- Ensure data structure is correct
- Auto-save authentication tokens

## Troubleshooting

### Common Issues

1. **401 Unauthorized:**
   - Check if `auth_token` is set correctly
   - Try logging in again to get a fresh token
   - Verify the token format: `Bearer {token}`

2. **404 Not Found:**
   - Verify the `base_url` is correct
   - Check if the API server is running
   - Ensure the endpoint path is correct

3. **422 Validation Error:**
   - Review the request body format
   - Check required fields are included
   - Verify file upload format for image requests

4. **500 Internal Server Error:**
   - Check server logs
   - Verify database connection
   - Ensure email configuration is set up (for password reset)

5. **File Upload Issues:**
   - Ensure file size is under 2MB
   - Use supported formats: JPEG, PNG, JPG, GIF
   - Check Content-Type is set to multipart/form-data

### Debug Mode

To enable debug mode:
1. Open Postman Console (View → Show Postman Console)
2. Send requests and check console for detailed logs
3. Look for automatic token saving messages

### Testing Password Reset

1. **Setup Email Configuration:**
   - Ensure your `.env` file has proper SMTP settings
   - Test email sending functionality

2. **Test Flow:**
   - Send "Forgot Password" request
   - Check email for reset token
   - Use token in "Reset Password" request
   - Login with new password

## API Changes Summary

### ✅ What's New
- **Password Reset:** Complete forgot/reset password flow
- **Profile Images:** Upload and manage profile pictures
- **Home Page Sliders:** Image and title sliders for mobile home page
- **No Pagination:** Units endpoint returns all units
- **Single Error Messages:** Cleaner error responses
- **Enhanced Authentication:** More comprehensive auth flow

### 🔄 What's Updated
- **Profile Management:** Now supports image uploads
- **Error Handling:** Consistent single-error format
- **Documentation:** Updated examples and descriptions
- **Testing:** Enhanced test scripts and automation

## Support

If you encounter issues:
1. Check the API documentation (`API_Documentation.md`)
2. Verify your environment variables
3. Test with the provided example requests
4. Check the troubleshooting section above
5. Contact the development team

## Next Steps

1. **Test all endpoints** using the collection
2. **Customize requests** for your specific needs
3. **Create additional environments** for different stages
4. **Share the collection** with your team
5. **Document any custom workflows** you create
6. **Test mobile app integration** with the new endpoints 