# Juicebox Laravel API Test

A RESTful API built with Laravel 12 demonstrating authentication, CRUD operations, external API integration, background jobs, and automated testing.

## 🚀 Features

- ✅ RESTful API design with proper HTTP methods and status codes
- ✅ JWT-like authentication using Laravel Sanctum
- ✅ User registration and login with token-based auth
- ✅ Posts CRUD with ownership validation
- ✅ Pagination for list endpoints
- ✅ External Weather API integration (WeatherAPI.com)
- ✅ 15-minute caching strategy for weather data
- ✅ Background jobs with queues
- ✅ Automated email notifications on user registration
- ✅ Scheduled tasks for periodic data updates
- ✅ Comprehensive API testing with PHPUnit
- ✅ Request validation and error handling

---

## 📋 Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Queue Worker](#queue-worker)
- [Scheduler](#scheduler)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Artisan Commands](#artisan-commands)
- [Project Structure](#project-structure)
- [Technologies Used](#technologies-used)

---

## 🔧 Requirements

- PHP 8.1 or higher
- Composer 2.x
- MySQL 5.7 or higher
- Node.js & NPM (optional, for frontend assets)

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/chrisimbolon/juicebox-laravel-test
cd juicebox-laravel-test
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Copy Environment File
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

---

## ⚙️ Configuration

### Database Configuration

Update your `.env` file with MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=juicebox_test
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Queue Configuration

Configure queue driver to use database:
```env
QUEUE_CONNECTION=database
```

### Weather API Configuration

Sign up for a free API key at [WeatherAPI.com](https://www.weatherapi.com/) and add to `.env`:
```env
WEATHER_API_KEY=your_weatherapi_key_here
```

Add to `config/services.php`:
```php
'weather' => [
    'key' => env('WEATHER_API_KEY'),
],
```

---

## 🗄️ Database Setup

### 1. Create Database
```bash
mysql -u root -p
```
```sql
CREATE DATABASE juicebox_test;
EXIT;
```

### 2. Run Migrations
```bash
php artisan migrate
```

This will create the following tables:
- `users` - User accounts
- `posts` - Blog posts with user relationship
- `personal_access_tokens` - Sanctum authentication tokens
- `jobs` - Queue jobs
- `failed_jobs` - Failed queue jobs

### 3. (Optional) Seed Database
```bash
php artisan db:seed
```

---

## 🚀 Running the Application

### Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://127.0.0.1:8000`

### Test the API
```bash
curl -X GET "http://127.0.0.1:8000/api/weather" \
-H "Accept: application/json"
```

---

## 📨 Queue Worker

The application uses queues for background jobs (welcome emails, weather updates).

### Start Queue Worker
```bash
php artisan queue:work
```

**Important:** Keep this running in a separate terminal during development.

### Monitor Queue

Check pending jobs:
```bash
php artisan queue:listen
```

View failed jobs:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

---

## ⏰ Scheduler

The application uses Laravel's task scheduler for periodic jobs.

### Development

Run the scheduler daemon (keeps running):
```bash
php artisan schedule:work
```

Or run scheduled tasks once (for testing):
```bash
php artisan schedule:run
```

List all scheduled tasks:
```bash
php artisan schedule:list
```

### Production

Add this single cron entry to your server:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This cron runs every minute, and Laravel decides which scheduled tasks to execute.

### Scheduled Tasks

- **Weather Update**: Runs hourly to refresh weather cache
  - Job: `UpdateWeatherDataJob`
  - Schedule: `->hourly()`
  - Purpose: Keep weather data fresh for instant API responses

---

## 📖 API Documentation

### Interactive Swagger UI

Access the interactive API documentation at:
```
http://127.0.0.1:8000/api/documentation
```

**Features:**
- ✅ Try all endpoints directly in browser
- ✅ Built-in authentication testing
- ✅ Request/response examples
- ✅ Schema validation
- ✅ Export as OpenAPI JSON

### Generate Documentation

If you make changes to API annotations:
```bash
php artisan l5-swagger:generate
```

### OpenAPI JSON

Access the raw OpenAPI specification at:
```
http://127.0.0.1:8000/docs/api-docs.json
```

This can be imported into:
- Postman
- Insomnia  
- API testing tools
- Code generators
---

### Authentication Endpoints

#### Register User
```http
POST /api/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-11-27T10:00:00.000000Z"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxx"
}
```

**Notes:**
- Welcome email job is automatically queued
- Token is used for authentication in subsequent requests

---

#### Login
```http
POST /api/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xxxxxxxxxxxxxxxxxxxxxx"
}
```

**Error Response (401 Unauthorized):**
```json
{
  "message": "Invalid credentials"
}
```

---

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Logged out"
}
```

---

### Posts Endpoints

#### List All Posts
```http
GET /api/posts?page=1&per_page=15
```

**Response (200 OK):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "My First Post",
      "content": "Post content here...",
      "user_id": 1,
      "created_at": "2025-11-27T10:00:00.000000Z",
      "updated_at": "2025-11-27T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      }
    }
  ],
  "per_page": 15,
  "total": 1
}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)

---

#### Get Single Post
```http
GET /api/posts/{id}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "My First Post",
  "content": "Post content here...",
  "user_id": 1,
  "created_at": "2025-11-27T10:00:00.000000Z",
  "updated_at": "2025-11-27T10:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

**Error (404 Not Found):**
```json
{
  "message": "No query results for model [App\\Models\\Post] 999"
}
```

---

#### Create Post
```http
POST /api/posts
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "title": "My New Post",
  "content": "This is the content of my post."
}
```

**Response (201 Created):**
```json
{
  "id": 2,
  "title": "My New Post",
  "content": "This is the content of my post.",
  "user_id": 1,
  "created_at": "2025-11-27T11:00:00.000000Z",
  "updated_at": "2025-11-27T11:00:00.000000Z"
}
```

**Validation Errors (422):**
```json
{
  "message": "The title field is required. (and 1 more error)",
  "errors": {
    "title": ["The title field is required."],
    "content": ["The content field is required."]
  }
}
```

---

#### Update Post
```http
PATCH /api/posts/{id}
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "title": "Updated Title",
  "content": "Updated content"
}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "title": "Updated Title",
  "content": "Updated content",
  "user_id": 1,
  "created_at": "2025-11-27T10:00:00.000000Z",
  "updated_at": "2025-11-27T12:00:00.000000Z"
}
```

**Authorization Error (403 Forbidden):**
```json
{
  "message": "Forbidden"
}
```

**Notes:**
- Only the post owner can update the post
- Both fields are optional for PATCH requests

---

#### Delete Post
```http
DELETE /api/posts/{id}
Authorization: Bearer {token}
```

**Response (204 No Content):**
```
(Empty response body)
```

**Authorization Error (403 Forbidden):**
```json
{
  "message": "Forbidden"
}
```

**Notes:**
- Only the post owner can delete the post

---

### User Endpoints

#### Get User Details
```http
GET /api/users/{id}
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "created_at": "2025-11-27T10:00:00.000000Z",
  "updated_at": "2025-11-27T10:00:00.000000Z",
  "posts": [
    {
      "id": 1,
      "title": "My First Post",
      "content": "Post content...",
      "created_at": "2025-11-27T10:00:00.000000Z"
    }
  ]
}
```

**Notes:**
- Includes all posts created by the user
- Requires authentication

---

### Weather Endpoint

#### Get Current Weather
```http
GET /api/weather
```

**Response (200 OK - From Cache):**
```json
{
  "source": "cache",
  "data": {
    "location": {
      "name": "Perth",
      "region": "Western Australia",
      "country": "Australia",
      "lat": -31.93,
      "lon": 115.83
    },
    "current": {
      "temp_c": 22.1,
      "temp_f": 71.8,
      "condition": {
        "text": "Sunny",
        "icon": "//cdn.weatherapi.com/weather/64x64/day/113.png"
      },
      "wind_kph": 27.0,
      "humidity": 35,
      "feelslike_c": 24.4
    }
  }
}
```

**Response (200 OK - From API):**
```json
{
  "source": "api",
  "data": {
    // Same structure as above
  }
}
```

**Error (502 Bad Gateway):**
```json
{
  "message": "Failed to retrieve weather",
  "error": "External API failed: ..."
}
```

**Features:**
- ✅ Data cached for 15 minutes
- ✅ Returns `source` indicator (cache or api)
- ✅ Graceful fallback to cache on API failure
- ✅ No authentication required (public endpoint)

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Feature tests
php artisan test --testsuite=Feature

# Unit tests
php artisan test --testsuite=Unit
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Test Categories

**Authentication Tests:**
- User registration
- User login
- Token generation
- Logout functionality

**Posts Tests:**
- List posts with pagination
- Get single post
- Create post (authenticated)
- Update post (with ownership check)
- Delete post (with ownership check)

**User Tests:**
- Get user details
- User-posts relationship

**Weather Tests:**
- Fetch weather data
- Cache functionality
- Mocked external API responses

---

## 🛠️ Artisan Commands

### Custom Commands

#### Send Welcome Email

Manually dispatch a welcome email job for a specific user:
```bash
php artisan email:welcome {userId}
```

**Example:**
```bash
php artisan email:welcome 1
```

**Output:**
```
✅ Welcome email job dispatched for user: John Doe (john@example.com)
📧 Check your queue worker to see the job processed!
```

**Error handling:**
```bash
php artisan email:welcome 999
```
```
❌ User with ID 999 not found!
```

---

#### Update Weather Data

Manually trigger weather data update:
```bash
php artisan weather:update
```

**Output:**
```
🌤️  Dispatching weather update job...
✅ Weather update job dispatched!
📋 Check queue worker and logs to see the job processed.
```

---

### Standard Commands
```bash
# Clear all caches
php artisan optimize:clear

# View routes
php artisan route:list

# Check queue status
php artisan queue:monitor

# View logs
tail -f storage/logs/laravel.log
```

---

## 📁 Project Structure
```
juicebox-laravel-test/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── SendWelcomeEmailCommand.php
│   │       └── TestWeatherUpdate.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── PostController.php
│   │   │       ├── UserController.php
│   │   │       └── WeatherController.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── PostRequest.php
│   │       └── RegisterRequest.php
│   ├── Jobs/
│   │   ├── SendWelcomeEmailJob.php
│   │   └── UpdateWeatherDataJob.php
│   └── Models/
│       ├── Post.php
│       └── User.php
├── config/
│   ├── database.php
│   └── services.php (Weather API config)
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php (API routes)
│   └── console.php (Scheduled tasks)
├── tests/
│   └── Feature/
│       ├── AuthTest.php
│       ├── PostTest.php
│       ├── UserTest.php
│       └── WeatherTest.php
├── .env.example
├── composer.json
└── README.md
```

---

## 🔐 Security

- ✅ Password hashing with bcrypt
- ✅ Laravel Sanctum for API token authentication
- ✅ CSRF protection
- ✅ SQL injection prevention via Eloquent ORM
- ✅ Request validation on all inputs
- ✅ Ownership verification for update/delete operations
- ✅ Rate limiting on API endpoints (configurable)

---

## 🛡️ Error Handling

### Standard HTTP Status Codes

- `200 OK` - Successful GET, PATCH
- `201 Created` - Successful POST
- `204 No Content` - Successful DELETE
- `401 Unauthorized` - Invalid/missing token
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors
- `502 Bad Gateway` - External API failure

### Error Response Format
```json
{
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY`
- [ ] Configure production database
- [ ] Set up cron job for scheduler
- [ ] Configure queue worker as a daemon (Supervisor)
- [ ] Enable HTTPS
- [ ] Set up monitoring and logging
- [ ] Configure rate limiting
- [ ] Set up database backups

### Queue Worker (Production)

Use Supervisor to keep queue worker running:
```ini
[program:juicebox-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/worker.log
stopwaitsecs=3600
```

---

## 🧰 Technologies Used

- **Framework:** Laravel 12.x
- **Language:** PHP 8.5
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **Queue:** Database driver
- **Cache:** File/Database driver
- **Testing:** PHPUnit
- **External API:** WeatherAPI.com
- **Task Scheduling:** Laravel Scheduler

---

## 📝 Development Notes

### Caching Strategy

Weather data is cached for 15 minutes to:
- Minimize external API calls
- Improve response times
- Stay within API rate limits
- Provide graceful fallback on API failures

### Queue Jobs

Two background jobs are implemented:

1. **SendWelcomeEmailJob**
   - Triggered: On user registration
   - Purpose: Send welcome email asynchronously
   - Prevents slow registration response

2. **UpdateWeatherDataJob**
   - Triggered: Hourly via scheduler
   - Purpose: Keep weather cache fresh
   - Ensures users always get instant responses

### API Design Decisions

- **RESTful conventions:** Standard HTTP verbs and status codes
- **Token authentication:** Stateless, scalable API auth
- **Pagination:** Prevents large data transfers
- **Eager loading:** Optimizes N+1 query problems
- **Request validation:** Form Request classes for clean controllers

---

## 🐛 Troubleshooting

### Queue Jobs Not Processing
```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Start queue worker
php artisan queue:work

# Check failed jobs
php artisan queue:failed
```

### Weather API Not Working
```bash
# Verify API key is set
php artisan tinker
>>> config('services.weather.key')

# Test manual API call
curl "http://api.weatherapi.com/v1/current.json?key=YOUR_KEY&q=Perth,Australia"

# Clear config cache
php artisan config:clear
```

### Database Connection Issues
```bash
# Test database connection
php artisan db:show

# Run migrations
php artisan migrate:fresh

# Check .env database credentials
cat .env | grep DB_
```

### Authentication Errors
```bash
# Clear cache
php artisan optimize:clear

# Regenerate key
php artisan key:generate

# Check token in database
php artisan tinker
>>> \DB::table('personal_access_tokens')->count()
```

---

## 📞 Support

For issues or questions about this project:

- **Email:** christyansimbolon@gmail.com
- **GitHub:** [yourusername/juicebox-laravel-test](https://github.com/chrisimbolon
- **Portfolio:** [chrisimbolon.dev](https://chrisimbolon.dev)

---

## 📄 License

This project is created as a technical assessment for Juicebox Indonesia.

---

## 🙏 Acknowledgments

- Laravel Framework - [laravel.com](https://laravel.com)
- WeatherAPI.com - Weather data provider
- Laravel Sanctum - API authentication

---

**Built with lots of coffee by Christyan Simbolon**

**Date:** November 2025