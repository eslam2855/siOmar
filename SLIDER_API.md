# Slider API Documentation

## Overview

The Slider API provides endpoints for managing home page sliders for the mobile application. Sliders are used to display promotional content, welcome messages, and featured content on the mobile home page.

## Base URL

```
http://localhost:8000/api
```

## Authentication

- **Public Endpoints:** No authentication required
- **Admin Endpoints:** Require admin authentication (for management)

## Endpoints

### 1. Get All Active Sliders

**GET** `/sliders`

Get all active sliders for the mobile home page.

#### Request
```http
GET /api/sliders
Accept: application/json
```

#### Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Welcome to SiOmar",
            "image": "sliders/welcome-slider.jpg",
            "order": 1
        },
        {
            "id": 2,
            "title": "Luxury Accommodations",
            "image": "sliders/luxury-slider.jpg",
            "order": 2
        }
    ]
}
```

#### Response Fields
- `id` - Unique slider identifier
- `title` - Slider title text
- `image` - Image file path (use with base URL)
- `order` - Display order (ascending)

### 2. Get Specific Slider

**GET** `/sliders/{id}`

Get details of a specific slider.

#### Request
```http
GET /api/sliders/1
Accept: application/json
```

#### Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Welcome to SiOmar",
        "image": "sliders/welcome-slider.jpg",
        "order": 1,
        "is_active": true,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

## Image URLs

To get the full URL for slider images, append the image path to your base URL:

```
Full Image URL = {{base_url}}/storage/{{image_path}}
```

Example:
```
http://localhost:8000/storage/sliders/welcome-slider.jpg
```

## Mobile App Integration

### Flutter Example

```dart
class SliderService {
  static const String baseUrl = 'http://localhost:8000/api';
  
  Future<List<Slider>> getSliders() async {
    final response = await http.get(
      Uri.parse('$baseUrl/sliders'),
      headers: {'Accept': 'application/json'},
    );
    
    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return (data['data'] as List)
          .map((json) => Slider.fromJson(json))
          .toList();
    }
    
    throw Exception('Failed to load sliders');
  }
}

class Slider {
  final int id;
  final String title;
  final String image;
  final int order;
  
  Slider({
    required this.id,
    required this.title,
    required this.image,
    required this.order,
  });
  
  factory Slider.fromJson(Map<String, dynamic> json) {
    return Slider(
      id: json['id'],
      title: json['title'],
      image: json['image'],
      order: json['order'],
    );
  }
  
  String get imageUrl => 'http://localhost:8000/storage/$image';
}
```

### React Native Example

```javascript
class SliderService {
  static baseUrl = 'http://localhost:8000/api';
  
  static async getSliders() {
    try {
      const response = await fetch(`${this.baseUrl}/sliders`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        return data.data.map(slider => ({
          ...slider,
          imageUrl: `http://localhost:8000/storage/${slider.image}`,
        }));
      }
      
      throw new Error('Failed to load sliders');
    } catch (error) {
      console.error('Error fetching sliders:', error);
      throw error;
    }
  }
}
```

## Slider Component Examples

### Flutter Slider Widget

```dart
class HomeSlider extends StatefulWidget {
  @override
  _HomeSliderState createState() => _HomeSliderState();
}

class _HomeSliderState extends State<HomeSlider> {
  List<Slider> sliders = [];
  int currentIndex = 0;
  
  @override
  void initState() {
    super.initState();
    loadSliders();
  }
  
  Future<void> loadSliders() async {
    try {
      final sliderList = await SliderService.getSliders();
      setState(() {
        sliders = sliderList;
      });
    } catch (e) {
      print('Error loading sliders: $e');
    }
  }
  
