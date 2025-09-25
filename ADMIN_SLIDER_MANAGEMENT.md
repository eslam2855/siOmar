# Admin Slider Management

## Overview

The Admin Slider Management interface allows administrators to manage home page sliders for the mobile application. This includes creating, editing, deleting, and organizing sliders that will be displayed on the mobile app's home page.

## Access

- **URL:** `/admin/sliders`
- **Authentication:** Admin login required
- **Role:** Admin users only

## Features

### 1. Slider List View (`/admin/sliders`)

#### Navigation
- Access via admin navigation menu: "Sliders"
- Direct URL: `/admin/sliders`

#### Features
- **View all sliders** in a table format
- **Search sliders** by title
- **Filter by status** (Active/Inactive)
- **Sort by order** (ascending)
- **Pagination** for large lists

#### Table Columns
- **Order:** Display order number
- **Image:** Thumbnail preview of slider image
- **Title:** Slider title text
- **Status:** Active/Inactive indicator
- **Created:** Creation date
- **Actions:** Edit, Toggle Status, Delete

#### Actions Available
- **Edit:** Navigate to edit form
- **Activate/Deactivate:** Toggle slider status
- **Delete:** Remove slider (with confirmation)

### 2. Create New Slider (`/admin/sliders/create`)

#### Form Fields
- **Title** (Required): Slider title text
- **Image** (Required): Upload image file
  - Accepted formats: JPEG, PNG, JPG, GIF
  - Maximum size: 2MB
- **Display Order** (Optional): Numeric order for display
  - Leave empty to add at the end
  - Lower numbers appear first
- **Active Status** (Checkbox): Enable/disable slider
  - Only active sliders appear on mobile app

#### Validation
- Title: Required, max 255 characters
- Image: Required, valid image format, max 2MB
- Order: Optional, integer, minimum 0

### 3. Edit Slider (`/admin/sliders/{id}/edit`)

#### Form Features
- **Pre-filled fields** with current values
- **Current image preview** (if exists)
- **Optional image update** (keep existing or upload new)
- **All fields editable**

#### Image Handling
- Shows current image if exists
- Optional file upload for new image
- Old image automatically deleted when new one uploaded

### 4. Slider Management Actions

#### Toggle Status
- **Purpose:** Activate/deactivate sliders
- **Effect:** Only active sliders appear on mobile app
- **Method:** POST to `/admin/sliders/{id}/toggle`

#### Delete Slider
- **Purpose:** Remove slider completely
- **Effect:** Deletes slider and associated image file
- **Confirmation:** Required before deletion
- **Method:** DELETE to `/admin/sliders/{id}`

#### Reorder Sliders
- **Purpose:** Change display order
- **Method:** POST to `/admin/sliders/reorder`
- **Data:** Array of slider IDs in desired order

## Image Requirements

### Supported Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

### Size Limits
- **Maximum file size:** 2MB
- **Recommended dimensions:** 1200x600 pixels (2:1 aspect ratio)
- **Minimum dimensions:** 800x400 pixels

### Storage
- Images stored in `storage/app/public/sliders/`
- Automatic filename generation with timestamp
- Old images deleted when replaced

## Mobile App Integration

### API Endpoint
- **Public endpoint:** `GET /api/sliders`
- **Authentication:** Not required
- **Response:** JSON with active sliders only

### Response Format
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Welcome to SiOmar",
            "image": "sliders/welcome-slider.jpg",
            "order": 1
        }
    ]
}
```

### Image URLs
- **Base URL:** `http://localhost:8000/storage/`
- **Full URL:** `http://localhost:8000/storage/sliders/filename.jpg`

## Best Practices

### Content Guidelines
1. **Title Length:** Keep titles concise (under 50 characters)
2. **Image Quality:** Use high-quality, optimized images
3. **Aspect Ratio:** Maintain 2:1 ratio for consistent display
4. **File Size:** Optimize images to reduce loading time

### Management Tips
1. **Order Planning:** Plan display order before creating sliders
2. **Status Management:** Use inactive status for temporary hiding
3. **Regular Updates:** Review and update sliders periodically
4. **Backup Images:** Keep original images for future use

### Performance Considerations
1. **Image Optimization:** Compress images before upload
2. **File Formats:** Use JPEG for photos, PNG for graphics
3. **Storage Cleanup:** Regularly review and delete unused images

## Troubleshooting

### Common Issues

#### Image Upload Fails
- **Check file size** (max 2MB)
- **Verify file format** (JPEG, PNG, JPG, GIF only)
- **Ensure storage permissions** are correct

#### Slider Not Appearing on Mobile
- **Check active status** is enabled
- **Verify order** is set correctly
- **Check API endpoint** is accessible

#### Image Not Displaying
- **Verify storage link** is created (`php artisan storage:link`)
- **Check file permissions** on storage directory
- **Confirm image path** is correct

### Error Messages

#### Validation Errors
- **Title required:** Enter a title for the slider
- **Image required:** Select an image file
- **Invalid image:** Use supported image format
- **File too large:** Reduce image size to under 2MB

#### System Errors
- **Storage error:** Check disk space and permissions
- **Database error:** Verify database connection
- **Permission denied:** Ensure admin role is assigned

## Security

### Access Control
- **Authentication required** for all admin routes
- **Admin role required** for slider management
- **CSRF protection** on all forms

### File Upload Security
- **File type validation** prevents malicious uploads
- **Size limits** prevent abuse
- **Automatic filename generation** prevents conflicts

### Data Protection
- **Input validation** on all fields
- **SQL injection protection** via Eloquent ORM
- **XSS protection** via Blade templating

## API Integration

### For Developers
The slider management system provides a RESTful API for mobile app integration:

```bash
# Get all active sliders
curl -X GET "http://localhost:8000/api/sliders" \
  -H "Accept: application/json"

# Get specific slider
curl -X GET "http://localhost:8000/api/sliders/1" \
  -H "Accept: application/json"
```

### Response Headers
- **Content-Type:** `application/json`
- **Cache-Control:** `no-cache` (for dynamic content)

### Error Handling
- **404:** Slider not found
- **500:** Server error
- **422:** Validation error (for admin endpoints)

## Maintenance

### Regular Tasks
1. **Review sliders** monthly for relevance
2. **Update images** for seasonal content
3. **Clean up inactive** sliders
4. **Monitor storage** usage

### Backup Strategy
1. **Database backup** includes slider records
2. **Image backup** from storage directory
3. **Configuration backup** for settings

### Performance Monitoring
1. **API response times** for mobile app
2. **Storage usage** for images
3. **Database performance** for queries
