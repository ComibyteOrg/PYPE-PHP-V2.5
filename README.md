# Pype PHP Framework V2.5 
### The Professional PHP Framework By Comibyte

<div align="center">
  <img src="https://imgs.search.brave.com/a2QJ4QGpzGpXeDGHk1c-pL3FdZ-v47YnUIxeu4pjCe4/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9vbHV3/YWRpbXUtYWRlZGVq/aS53ZWIuYXBwL2lt/YWdlcy9sb2dvLnBu/Zw" alt="Comibyte Welcome Page" width="300">
  <br>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
    <img src="https://img.shields.io/badge/Status-In%20Development-orange?style=for-the-badge" alt="Status">
  </p>
</div>

Pype PHP V2 is a lightweight, expressive, and powerful PHP framework designed for speed and simplicity. It provides a Laravel-like experience with a fluent Query Builder, Twig templating, Social Authentication, and a robust Mailing system.

## Older Version
### [View Version 1 of Pype PHP Here](https://github.com/ComibyteOrg/Comibyte-PHP-Framework)
### [View Version 2.0 of Pype PHP Here](https://github.com/ComibyteOrg/PYPE-PHP-V2)

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [CLI Commands](#cli-commands)
- [Routing](#routing)
- [HTTP Layer](#http-layer)
  - [Controllers](#controllers)
  - [API Resources](#api-resources)
  - [API Response Formatting](#api-response-formatting)
- [Models & ORM](#models--orm)
  - [Model-Based ORM (Recommended)](#1-model-based-orm-recommended)
  - [DB Helper (Fluent Query Builder)](#2-db-helper-fluent-query-builder)
- [Authentication](#authentication)
  - [Full Authentication System Example](#full-authentication-system-example)
  - [Social Authentication](#social-authentication)
  - [Manual Authentication](#manual-authentication)
- [Migrations](#migrations)
  - [Django-Style Migrations](#django-style-migrations)
  - [Schema Builder Methods](#schema-builder-methods)
  - [Advanced Schema Operations](#advanced-schema-operations)
- [Logging](#logging)
  - [Logger Usage](#logger-usage)
  - [Log Configuration](#log-configuration)
- [Email](#email)
  - [Email Service](#email-service)
  - [Mailer Class (Alternative)](#mailer-class-alternative)
  - [Email Templates](#email-templates)
  - [Using Email in Controllers](#using-email-in-controllers)
  - [Email Configuration Options](#email-configuration-options)
  - [Supported Email Drivers](#supported-email-drivers)
- [Helpers](#helpers)
  - [ApiResponse](#apiresponse)
  - [Auth](#auth)
  - [CSRF Protection](#csrf-protection)
  - [Database Helper (DB)](#database-helper-db)
  - [Email Service](#email-service-1)
  - [File Uploader](#file-uploader)
  - [Logger](#logger)
  - [XSS Protection](#xss-protection)
  - [Helper](#helper)
  - [Validator](#validator)
  - [Global Functions](#global-functions)
- [Middleware](#middleware)
  - [Built-in Middleware](#built-in-middleware)
  - [Creating Custom Middleware](#creating-custom-middleware)
- [Configuration](#configuration)
- [Best Practices](#best-practices)

## Features

- **Model-View-Controller (MVC)** architecture
- **Django-style models and migrations** - Define schema in models, migrations execute them
- **Powerful ORM** with fluent query builder
- **Flexible routing system** with RESTful routes
- **Built-in authentication** with social login support
- **Database migrations** with tracking system
- **Twig templating engine** integration
- **Email service** with multiple drivers
- **File upload handling**
- **Validation and XSS protection**
- **Comprehensive CLI tools**

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node JS 22.16.0+
- Git Terminal
- Web server (Apache/Nginx) or use built-in PHP server
- Extensions: `pdo`, `mbstring`, `openssl`, `curl`. `Twig Language 2`

### Getting Started

1. Create Your project Folder & Clone the repository:
   ```bash
   git clone https://github.com/ComibyteOrg/PYPE-PHP-V2.5.git
   ```

2. Move the framework to your project directory:
   ```bash
   # If downloaded in a subfolder, move contents to project root
   mv PYPE-PHP-V2.5/* ./
   ```

3. Initialize the framework:
   ```bash
   php pype.php init
   ```

4. Configure your `.env` file:
   ```env
   DB_TYPE=sqlite
   DB_PATH=database.sqlite
   # Or for MySQL:
   # DB_TYPE=mysql
   # DB_HOST=localhost
   # DB_NAME=your_database
   # DB_USER=your_username
   # DB_PASS=your_password
   ```

6. Run migrations (Optional):
   ```bash
   php pype.php migrate
   ```

7. Start the development server:
   ```bash
   php pype.php serve
   ```

## Project Structure

```
project-root/
├── App/                 # Application code
│   ├── Controller/      # Controllers
│   ├── Models/          # Models
│   ├── Helper/          # Helper functions
│   └── Middleware/      # Middleware
├── Framework/           # Framework core
├── Resources/           # Assets, views
│   └── views/           # Twig templates
├── routes/              # Route definitions
│   └── web.php          # Web routes
├── Storage/             # File storage
├── migrations/          # Database migrations
├── vendor/              # Composer dependencies
├── .env                 # Environment variables
├── composer.json        # Dependencies
├── index.php            # Entry point
└── pype.php             # CLI tool
```


> [!IMPORTANT] > **Architecture Rule**: Always place your Controllers in `App/Controllers`, Models in `App/Models`, and Middleware in `App/Middleware`. The `Framework/` folder is reserved for the framework is internal mechanics.

## CLI Commands

Pype PHP comes with powerful CLI commands to accelerate development:

### Initialization
```bash
php pype.php init
```
Initialize a new Pype project with default structure.

### Model Generation
```bash
php pype.php make:model Post
# or
php pype.php createmodel Post
```
Create a new model with default schema method.

### Controller Generation
```bash
php pype.php createcontroller PostController
```
Create a new controller with basic methods.

### Migration Commands
```bash
php pype.php make:migration create_posts_table
```
Create a new migration file.

```bash
php pype.php migrate
```
Run all pending migrations.

```bash
php pype.php migrate:rollback
```
Rollback the last migration batch.

### View Generation
```bash
php pype.php createview home
```
Create a new Twig view file.

### Development Server
```bash
php pype.php serve
```
Start the built-in development server.

### Help
```bash
php pype.php help
```
Show available commands and usage.

## Routing

Pype uses a flexible routing system defined in `routes/web.php`:

### Basic Routes
```php
<?php
use Framework\Router\Route;

// Closure route
Route::get("/", function () {
    return view("home");
});

// Controller route
Route::get("/posts", "PostController@index");
Route::get("/posts/{id}", "PostController@show");
```

### HTTP Methods
```php
Route::get($uri, $action);      // GET request
Route::post($uri, $action);     // POST request
Route::put($uri, $action);      // PUT request
Route::patch($uri, $action);    // PATCH request
Route::delete($uri, $action);   // DELETE request
Route::options($uri, $action);  // OPTIONS request
```

### Route Parameters
```php
Route::get("/users/{id}", "UserController@show");
Route::get("/posts/{category}/{id}", "PostController@show");
```

### Named Routes
Assign names to routes for easy URL generation.

```php
Route::get("/dashboard/profile", [ProfileController::class, 'show'])->name('profile');

// Generate URLs to named routes
$url = Route::getUrl('profile');  // Returns: /dashboard/profile
```

### Route Groups
Group related routes with shared attributes like prefixes and middleware.

```php
// Group with prefix
Route::group(['prefix' => 'admin'], function() {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/settings', [AdminController::class, 'settings']);
});
// Creates: /admin/dashboard, /admin/users, /admin/settings

// Group with middleware
Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});

// Group with both prefix and middleware
Route::group(['prefix' => 'api/v1', 'middleware' => ['auth', 'cors']], function() {
    Route::get('/users', [ApiController::class, 'users']);
    Route::post('/users', [ApiController::class, 'create']);
    Route::get('/posts', [ApiController::class, 'posts']);
});
```

### Middleware
Apply middleware to routes or route groups for authentication, authorization, etc.

```php
// Register middleware
Route::registerMiddleware('auth', function($params, $next) {
    // Check if user is authenticated
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    return $next($params);
});

// Apply middleware to routes
Route::get("/dashboard", [DashboardController::class, 'index'])->middleware('auth');

// Multiple middleware
Route::get("/admin", [AdminController::class, 'index'])
    ->middleware(['auth', 'admin']);

// Skip CSRF protection for specific routes
Route::post("/webhook", [WebhookController::class, 'handle'])
    ->csrfExempt();
```

### Social Authentication Routes
Quickly set up social authentication routes.

```php
// Sets up Google, GitHub, and Facebook OAuth routes
Route::socialAuth();
// Creates:
// GET /auth/{provider} - Redirect to provider
// GET /auth/{provider}/callback - Handle callback
```

### Method Spoofing
Use POST requests to simulate PUT and DELETE methods.

```html
<!-- Form to delete a post -->
<form method="POST" action="/posts/1">
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit">Delete Post</button>
</form>
```

## Models & ORM

Pype provides two approaches for database operations:

### 1. Model-Based ORM (Recommended)
This Django-style approach defines schema in models and uses migrations to execute them.

#### Creating Models
Use the CLI command:
```bash
php pype.php make:model Post
```

#### Model Structure
```php
<?php

namespace App\Models;

use Framework\Model\Model;

class Post extends Model
{
    protected static $table = 'posts';
    protected static $primaryKey = 'id';

    public static function schema($table)
    {
        $table->id();
        $table->string('title', 255);
        $table->string('slug', 255)->unique();
        $table->text('content');
        $table->boolean('published')->default(false);
        $table->timestamps();
        $table->softDeletes();
    }
}
```

#### Model ORM Methods

##### Static Methods
```php
// Get all records
$posts = Post::all();

// Find by ID
$post = Post::find(1);

// Find by column
$user = Post::findBy('email', 'user@example.com');

// Filter with conditions
$published = Post::filter(['published' => true]);

// Get first record
$post = Post::first();

// Count records
$count = Post::count();

// Create new record
$post = Post::create(['title' => 'New Post', 'content' => 'Content']);

// Update record
Post::updateRecord(1, ['title' => 'Updated Title']);

// Delete record
Post::destroy(1);

// Truncate table
Post::truncate();

// Raw SQL query
$results = Post::raw("SELECT * FROM posts WHERE status = ?", [1]);
```

##### Instance Methods
```php
// Create and save
$post = new Post();
$post->title = 'New Post';
$post->content = 'Content';
$post->save();

// Update existing
$post = Post::find(1);
$post->title = 'Updated Title';
$post->save();

// Delete instance
$post = Post::find(1);
$post->remove();

// Access attributes
echo $post->title;
$post->title = 'New Title';

// Convert to array/json
$array = $post->toArray();
$json = $post->toJson();
```

### 2. DB Helper (Fluent Query Builder)
For more complex queries or direct database access without models.

#### DB Helper Methods
```php
use Framework\Helper\DB;

// Fluent Query Builder
$users = DB::users()->where('active', 1)->get();
$user = DB::users()->where('id', 5)->first();
$user = DB::users()->find(5);

// Advanced queries
$users = DB::users()
    ->select('id, name, email')
    ->where('age', 25, '>=')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Joins
$posts = DB::posts()
    ->join('users', 'posts.user_id', '=', 'users.id')
    ->where('users.active', 1)
    ->get();

// Aggregates
$count = DB::users()->count();
$total = DB::orders()->sum('amount');
$average = DB::ratings()->avg('score');
$min = DB::products()->min('price');
$max = DB::products()->max('price');

// Insert
$id = DB::users()->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'active' => 1
]);

// Update
DB::users()->update(['name' => 'Jane Doe'], ['id' => 5]);

// Delete
DB::users()->delete(['id' => 5]);

// Raw queries
$result = DB::table('users')->raw('SELECT * FROM users WHERE active = ?', [1]);
```

Both approaches can be used depending on your needs:
- Use **Model ORM** for structured, schema-defined operations with built-in methods
- Use **DB Helper** for complex queries, ad-hoc operations, or when you don't need model features
```

## Controllers

### Creating Controllers
Use the CLI command:
```bash
php pype.php createcontroller PostController
```

### Controller Structure
```php
<?php

namespace App\Controller;

use App\Models\Post;

class PostController
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store()
    {
        $data = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'published' => isset($_POST['published'])
        ];
        
        $post = Post::create($data);
        
        if ($post) {
            redirect('/posts');
        } else {
            // Handle error
        }
    }

    public function edit($id)
    {
        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }
        return view('posts.edit', compact('post'));
    }

    public function update($id)
    {
        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }
        
        $post->title = $_POST['title'];
        $post->content = $_POST['content'];
        $post->published = isset($_POST['published']);
        
        if ($post->save()) {
            redirect('/posts/' . $id);
        } else {
            // Handle error
        }
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->destroy($id);
        }
        redirect('/posts');
    }
}
```

## Views

Pype uses the Twig templating engine. Views are stored in `Resources/views/`.

### Basic View Structure
```html
<!-- Resources/views/posts/index.twig -->
<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
</head>
<body>
    <h1>All Posts</h1>
    {% for post in posts %}
        <div>
            <h2>{{ post.title }}</h2>
            <p>{{ post.content|truncate(150) }}</p>
            <a href="/posts/{{ post.id }}">Read More</a>
        </div>
    {% endfor %}
</body>
</html>
```

### Using Views in Controllers
```php
// Return a view with data
return view('posts.index', ['posts' => $posts]);

// Or with compact
return view('posts.show', compact('post'));
```

## Migrations

### Django-Style Migrations
Pype uses a Django-inspired approach where models define the schema and migrations execute them.

### Creating Migrations
```bash
php pype.php make:migration create_posts_table
```

### Migration Structure
```php
<?php

use Framework\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up()
    {
        // Create posts table based on model schema
        $this->createTable('posts', function($table) {
            // Call the model's schema method
            \App\Models\Post::schema($table);
        });
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

### Schema Builder Methods
```php
$table->id();                           // Auto-incrementing ID
$table->string('name', 255);           // VARCHAR(255)
$table->integer('count');               // INT
$table->text('description');            // TEXT
$table->boolean('active');              // BOOLEAN
$table->double('price', 8, 2);         // DOUBLE(8,2)
$table->date('birth_date');             // DATE
$table->datetime('published_at');       // DATETIME
$table->timestamp('created_at');        // TIMESTAMP
$table->json('metadata');               // JSON

// Modifiers
$table->string('name')->nullable();     // Allow NULL
$table->string('name')->default('John'); // Default value
$table->string('email')->unique();      // Unique constraint
$table->timestamps();                   // created_at & updated_at
$table->softDeletes();                  // deleted_at column

// Additional Methods
$table->time('time_field');             // TIME
$table->binary('data');                 // BINARY/BLOB
$table->enum('status', ['active', 'inactive']); // ENUM (MySQL specific)
$table->raw('custom_sql_definition');   // Raw SQL column definition
```

#### Advanced Schema Operations
```php
// Create table with engine and charset (MySQL)
$this->createTable('posts', function($table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->timestamps();
}); // Creates with ENGINE=InnoDB DEFAULT CHARSET=utf8mb4

// Use raw method for database-specific features
$this->createTable('products', function($table) {
    $table->id();
    $table->string('name');
    $table->raw('fulltext(name)');      // MySQL fulltext index
    $table->raw('spatial index (location)'); // MySQL spatial index
});
```

#### Complete List of Schema Methods
```php
// Column Types
$table->id();                           // Auto-incrementing ID
$table->string('name', 255);           // VARCHAR
$table->integer('count');               // INT
$table->text('description');            // TEXT
$table->boolean('active');              // BOOLEAN
$table->double('price', 8, 2);         // DOUBLE
$table->date('birth_date');             // DATE
$table->datetime('published_at');       // DATETIME
$table->timestamp('created_at');        // TIMESTAMP
$table->json('metadata');               // JSON
$table->time('time_field');             // TIME
$table->binary('data');                 // BINARY/BLOB
$table->enum('status', ['active', 'inactive']); // ENUM

// Modifiers
$column->nullable();                    // Allow NULL values
$column->default($value);               // Set default value
$column->unique();                      // Add UNIQUE constraint

// Table Methods
$table->timestamps();                   // Add created_at and updated_at
$table->softDeletes();                  // Add deleted_at for soft deletes
$table->raw('custom_sql');              // Add raw SQL definition
```

### Running Migrations
```bash
# Run pending migrations
php pype.php migrate

# Rollback last migration
php pype.php migrate:rollback
```

## Authentication

### Full Authentication System Example

Here's a complete example of implementing a full authentication system with registration, login, email verification, and password reset.

#### 1. Create the User Model

```bash
php pype.php make:model User
```

```php
<?php

namespace App\Models;

use Framework\Model\Model;

class User extends Model
{
    protected static $table = 'users';
    protected static $primaryKey = 'id';

    public static function schema($table)
    {
        $table->id();
        $table->string('name', 255);
        $table->string('email', 255)->unique();
        $table->string('password', 255);
        $table->string('avatar', 500)->nullable();
        $table->string('provider', 50)->nullable();
        $table->string('provider_id', 255)->nullable();
        $table->string('verification_token', 100)->nullable();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('remember_token', 100)->nullable();
        $table->timestamps();
        $table->softDeletes();
    }
}
```

#### 2. Run the Migration

```bash
php pype.php migrate
```

#### 3. Create Authentication Controllers

```bash
php pype.php createcontroller AuthController
php pype.php createcontroller PasswordResetController
```

**App/Controller/AuthController.php:**

```php
<?php

namespace App\Controller;

use App\Models\User;
use Framework\Helper\EmailService;
use Framework\Helper\Validator;
use Framework\Helper\Helper;
use Framework\Helper\XSSProtection;

class AuthController
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register()
    {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return view('auth.register', [
                'errors' => $validator->errors(),
                'old' => $_POST
            ]);
        }

        // Hash password
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Create user
        $user = User::create([
            'name' => XSSProtection::clean($_POST['name']),
            'email' => XSSProtection::clean($_POST['email']),
            'password' => $hashedPassword,
            'verification_token' => $verificationToken
        ]);

        if ($user) {
            // Send verification email
            $this->sendVerificationEmail($user, $verificationToken);
            
            Helper::flash('success', 'Registration successful! Please check your email to verify your account.');
            Helper::redirect('/login');
        } else {
            Helper::flash('error', 'Registration failed. Please try again.');
            Helper::redirect('/register');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        $validator = Validator::make($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return view('auth.login', [
                'errors' => $validator->errors(),
                'old' => $_POST
            ]);
        }

        $user = User::findBy('email', $_POST['email']);

        if ($user && password_verify($_POST['password'], $user->password)) {
            if (!$user->email_verified_at) {
                Helper::flash('error', 'Please verify your email address before logging in.');
                Helper::redirect('/login');
            }

            // Set session
            $_SESSION['user_id'] = $user->id;
            $_SESSION['auth_user'] = $user->toArray();

            // Remember me functionality
            if (isset($_POST['remember'])) {
                $rememberToken = bin2hex(random_bytes(32));
                $user->remember_token = $rememberToken;
                $user->save();
                
                setcookie('remember_me', $rememberToken, time() + (86400 * 30), '/');
            }

            Helper::redirect('/dashboard');
        } else {
            Helper::flash('error', 'Invalid credentials. Please try again.');
            Helper::redirect('/login');
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['auth_user']);
        
        if (isset($_COOKIE['remember_me'])) {
            unset($_COOKIE['remember_me']);
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        Helper::redirect('/');
    }

    public function verifyEmail($token)
    {
        $user = User::findBy('verification_token', $token);

        if ($user) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_token = null;
            $user->save();

            Helper::flash('success', 'Email verified successfully! You can now login.');
            Helper::redirect('/login');
        } else {
            Helper::flash('error', 'Invalid verification token.');
            Helper::redirect('/register');
        }
    }

    private function sendVerificationEmail($user, $token)
    {
        $email = new EmailService();
        $verificationLink = Helper::url('verify-email', ['token' => $token]);
        
        $email->sendTemplate(
            $user->email,
            'Verify Your Email Address',
            'emails.verify-email',
            [
                'name' => $user->name,
                'verification_link' => $verificationLink
            ]
        );
    }
}
```

#### 4. Create Password Reset Controller

**App/Controller/PasswordResetController.php:**

```php
<?php

namespace App\Controller;

use App\Models\User;
use Framework\Helper\EmailService;
use Framework\Helper\Validator;
use Framework\Helper\Helper;
use Framework\Helper\XSSProtection;

class PasswordResetController
{
    public function showRequestForm()
    {
        return view('auth.password.request');
    }

    public function sendResetLink()
    {
        $validator = Validator::make($_POST, [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return view('auth.password.request', [
                'errors' => $validator->errors(),
                'old' => $_POST
            ]);
        }

        $user = User::findBy('email', $_POST['email']);

        if ($user) {
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetTokenExpiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Update user with reset token
            $user->remember_token = $resetToken; // Using remember_token for reset
            $user->save();

            // Send reset email
            $this->sendResetEmail($user, $resetToken);

            Helper::flash('success', 'Password reset link sent to your email.');
            Helper::redirect('/login');
        } else {
            Helper::flash('error', 'No user found with that email address.');
            Helper::redirect('/password/reset');
        }
    }

    public function showResetForm($token)
    {
        // Check if token is valid
        $user = User::findBy('remember_token', $token);
        
        if (!$user) {
            Helper::flash('error', 'Invalid password reset token.');
            Helper::redirect('/password/reset');
        }

        return view('auth.password.reset', ['token' => $token]);
    }

    public function resetPassword($token)
    {
        $user = User::findBy('remember_token', $token);

        if (!$user) {
            Helper::flash('error', 'Invalid password reset token.');
            Helper::redirect('/password/reset');
        }

        $validator = Validator::make($_POST, [
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return view('auth.password.reset', [
                'token' => $token,
                'errors' => $validator->errors()
            ]);
        }

        // Update password
        $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $user->remember_token = null; // Clear the reset token
        $user->save();

        Helper::flash('success', 'Password reset successfully! You can now login.');
        Helper::redirect('/login');
    }

    private function sendResetEmail($user, $token)
    {
        $email = new EmailService();
        $resetLink = Helper::url('password.reset.form', ['token' => $token]);
        
        $email->sendTemplate(
            $user->email,
            'Reset Your Password',
            'emails.password-reset',
            [
                'name' => $user->name,
                'reset_link' => $resetLink
            ]
        );
    }
}
```

#### 5. Create Email Templates

Create email templates in `Resources/views/emails/`:

**Resources/views/emails/verify-email.twig:**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
</head>
<body>
    <h1>Hello {{ name }}!</h1>
    <p>Thank you for registering. Please click the button below to verify your email address:</p>
    
    <a href="{{ verification_link }}" style="display: inline-block; padding: 10px 20px; background-color: #007cba; color: white; text-decoration: none; border-radius: 5px;">
        Verify Email Address
    </a>
    
    <p>If you didn't create an account, no further action is required.</p>
</body>
</html>
```

**Resources/views/emails/password-reset.twig:**
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password</title>
</head>
<body>
    <h1>Hello {{ name }}!</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    
    <a href="{{ reset_link }}" style="display: inline-block; padding: 10px 20px; background-color: #007cba; color: white; text-decoration: none; border-radius: 5px;">
        Reset Password
    </a>
    
    <p>This password reset link will expire in 1 hour.</p>
    
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>
```

#### 6. Create Authentication Views

**Resources/views/auth/register.twig:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    
    {% if flash('error') %}
        <div style="color: red;">{{ flash('error') }}</div>
    {% endif %}
    
    {% if flash('success') %}
        <div style="color: green;">{{ flash('success') }}</div>
    {% endif %}
    
    <form method="POST" action="/register">
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ old.name|default('') }}" required>
            {% if errors.name is defined %}
                <span style="color: red;">{{ errors.name[0] }}</span>
            {% endif %}
        </div>
        
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old.email|default('') }}" required>
            {% if errors.email is defined %}
                <span style="color: red;">{{ errors.email[0] }}</span>
            {% endif %}
        </div>
        
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
            {% if errors.password is defined %}
                <span style="color: red;">{{ errors.password[0] }}</span>
            {% endif %}
        </div>
        
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirmation" required>
            {% if errors.password is defined %}
                <span style="color: red;">Passwords do not match</span>
            {% endif %}
        </div>
        
        <button type="submit">Register</button>
    </form>
    
    <p><a href="/login">Already have an account? Login</a></p>
</body>
</html>
```

#### 7. Add Routes

Add these routes to `routes/web.php`:

```php
<?php
use Framework\Router\Route;

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])->name('verify-email');

// Password reset routes
Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/password/reset/{token}', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Protected routes
Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', function() {
        return view('dashboard');
    });
});
```

#### 8. Create Middleware

Create an authentication middleware:

```bash
php pype.php createmiddleware AuthMiddleware
```

**App/Middleware/AuthMiddleware.php:**
```php
<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle($params, $next)
    {
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login
            header('Location: /login');
            exit;
        }
        
        return $next($params);
    }
}
```

Register the middleware in your bootstrap code or route registration:
```php
Route::registerMiddleware('auth', AuthMiddleware::class);
```

### Social Authentication

Pype includes built-in social authentication with support for Google, GitHub, and Facebook.

#### Setup

1. Configure your `.env` file with provider credentials:
```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# GitHub OAuth (optional)
GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

# Facebook OAuth (optional)
FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

2. Set up the routes in `routes/web.php`:
```php
<?php
use Framework\Router\Route;

// Enable social authentication routes
Route::socialAuth();

// Or set up manually:
Route::get('/auth/{provider}', 'SocialAuthController@redirectToProvider');
Route::get('/auth/{provider}/callback', 'SocialAuthController@handleProviderCallback');
```

#### Database Schema

Make sure your users table includes social authentication fields:
```php
// In your User model schema
public static function schema($table)
{
    $table->id();
    $table->string('name', 255);
    $table->string('email', 255)->unique();
    $table->string('avatar', 500)->nullable();        // Profile picture
    $table->string('provider', 50)->nullable();       // Provider name (google, github, facebook)
    $table->string('provider_id', 255)->nullable();   // Provider user ID
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
}
```

#### Usage in Controllers

The SocialAuthController handles the authentication flow automatically:

```php
<?php
// The SocialAuthController is automatically available
// No need to create it manually

// Users will be redirected to provider login
// After successful authentication, they'll be redirected back
// A user account will be created/updated automatically
```

#### Customizing Social Authentication Flow

If you need custom logic, you can create your own controller:

```php
<?php

namespace App\Controller;

use Framework\Auth\SocialLoginManager;
use Framework\Helper\DB;
use Framework\Helper\Helper;

class CustomSocialAuthController
{
    private $manager;

    public function __construct()
    {
        $this->manager = new SocialLoginManager();
        $this->manager->autoRegisterProviders();
    }

    public function redirectToProvider($provider)
    {
        return $this->manager->redirectToProvider($provider);
    }

    public function handleProviderCallback($provider)
    {
        $code = $_GET['code'] ?? null;
        if (!$code) {
            Helper::redirect('/login?error=no_code');
            return;
        }

        try {
            $userData = $this->manager->handleCallback($provider, $code);

            // Custom user lookup/creation logic
            $user = DB::table('users')->where('email', $userData['email'])->first();

            if (!$user) {
                // Custom user creation
                $userId = DB::table('users')->insert([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'avatar' => $userData['avatar'],
                    'provider' => $userData['provider'],
                    'provider_id' => $userData['id'],
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);
                $user = DB::table('users')->find($userId);
            } else {
                // Update existing user with provider info
                DB::table('users')->update([
                    'avatar' => $userData['avatar'],
                    'provider' => $userData['provider'],
                    'provider_id' => $userData['id']
                ], ['id' => $user['id']]);
            }

            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['auth_user'] = $user;

            Helper::redirect('/dashboard');

        } catch (\Exception $e) {
            error_log("Social auth error: " . $e->getMessage());
            Helper::redirect('/login?error=social_auth_failed');
        }
    }
}
```

#### Available Providers

- **Google**: Requires `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- **GitHub**: Requires `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, `GITHUB_REDIRECT_URI`  
- **Facebook**: Requires `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`, `FACEBOOK_REDIRECT_URI`

#### Frontend Integration

Add social login buttons to your login page:

```html
<!-- Resources/views/auth/login.twig -->
<div class="social-login">
    <a href="/auth/google" class="btn btn-google">Login with Google</a>
    <a href="/auth/github" class="btn btn-github">Login with GitHub</a>
    <a href="/auth/facebook" class="btn btn-facebook">Login with Facebook</a>
</div>
```

### Manual Authentication

Use the Auth helper for traditional username/password authentication:

```php
use Framework\Helper\Auth;

// Login user
Auth::login($user);

// Check if user is logged in
if (Auth::check()) {
    $user = Auth::user();
}

// Logout
Auth::logout();

// Get user ID
$userId = Auth::id();

// Protect routes with middleware
Route::get('/dashboard', 'DashboardController@index')->middleware('auth');
```

#### Session Management

Social authentication automatically manages user sessions after successful login. The authenticated user data is stored in `$_SESSION['auth_user']`.
```

## HTTP Layer

Pype provides a comprehensive HTTP layer with controllers, resources, and API response handling.

### Controllers
Controllers handle HTTP requests and return responses.

```php
<?php

namespace App\Controller;

use Framework\Helper\ApiResponse;
use Framework\Helper\DB;

class UserController
{
    public function index()
    {
        $users = DB::table('users')->get();
        return ApiResponse::success($users, "Users retrieved successfully");
    }

    public function show($id)
    {
        $user = DB::table('users')->find($id);
        if (!$user) {
            return ApiResponse::error("User not found", 404);
        }
        return ApiResponse::success($user, "User retrieved successfully");
    }

    public function update($id)
    {
        $user = DB::table('users')->find($id);
        if (!$user) {
            return ApiResponse::error("User not found", 404);
        }

        $data = [
            'name' => $_POST['name'] ?? $user['name'],
            'email' => $_POST['email'] ?? $user['email'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        DB::table('users')->update($data, ['id' => $id]);
        $updatedUser = DB::table('users')->find($id);

        return ApiResponse::success($updatedUser, "User updated successfully");
    }

    public function destroy($id)
    {
        $user = DB::table('users')->find($id);
        if (!$user) {
            return ApiResponse::error("User not found", 404);
        }

        DB::table('users')->delete(['id' => $id]);

        return ApiResponse::success(null, "User deleted successfully");
    }
}
```

### API Resources
Transform models and collections for API responses.

```php
<?php

namespace App\Http\Resources;

use Framework\Http\Resources\Resource;

class UserResource extends Resource
{
    public static function make($user)
    {
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => $user['avatar'] ?? null,
            'created_at' => $user['created_at'] ?? null,
            'updated_at' => $user['updated_at'] ?? null
        ];
    }
}

// Usage in controller
public function index()
{
    $users = DB::table('users')->get();
    return ApiResponse::success(UserResource::collection($users), "Users retrieved successfully");
}
```

### API Response Formatting
Consistent response format for API endpoints.

```php
use Framework\Helper\ApiResponse;

// Success response
return ApiResponse::success($data, 'Operation successful', 200);

// Error response
return ApiResponse::error('Something went wrong', 400);
```

## Logging

Pype includes a robust logging system for tracking application events.

## Middleware

Middleware provides a convenient mechanism for filtering HTTP requests entering your application. You can perform authentication checks, set CORS headers, validate CSRF tokens, and more.

### Built-in Middleware

#### AuthMiddleware
Verify that the user is authenticated before accessing protected routes.

```php
use Framework\Middleware\AuthMiddleware;

// Apply to specific routes
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(AuthMiddleware::class);

// Apply to route groups
Route::group(['middleware' => AuthMiddleware::class], function() {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

#### GuestMiddleware
Ensure that the user is not authenticated (for login/register pages).

```php
use Framework\Middleware\GuestMiddleware;

Route::group(['middleware' => GuestMiddleware::class], function() {
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm']);
});
```

#### CorsMiddleware
Handle Cross-Origin Resource Sharing (CORS) for API requests.

```php
use Framework\Middleware\CorsMiddleware;

// Apply to API routes
Route::group(['prefix' => 'api', 'middleware' => CorsMiddleware::class], function() {
    Route::get('/users', [ApiController::class, 'users']);
    Route::post('/users', [ApiController::class, 'create']);
});
```

#### CsrfMiddleware
Protect against Cross-Site Request Forgery attacks.

```php
use Framework\Middleware\CsrfMiddleware;

// Automatically applied to POST routes by the framework
// You can also apply manually
Route::post('/submit-form', [FormController::class, 'store'])->middleware(CsrfMiddleware::class);

// Exempt specific routes from CSRF protection
CsrfMiddleware::csrf_exempt('/webhook');
```

#### RateLimitMiddleware
Limit the number of requests a client can make.

```php
use Framework\Middleware\RateLimitMiddleware;

// Limit to 60 requests per minute
Route::group(['middleware' => RateLimitMiddleware::class], function() {
    Route::get('/api/data', [ApiController::class, 'getData']);
});
```

### Creating Custom Middleware

Create your own middleware by implementing a class with a `handle` method:

```bash
php pype.php createmiddleware CustomMiddleware
```

**App/Middleware/CustomMiddleware.php:**
```php
<?php

namespace App\Middleware;

class CustomMiddleware
{
    public function handle($params, $next)
    {
        // Perform action before request is handled
        // e.g., log request, check permissions, etc.
        
        // Continue to next middleware/route
        $response = $next($params);
        
        // Perform action after request is handled
        // e.g., modify response, log response, etc.
        
        return $response;
    }
}
```

Register and use your custom middleware:

```php
// Register the middleware
Route::registerMiddleware('custom', CustomMiddleware::class);

// Apply to routes
Route::get('/protected', [ProtectedController::class, 'index'])->middleware('custom');
```

Middleware can also accept parameters:

```php
class RoleMiddleware
{
    public function handle($params, $next, $role = 'user')
    {
        // Check if user has required role
        if (!hasRole($role)) {
            header('Location: /unauthorized');
            exit;
        }
        
        return $next($params);
    }
}
```

### Logger Usage
```php
use Framework\Logging\Logger;

// Log different levels of messages
Logger::info('User logged in', ['user_id' => 123]);
Logger::error('Database connection failed', ['error' => $exception->getMessage()]);
Logger::warning('Deprecated function called', ['function' => 'old_function']);
Logger::debug('Debug information', ['data' => $debugData]);

// Enable/disable logging
Logger::enable();   // Enable logging
Logger::disable();  // Disable logging

// Get log file path
$logPath = Logger::getLogPath();  // Returns path to Storage/logs/app.log
```

### Log Configuration
Logs are automatically stored in `Storage/logs/app.log` and the directory is created if it doesn't exist.

## Helpers

Pype PHP provides a comprehensive set of helper classes to simplify common development tasks.

### Available Helpers

#### ApiResponse
Handle API responses with consistent format.

```php
use Framework\Helper\ApiResponse;

return ApiResponse::success($data, 'Operation successful', 200);
return ApiResponse::error('Something went wrong', 400);
```

#### Auth
Handle user authentication and session management.

```php
use Framework\Helper\Auth;

// Check if user is logged in
if (Auth::check()) {
    $user = Auth::user();  // Get authenticated user
    $userId = Auth::id();  // Get user ID
}

// Access user properties directly
echo Auth::name;     // Access user's name property
echo Auth::email;    // Access user's email property
```

#### CSRF Protection
Protect forms from Cross-Site Request Forgery attacks.

```php
use Framework\Helper\CSRF;

// Generate and display token in form
echo CSRF::getTokenField();  // Outputs hidden input field

// Get token value
$token = CSRF::generateToken();

// Validate token
if (CSRF::validateToken($_POST['csrf_token'])) {
    // Process form
} else {
    // Invalid token
}

// Clear token after successful form processing
CSRF::clearToken();
```

#### Database Helper (DB)
Fluent query builder for complex database operations.

```php
use Framework\Helper\DB;

// Fluent Query Builder
$users = DB::users()->where('active', 1)->get();
$user = DB::users()->where('id', 5)->first();
$user = DB::users()->find(5);

// Advanced queries
$users = DB::users()
    ->select('id, name, email')
    ->where('age', 25, '>=')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Joins
$posts = DB::posts()
    ->join('users', 'posts.user_id', '=', 'users.id')
    ->where('users.active', 1)
    ->get();

// Aggregates
$count = DB::users()->count();
$total = DB::orders()->sum('amount');
$average = DB::ratings()->avg('score');
$min = DB::products()->min('price');
$max = DB::products()->max('price');

// Insert
$id = DB::users()->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'active' => 1
]);

// Update
DB::users()->update(['name' => 'Jane Doe'], ['id' => 5]);

// Delete
DB::users()->delete(['id' => 5]);

// Raw queries
$result = DB::table('users')->raw('SELECT * FROM users WHERE active = ?', [1]);

// Additional DB Methods
DB::users()->whereNull('deleted_at');           // WHERE column IS NULL
DB::users()->whereNotNull('deleted_at');        // WHERE column IS NOT NULL  
DB::users()->whereIn('status', [1, 2, 3]);     // WHERE column IN (...)
DB::users()->orWhere('name', 'John', '=');     // OR WHERE clause
DB::users()->groupBy('status');                 // GROUP BY clause
DB::users()->offset(10);                        // OFFSET clause
DB::users()->leftJoin('profiles', 'users.id', '=', 'profiles.user_id'); // LEFT JOIN
DB::users()->pluck('name');                     // Get array of column values
DB::users()->exists();                          // Check if records exist
DB::users()->paginate(10, 1);                  // Paginate results
DB::users()->transaction(function($db) {        // Execute in transaction
    // Database operations
});
DB::users()->debug();                           // Enable query debugging
```

#### Email Service
Send emails via SMTP, mail() function, or log driver.

```php
use Framework\Helper\EmailService;

// Basic usage
$email = new EmailService();
$email->sendEmail('user@example.com', 'Subject', 'HTML Body', 'Plain text body');

// Send with template
$email->sendTemplate('user@example.com', 'Welcome!', 'welcome.html', [
    'name' => 'John',
    'link' => 'https://example.com'
]);

// Configure in .env
MAIL_DRIVER=smtp    # Options: smtp, log, mail
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_EMAIL=noreply@yourapp.com
MAIL_FROM_NAME="Your App Name"
```

#### Mailer Class (Alternative)
Simple email delivery class.

```php
use Framework\Mail\Mailer;

// Send an email
$result = Mailer::send('user@example.com', 'Subject', 'Email body');

// Queue an email for later delivery
$result = Mailer::queue('user@example.com', 'Subject', 'Email body', $delayInSeconds);

// The Mailer class uses the same .env configuration as EmailService
```

### Email Templates
Create reusable email templates in `Resources/views/emails/`.

```html
<!-- Resources/views/emails/welcome.html -->
<!DOCTYPE html>
<html>
<head>
    <title>Welcome!</title>
</head>
<body>
    <h1>Hello {{ name }}!</h1>
    <p>Welcome to our platform. Click <a href="{{ link }}">here</a> to get started.</p>
</body>
</html>
```

### Using Email in Controllers
Sending emails from controllers with proper error handling.

```php
<?php

namespace App\Controller;

use Framework\Helper\EmailService;
use Framework\Helper\Helper;

class UserController
{
    public function welcome($userId)
    {
        $user = DB::table('users')->find($userId);
        
        if (!$user) {
            return Helper::redirect('/users');
        }
        
        $email = new EmailService();
        $sent = $email->sendTemplate(
            $user['email'], 
            'Welcome to Our Platform!', 
            'emails.welcome', 
            [
                'name' => $user['name'],
                'link' => Helper::url('dashboard'),
                'activation_link' => Helper::url('activate', ['token' => $user['activation_token']])
            ]
        );
        
        if ($sent) {
            Helper::flash('success', 'Welcome email sent successfully!');
        } else {
            Helper::flash('error', 'Failed to send welcome email.');
        }
        
        return Helper::redirect('/users/' . $userId);
    }
    
    public function sendNotification($userId, $notification)
    {
        $user = DB::table('users')->find($userId);
        
        if (!$user) {
            return false;
        }
        
        $email = new EmailService();
        return $email->sendEmail(
            $user['email'],
            $notification['subject'],
            $notification['html_body'],
            $notification['text_body']
        );
    }
}
```

### Email Configuration Options
Different drivers for different environments:

**Development (.env):**
```env
MAIL_DRIVER=log          # Logs emails instead of sending
MAIL_FROM_EMAIL=dev@yourapp.local
```

**Production (.env):**
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-app@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_EMAIL=noreply@yourapp.com
MAIL_FROM_NAME="Your App Name"
```

### Supported Email Drivers
- **smtp**: Send via SMTP server (Gmail, Outlook, etc.)
- **mail**: Use PHP's built-in mail() function
- **log**: Log emails to file instead of sending (useful for development)
```

#### File Uploader
Handle file uploads with validation and security.

```php
use Framework\Helper\FileUploader;

// Upload file with allowed extensions
$filename = FileUploader::upload($_FILES['avatar'], 'uploads/avatars/', ['jpg', 'png', 'gif']);

// Check if directory is empty
if (FileUploader::isEmpty('uploads/temp')) {
    // Directory is empty
}

// Check if file exists
if (FileUploader::exists('uploads/image.jpg')) {
    // File exists
}
```

#### Logger
Log application events and errors.

```php
use Framework\Helper\Logger;

Logger::info('User logged in', ['user_id' => 123]);
Logger::error('Database connection failed', ['error' => $exception->getMessage()]);
Logger::warning('Deprecated function called', ['function' => 'old_function']);
```

#### XSS Protection
Clean user input to prevent cross-site scripting attacks.

```php
use Framework\Helper\XSSProtection;

// Clean single input
$safeContent = XSSProtection::clean($userInput);

// Clean array of inputs
$safeInputs = XSSProtection::cleanArray($_POST);

// Clean HTML content
$cleanHtml = XSSProtection::clean('<script>alert("XSS")</script><p>Safe content</p>');
```

#### Helper
General utility functions for common tasks.

```php
use Framework\Helper\Helper;

// Input sanitization
$safeInput = Helper::sanitize($_POST['input']);

// Redirect with delay
Helper::redirect('/dashboard', 2);  // Redirect after 2 seconds

// Redirect with flash message
Helper::redirectWith('/login', 'error', 'Invalid credentials');

// Show alerts
echo Helper::set_alert('success', 'Operation completed successfully');

// View rendering
Helper::view('pages.home', ['data' => $data]);

// JSON response
Helper::returnJson(['status' => 'success'], 200);

// Text utilities
$excerpt = Helper::excerpt($longText, 100);
$readingTime = Helper::readingTime($content);

// Environment and paths
$appUrl = Helper::env('APP_URL');
$assetUrl = Helper::asset('css/style.css');
$fullUrl = Helper::url('user.profile', ['id' => 123]);
$slug = Helper::slugify('My Page Title');

// Session management
Helper::session('user_id', 123);  // Set
$userId = Helper::session('user_id');  // Get

// Flash messages
Helper::flash('success', 'Item created successfully');
$message = Helper::getFlash('success');

// Authentication helpers
$user = Helper::auth();      // Get authenticated user
$userId = Helper::id();      // Get user ID
$loggedIn = Helper::check();  // Check if logged in
Helper::logout();            // Logout

// File operations
$uploaded = Helper::upload($_FILES['file'], 'uploads/', ['jpg', 'png']);

// Utility functions
$allInput = Helper::input();                    // Get all input
$value = Helper::input('field', 'default');     // Get specific input
$nestedValue = Helper::array_get($array, 'key.subkey');  // Get nested array value
$basePath = Helper::base_path();                // Get project root path
$appPath = Helper::app_path();                  // Get App directory path
$storagePath = Helper::storage_path();          // Get Storage directory path
$method = Helper::method();                     // Get request method (GET, POST, etc.)
$oldValue = Helper::old('field');               // Get old input value after validation failure

// Debugging
Helper::dd($variable);  // Dump and die
```

#### Validator
Validate form input data with multiple rules.

```php
use Framework\Helper\Validator;

// Basic validation
$validator = new Validator($_POST);
$validator->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|max:50'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle validation errors
}

// Or use static method
$validator = Validator::make($_POST, [
    'name' => 'required|min:3',
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle errors
}

// Available Validation Rules

**Required Rules:**
- `required` - Field must not be empty

**String Validation:**
- `min:length` - Minimum character length (e.g., `min:8`)
- `max:length` - Maximum character length (e.g., `max:50`)
- `between:min,max` - Between minimum and maximum lengths (e.g., `between:5,20`)
- `alpha` - Contains only letters
- `alpha_num` - Contains only letters and numbers
- `alpha_dash` - Contains only letters, numbers, dashes and underscores

**Numeric Validation:**
- `numeric` - Field must be numeric
- `integer` - Field must be an integer

**Format Validation:**
- `email` - Value must be a valid email address
- `url` - Value must be a valid URL
- `ip` - Value must be a valid IP address
- `regex:pattern` - Field must match the given regular expression pattern

**List Validation:**
- `in:value1,value2,value3` - Field must be one of the specified values
- `not_in:value1,value2,value3` - Field must NOT be one of the specified values

**Confirmation Validation:**
- `confirmed` - Field must have a matching confirmation field (e.g., password and password_confirmation)

**Example Usage:**
```php
$validator = Validator::make($_POST, [
    'username' => 'required|alpha_dash|min:3|max:20',
    'email' => 'required|email',
    'age' => 'required|integer|min:18|max:100',
    'website' => 'url',
    'role' => 'in:admin,user,guest',
    'password' => 'required|min:8|confirmed',
    'terms' => 'required|in:on'  // Checkbox validation
]);
```

#### Global Functions
Global helper functions available throughout the application that can be used directly without the Helper:: prefix.

```php
// Input handling
$input = input('field_name');           // Get input value from POST/GET
$input = input('field_name', 'default'); // Get input with default value
$allInput = input();                    // Get all input data

// Input sanitization
$safeInput = sanitize($_POST['input']); // Sanitize user input

// Redirect functions
redirect('/dashboard');                 // Immediate redirect
redirect('/dashboard', 2);              // Delayed redirect (after 2 seconds)

// Alert functions
echo set_alert('success', 'Operation completed'); // Show styled alert

// Text utilities
$excerpt = excerpt($longText, 100);     // Create text excerpt
$time = readingTime($content);          // Estimate reading time

// JSON response
returnJson(['status' => 'success']);     // Send JSON response and exit

// Environment and paths
$appUrl = env('APP_URL');               // Get environment variable
$assetUrl = asset('css/style.css');     // Get asset URL
$fullUrl = url('user.profile', ['id' => 123]); // Generate URL
$slug = slugify('My Page Title');       // Create URL-friendly slug

// Session management
session('user_id', 123);                 // Set session value
$userId = session('user_id');            // Get session value

// Flash messages
flash('success', 'Item created');        // Set flash message
$message = getFlash('success');          // Get and clear flash message

// File paths
$basePath = base_path();                 // Get project root path
$appPath = app_path();                   // Get App directory path
$storagePath = storage_path();           // Get Storage directory path
$dbPath = db_path();                     // Get database path

// Request information
$method = method();                      // Get request method (GET, POST, etc.)

// Old input (for forms after validation failure)
$oldValue = old('field_name');          // Get old input value

// Authentication helpers
$user = auth();                         // Get authenticated user
$loggedIn = check();                     // Check if user is logged in
logout();                               // Logout user

// CSRF protection
$token = csrf_token();                  // Generate CSRF token
echo csrf_field();                      // Output hidden CSRF token field
csrf_verify($token);                    // Verify CSRF token
csrf_enforce();                         // Enforce CSRF validation (automatically called for POST requests)

// File operations
$uploaded = upload($_FILES['file'], 'uploads/', ['jpg', 'png']); // Upload file

// Array utilities
$value = array_get($array, 'key.subkey'); // Get nested array value

// View rendering
view('pages.home', ['data' => $data]); // Render view with data

// Email service
$emailService = EmailService();         // Get email service instance

// Debugging
dd($variable);                          // Dump variable and die
```

#### Function Aliases
Some functions have multiple names for convenience:
- `csrf_field()`, `csrfField()`, `csrfInput()` - All output CSRF token field
- `csrf_token()` - Get CSRF token
- `csrf_verify()` - Verify CSRF token
```
$fileInfo = $uploader->upload($_FILES['avatar'], 'uploads/avatars/');
```

#### Validator
```php
use Framework\Helper\Validator;

$validator = new Validator();
$validator->validate([
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

#### Validation
```php
use Framework\Helper\Validator;

// Basic validation
$validator = new Validator($_POST);
$validator->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|max:50'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle validation errors
}

// Or use static method
$validator = Validator::make($_POST, [
    'name' => 'required|min:3',
    'email' => 'required|email',
    'password' => 'required|min:8'
]);

if ($validator->fails()) {
    $errors = $validator->errors();
    // Handle errors
}
```

#### Available Validation Rules

**Required Rules:**
- `required` - Field must not be empty

**String Validation:**
- `min:length` - Minimum character length (e.g., `min:8`)
- `max:length` - Maximum character length (e.g., `max:50`)
- `between:min,max` - Between minimum and maximum lengths (e.g., `between:5,20`)
- `alpha` - Contains only letters
- `alpha_num` - Contains only letters and numbers
- `alpha_dash` - Contains only letters, numbers, dashes and underscores

**Numeric Validation:**
- `numeric` - Field must be numeric
- `integer` - Field must be an integer

**Format Validation:**
- `email` - Value must be a valid email address
- `url` - Value must be a valid URL
- `ip` - Value must be a valid IP address
- `regex:pattern` - Field must match the given regular expression pattern

**List Validation:**
- `in:value1,value2,value3` - Field must be one of the specified values
- `not_in:value1,value2,value3` - Field must NOT be one of the specified values

**Confirmation Validation:**
- `confirmed` - Field must have a matching confirmation field (e.g., password and password_confirmation)

**Example Usage:**
```php
$validator = Validator::make($_POST, [
    'username' => 'required|alpha_dash|min:3|max:20',
    'email' => 'required|email',
    'age' => 'required|integer|min:18|max:100',
    'website' => 'url',
    'role' => 'in:admin,user,guest',
    'password' => 'required|min:8|confirmed',
    'terms' => 'required|in:on'  // Checkbox validation
]);
```

#### Using Validation in Controllers
```php
<?php

namespace App\Controller;

use Framework\Helper\Validator;
use Framework\Helper\Helper;

class UserController
{
    public function store()
    {
        $validator = Validator::make($_POST, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            // Return to form with errors
            return $this->renderView('users/create', [
                'errors' => $validator->errors(),
                'old_data' => $_POST
            ]);
        }

        // Process valid data
        // ... save user to database
        redirect('/users');
    }

    private function renderView($view, $data = [])
    {
        // Render view with validation errors
        extract($data);
        // ... view rendering logic
    }
}
```

#### Passing Validation Errors to Views
```php
// In controller
return view('forms.register', [
    'errors' => $validator->errors(),
    'old_data' => $_POST
]);

// In Twig template
{% if errors.email is defined %}
    <div class="error">{{ errors.email[0] }}</div>
{% endif %}
<input type="email" name="email" value="{{ old_data.email|default('') }}">
```

#### XSS Protection
```php
use Framework\Helper\XSSProtection;

$safeContent = XSSProtection::clean($userInput);
```

#### Global Functions
```php
// In App/Helper/functions.php

baseUrl();           // Get base URL
redirect('/path');   // Redirect to URL
e('string');        // Escape HTML
csrf_token();        // Get CSRF token
csrf_verify($token); // Verify CSRF token
```

## Configuration

### Environment Variables
Configure your `.env` file:

```env
# Database Configuration
DB_TYPE=sqlite     # Options: mysql, sqlite, postgresql
DB_HOST=localhost
DB_NAME=myapp
DB_USER=root
DB_PASS=
DB_PORT=3306
DB_PATH=database.sqlite  # For SQLite

# App Configuration
APP_URL=http://localhost:8000

# Mail Configuration
MAIL_DRIVER=smtp    # Options: smtp, log, mail
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_EMAIL=noreply@yourapp.com
MAIL_FROM_NAME="Your App Name"

# Social Authentication
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Best Practices

### Model-First Approach
1. Create your model with schema definition
2. Generate migration that uses the model's schema
3. Run migration to create/update database

### Use CLI Commands
- Always use `php pype.php make:model` instead of creating models manually
- Use `php pype.php make:migration` for consistent migration files
- Use `php pype.php createcontroller` for standardized controllers

### Security
- Always validate and sanitize user inputs
- Use CSRF tokens in forms
- Use XSS protection for user-generated content
- Hash passwords before storing

### Error Handling
- Use try-catch blocks for database operations
- Implement proper logging
- Show user-friendly error messages

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

MIT License - See LICENSE file for details.

## Support

For support, please open an issue in the repository or contact the maintainers.#