  @override
  Widget build(BuildContext context) {
    if (sliders.isEmpty) {
      return Container(
        height: 200,
        child: Center(child: CircularProgressIndicator()),
      );
    }
    
    return Container(
      height: 200,
      child: PageView.builder(
        itemCount: sliders.length,
        onPageChanged: (index) {
          setState(() {
            currentIndex = index;
          });
        },
        itemBuilder: (context, index) {
          final slider = sliders[index];
          return Container(
            margin: EdgeInsets.symmetric(horizontal: 8),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  color: Colors.black26,
                  blurRadius: 8,
                  offset: Offset(0, 4),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: Stack(
                children: [
                  Image.network(
                    slider.imageUrl,
                    width: double.infinity,
                    height: double.infinity,
                    fit: BoxFit.cover,
                  ),
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                        colors: [
                          Colors.transparent,
                          Colors.black54,
                        ],
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 16,
                    left: 16,
                    right: 16,
                    child: Text(
                      slider.title,
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
```

### React Native Slider Component

```javascript
import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  Image,
  Dimensions,
  ScrollView,
  StyleSheet,
} from 'react-native';
import { SliderService } from '../services/SliderService';

const { width } = Dimensions.get('window');

const HomeSlider = () => {
  const [sliders, setSliders] = useState([]);
  const [currentIndex, setCurrentIndex] = useState(0);
  
  useEffect(() => {
    loadSliders();
  }, []);
  
  const loadSliders = async () => {
    try {
      const sliderList = await SliderService.getSliders();
      setSliders(sliderList);
    } catch (error) {
      console.error('Error loading sliders:', error);
    }
  };
  
  const handleScroll = (event) => {
    const contentOffset = event.nativeEvent.contentOffset.x;
    const index = Math.round(contentOffset / width);
    setCurrentIndex(index);
  };
  
  if (sliders.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <Text>Loading...</Text>
      </View>
    );
  }
  
  return (
    <View style={styles.container}>
      <ScrollView
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        onScroll={handleScroll}
        scrollEventThrottle={16}
      >
        {sliders.map((slider, index) => (
          <View key={slider.id} style={styles.slide}>
            <Image
              source={{ uri: slider.imageUrl }}
              style={styles.image}
              resizeMode="cover"
            />
            <View style={styles.overlay}>
              <Text style={styles.title}>{slider.title}</Text>
            </View>
          </View>
        ))}
      </ScrollView>
      
      <View style={styles.pagination}>
        {sliders.map((_, index) => (
          <View
            key={index}
            style={[
              styles.paginationDot,
              index === currentIndex && styles.paginationDotActive,
            ]}
          />
        ))}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    height: 200,
    position: 'relative',
  },
  loadingContainer: {
    height: 200,
    justifyContent: 'center',
    alignItems: 'center',
  },
  slide: {
    width,
    height: 200,
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  overlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 16,
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  title: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  pagination: {
    position: 'absolute',
    bottom: 8,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  paginationDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: 'rgba(255,255,255,0.5)',
    marginHorizontal: 4,
  },
  paginationDotActive: {
    backgroundColor: 'white',
  },
});

export default HomeSlider;
```

## Error Responses

### 404 Not Found
```json
{
    "success": false,
    "message": "Slider not found"
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Internal server error"
}
```

## Best Practices

### 1. Image Optimization
- Use appropriate image sizes for mobile devices
- Compress images to reduce loading time
- Consider using WebP format for better compression

### 2. Caching
- Implement client-side caching for slider images
- Use appropriate cache headers for API responses

### 3. Error Handling
- Always handle network errors gracefully
- Show loading states while fetching data
- Provide fallback content if sliders fail to load

### 4. Performance
- Load sliders asynchronously
- Implement lazy loading for images
- Consider preloading next/previous slides

## Testing

### cURL Examples

```bash
# Get all sliders
curl -X GET "http://localhost:8000/api/sliders" \
  -H "Accept: application/json"

# Get specific slider
curl -X GET "http://localhost:8000/api/sliders/1" \
  -H "Accept: application/json"
```

### Postman Collection

Import the updated Postman collection which includes slider endpoints for testing.

## Notes

- Sliders are ordered by the `order` field in ascending order
- Only active sliders (`is_active = true`) are returned by the public API
- Image URLs are relative paths that need to be combined with the base storage URL
- The API is designed for mobile app consumption with minimal data transfer
