# Firebase Setup Guide

This guide will help you set up Firebase Cloud Messaging (FCM) for push notifications in the SiOmar application.

## Prerequisites

1. A Firebase project
2. Firebase service account credentials
3. FCM server key (legacy method)

## Setup Steps

### 1. Create Firebase Project

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select an existing one
3. Enable Cloud Messaging in the project settings

### 2. Generate Service Account Credentials

1. In Firebase Console, go to Project Settings > Service Accounts
2. Click "Generate new private key"
3. Download the JSON file
4. Place it in your Laravel project root or a secure location

### 3. Configure Environment Variables

Add the following to your `.env` file:

```env
# Firebase Configuration
FIREBASE_PROJECT=app
FIREBASE_CREDENTIALS=/path/to/your/firebase-service-account.json
GOOGLE_APPLICATION_CREDENTIALS=/path/to/your/firebase-service-account.json

# Legacy FCM Configuration (for backward compatibility)
FCM_SERVER_KEY=your_fcm_server_key_here
FCM_SENDER_ID=your_fcm_sender_id_here
```

### 4. Get FCM Server Key (Legacy Method)

1. In Firebase Console, go to Project Settings > Cloud Messaging
2. Copy the "Server key" from the Cloud Messaging tab
3. Add it to your `.env` file as `FCM_SERVER_KEY`

### 5. Test the Setup

You can test the Firebase setup by:

1. Running the notification command:
   ```bash
   php artisan notifications:process-scheduled
   ```

2. Creating a test notification through the API:
   ```bash
   POST /api/notifications/create
   {
     "type": "system",
     "title": "Test Notification",
     "message": "This is a test notification",
     "priority": "normal",
     "is_global": true
   }
   ```

## Firebase SDK Features

The application now uses the official Firebase PHP SDK which provides:

- **Better Error Handling**: More detailed error messages and proper exception handling
- **Platform-Specific Configurations**: Different settings for Android and iOS
- **Token Validation**: Automatic detection and handling of invalid tokens
- **Message Targeting**: Support for different message types and targeting options
- **Delivery Tracking**: Better tracking of message delivery status

## Platform-Specific Features

### Android
- Custom notification icons
- Sound and vibration settings
- Click actions
- Priority levels

### iOS
- Badge numbers
- Sound settings
- APNS priority levels
- Rich notifications

## Troubleshooting

### Common Issues

1. **Invalid Credentials**
   - Ensure the service account JSON file path is correct
   - Check file permissions
   - Verify the JSON file is valid

2. **Token Invalid Errors**
   - The system automatically deactivates invalid tokens
   - Users need to re-register their push tokens
   - Check if the app is properly configured for FCM

3. **Permission Denied**
   - Ensure the service account has the necessary permissions
   - Check if Cloud Messaging is enabled in Firebase Console

### Logs

Check the Laravel logs for detailed error messages:
```bash
tail -f storage/logs/laravel.log
```

## Security Notes

- Never commit the service account JSON file to version control
- Use environment variables for all sensitive configuration
- Regularly rotate your service account keys
- Monitor Firebase usage and costs

## API Endpoints

The following endpoints are available for push notification management:

### User Endpoints
- `POST /api/push-tokens/register` - Register a push token
- `POST /api/push-tokens/unregister` - Unregister a push token
- `GET /api/push-tokens` - Get user's push tokens

### Admin Endpoints
- `POST /api/notifications/create` - Create and send notifications
- `GET /api/notifications/statistics` - Get notification statistics
- `POST /api/notifications/process-scheduled` - Process scheduled notifications

## Next Steps

1. Set up your Firebase project
2. Configure the environment variables
3. Test with a simple notification
4. Integrate with your mobile app
5. Monitor and optimize notification delivery
