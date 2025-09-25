# Profile Image API Documentation

## Overview
The profile image functionality is now integrated into the main profile update endpoint. Users can upload, update, and remove their profile pictures through the single `PUT /api/profile` endpoint.

## API Endpoint

### Update Profile (with optional image)
**PUT** `/api/profile`

Updates user profile information including optional profile image.

**Headers:**
- `Authorization: Bearer {token}`
- `Accept: application/json`
- `Content-Type: multipart/form-data` (if uploading image)

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890",
    "profile_image": [file] // Optional image file
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
        "phone_number": "+1234567890",
        "profile_image": "profiles/profile_1234567890_1.jpg",
        "profile_image_url": "http://localhost:8000/storage/profiles/profile_1234567890_1.jpg",
        "created_at": "2025-08-11T20:51:47.000000Z",
        "updated_at": "2025-08-11T20:51:47.000000Z"
    }
}
```

### Get Profile
**GET** `/api/profile`

Returns user profile information including profile image URL.

**Headers:**
- `Authorization: Bearer {token}`
- `Accept: application/json`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone_number": "+1234567890",
        "profile_image": "profiles/profile_1234567890_1.jpg",
        "profile_image_url": "http://localhost:8000/storage/profiles/profile_1234567890_1.jpg",
        "created_at": "2025-08-11T20:51:47.000000Z",
        "updated_at": "2025-08-11T20:51:47.000000Z"
    }
}
```

## Usage Scenarios

### 1. Update Profile Information Only
Send a request without the `profile_image` field:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890"
}
```

### 2. Update Profile with New Image
Include the image file in the request:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890",
    "profile_image": [file]
}
```

### 3. Remove Profile Image
Send a request with an empty or null value for `profile_image`:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone_number": "+1234567890",
    "profile_image": null
}
```

## Image Requirements

- **Supported formats**: JPEG, PNG, JPG, GIF
- **Maximum size**: 2MB (2048 KB)
- **Storage location**: `storage/app/public/profiles/`
- **File naming**: `profile_{timestamp}_{user_id}.{extension}`

## Error Responses

### Validation Errors (422)
```json
{
    "success": false,
    "message": "The profile image must be an image."
}
```

### File Size Error (422)
```json
{
    "success": false,
    "message": "The profile image may not be greater than 2048 kilobytes."
}
```

### File Type Error (422)
```json
{
    "success": false,
    "message": "The profile image must be a file of type: jpeg, png, jpg, gif."
}
```

## Implementation Details

### Database Changes
- Added `profile_image` column to `users` table
- Column type: `VARCHAR(255)` nullable

### Model Changes
- Added `profile_image` to `$fillable` array in User model
- Added `profile_image_url` accessor for full URL generation

### File Management
- Old profile images are automatically deleted when a new one is uploaded
- Images are stored in the `profiles` directory within public storage
- Full URLs are generated using the `asset()` helper

### Security
- Only authenticated users can update their own profile
- File type and size validation
- Automatic cleanup of old files

## Usage Examples

### Using cURL to update profile with image
```bash
curl -X PUT http://localhost:8000/api/profile \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "phone_number=+1234567890" \
  -F "profile_image=@/path/to/image.jpg"
```

### Using JavaScript/Fetch
```javascript
const formData = new FormData();
formData.append('name', 'John Doe');
formData.append('email', 'john@example.com');
formData.append('phone_number', '+1234567890');
formData.append('profile_image', fileInput.files[0]);

fetch('/api/profile', {
    method: 'PUT',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    },
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

### Using JavaScript/Fetch (without image)
```javascript
const formData = new FormData();
formData.append('name', 'John Doe');
formData.append('email', 'john@example.com');
formData.append('phone_number', '+1234567890');

fetch('/api/profile', {
    method: 'PUT',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    },
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## Benefits of This Approach

1. **Simplified API**: Single endpoint for all profile updates
2. **Better UX**: Users can update all profile information in one request
3. **Reduced Complexity**: Fewer endpoints to maintain and document
4. **Consistent Behavior**: All profile changes go through the same validation and processing
5. **Atomic Updates**: Profile information and image are updated together
