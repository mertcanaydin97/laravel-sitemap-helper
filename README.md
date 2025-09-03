# Laravel Sitemap Helper

A simple Laravel helper to generate XML sitemaps for your website. Add models, submodels, static pages, and routes easily.

## Quick Install

1. Copy `SitemapHelper.php` to `app/Helpers/`
2. Add to `composer.json`:
   ```json
   "autoload": {
       "psr-4": {
           "App\\Helpers\\": "app/Helpers/"
       }
   }
   ```
3. Run: `composer dump-autoload`

## Setting Up Slug Columns

### **Step 1: Create Migration for Slug Columns**
```bash
php artisan make:migration add_slugs_to_tables
```

### **Step 2: Add Slug Columns to Your Tables**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugsToTables extends Migration
{
    public function up()
    {
        // Posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
        });
        
        // Products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });
        
        // Categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });
        
        // Tags table
        Schema::table('tags', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });
        
        // Authors table
        Schema::table('authors', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
        
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
```

### **Step 3: Run Migration**
```bash
php artisan migrate
```

### **Step 4: Fill Slug Columns with Data**
```bash
php artisan tinker
```

```php
// Fill posts table slugs
use App\Models\Post;
Post::all()->each(function ($post) {
    $post->slug = \Str::slug($post->title);
    $post->save();
});

// Fill products table slugs
use App\Models\Product;
Product::all()->each(function ($product) {
    $product->slug = \Str::slug($product->name);
    $product->save();
});

// Fill categories table slugs
use App\Models\Category;
Category::all()->each(function ($category) {
    $category->slug = \Str::slug($category->name);
    $category->save();
});
```

### **Step 5: Update Your Models**
```php
// In your Post model
class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'status'];
    
    // Auto-generate slug when creating/updating
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = \Str::slug($post->title);
            }
        });
        
        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = \Str::slug($post->title);
            }
        });
    }
}
```

## Basic Usage

### In Routes
```php
// routes/web.php
use App\Helpers\SitemapHelper;

Route::get('/sitemap.xml', function () {
    return SitemapHelper::quickGenerate([
        ['model' => 'App\Models\Post', 'route' => 'posts.show'],
        ['model' => 'App\Models\Product', 'route' => 'products.show'],
        ['model' => 'App\Models\Category', 'route' => 'categories.show'],
    ])->response();
});
```

### In Controllers
```php
use App\Helpers\SitemapHelper;

public function sitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/blog', 'priority' => 0.7, 'change_freq' => 'daily'],
        ['url' => '/shop', 'priority' => 0.7, 'change_freq' => 'daily'],
    ]);
    
    // Main models
    $helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8);
    $helper->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9);
    $helper->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6);
    
    // Submodels (related content)
    $helper->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.5);
    $helper->addModelCollection('App\Models\Author', 'authors.show', 'monthly', 0.6);
    $helper->addModelCollection('App\Models\Brand', 'brands.show', 'monthly', 0.6);
    
    // Archive pages
    $helper->addModelCollection('App\Models\Archive', 'archives.show', 'yearly', 0.3);
    
    return $helper->response();
}
```

## Using Slug Columns for SEO-Friendly URLs

### Basic Slug Usage
```php
// Use 'slug' column instead of ID for URLs
$helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [], 'slug');

// Use 'url_slug' column
$helper->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [], 'url_slug');

// Use 'seo_title' column
$helper->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'seo_title');
```

### Advanced Slug Usage with Constraints
```php
// Use slug column with additional constraints
$helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');

// Use custom slug column name
$helper->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [
    'is_active' => true
], 'product_slug');
```

## Complete Examples for Different App Types

### 🛒 E-Commerce App
```php
public function ecommerceSitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/shop', 'priority' => 0.9, 'change_freq' => 'daily'],
        ['url' => '/about', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/help', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/shipping', 'priority' => 0.5, 'change_freq' => 'monthly'],
        ['url' => '/returns', 'priority' => 0.5, 'change_freq' => 'monthly'],
        ['url' => '/privacy', 'priority' => 0.4, 'change_freq' => 'yearly'],
        ['url' => '/terms', 'priority' => 0.4, 'change_freq' => 'yearly'],
    ]);
    
    // Main products - using slug column for SEO-friendly URLs
    $helper->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [
        'is_active' => true,
        'stock' => '>', 0
    ], 'slug');
    
    // Product categories - using slug column
    $helper->addModelCollection('App\Models\Category', 'categories.show', 'weekly', 0.7, [], 'slug');
    
    // Product brands - using slug column
    $helper->addModelCollection('App\Models\Brand', 'brands.show', 'weekly', 0.7, [], 'slug');
    
    // Product tags - using slug column
    $helper->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.6, [], 'slug');
    
    // Product reviews - using slug column
    $helper->addModelCollection('App\Models\Review', 'reviews.show', 'weekly', 0.5, [], 'slug');
    
    // Product collections - using slug column
    $helper->addModelCollection('App\Models\Collection', 'collections.show', 'weekly', 0.7, [], 'slug');
    
    // Product variants - using slug column
    $helper->addModelCollection('App\Models\Variant', 'variants.show', 'daily', 0.8, [], 'slug');
    
    // Sales and deals - using slug column
    $helper->addModelCollection('App\Models\Sale', 'sales.show', 'daily', 0.8, [], 'slug');
    
    // Blog posts - using slug column
    $helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.7, [], 'slug');
    
    // Blog categories - using slug column
    $helper->addModelCollection('App\Models\BlogCategory', 'blog-categories.show', 'monthly', 0.6, [], 'slug');
    
    // Blog tags - using slug column
    $helper->addModelCollection('App\Models\BlogTag', 'blog-tags.show', 'monthly', 0.5, [], 'slug');
    
    // Blog authors - using slug column
    $helper->addModelCollection('App\Models\Author', 'authors.show', 'monthly', 0.6, [], 'slug');
    
    return $helper->response();
}
```

### 📝 Blog/News App
```php
public function blogSitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/blog', 'priority' => 0.9, 'change_freq' => 'daily'],
        ['url' => '/about', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/subscribe', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/privacy', 'priority' => 0.4, 'change_freq' => 'yearly'],
        ['url' => '/terms', 'priority' => 0.4, 'change_freq' => 'yearly'],
    ]);
    
    // Blog posts - using slug column for SEO-friendly URLs
    $helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
        'status' => 'published',
        'is_featured' => true
    ], 'slug');
    
    // Post categories - using slug column
    $helper->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.7, [], 'slug');
    
    // Post tags - using slug column
    $helper->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.6, [], 'slug');
    
    // Post authors - using slug column
    $helper->addModelCollection('App\Models\Author', 'authors.show', 'monthly', 0.6, [], 'slug');
    
    // Post series - using slug column
    $helper->addModelCollection('App\Models\Series', 'series.show', 'monthly', 0.6, [], 'slug');
    
    // Post comments - using slug column
    $helper->addModelCollection('App\Models\Comment', 'comments.show', 'weekly', 0.4, [], 'slug');
    
    // Archive pages - using slug column
    $helper->addModelCollection('App\Models\Archive', 'archives.show', 'yearly', 0.3, [], 'slug');
    
    // Search pages - using slug column
    $helper->addModelCollection('App\Models\SearchPage', 'search.show', 'daily', 0.5, [], 'slug');
    
    return $helper->response();
}
```

### 🎨 Portfolio/Creative App
```php
public function portfolioSitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/portfolio', 'priority' => 0.9, 'change_freq' => 'weekly'],
        ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/services', 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => '/pricing', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/blog', 'priority' => 0.7, 'change_freq' => 'weekly'],
        ['url' => '/testimonials', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/team', 'priority' => 0.6, 'change_freq' => 'monthly'],
    ]);
    
    // Portfolio projects - using slug column for SEO-friendly URLs
    $helper->addModelCollection('App\Models\Project', 'projects.show', 'weekly', 0.8, [
        'is_published' => true
    ], 'slug');
    
    // Project categories - using slug column
    $helper->addModelCollection('App\Models\ProjectCategory', 'project-categories.show', 'monthly', 0.7, [], 'slug');
    
    // Project tags - using slug column
    $helper->addModelCollection('App\Models\ProjectTag', 'project-tags.show', 'monthly', 0.6, [], 'slug');
    
    // Project skills - using slug column
    $helper->addModelCollection('App\Models\Skill', 'skills.show', 'monthly', 0.6, [], 'slug');
    
    // Project clients - using slug column
    $helper->addModelCollection('App\Models\Client', 'clients.show', 'monthly', 0.6, [], 'slug');
    
    // Blog posts - using slug column
    $helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.7, [], 'slug');
    
    // Blog categories - using slug column
    $helper->addModelCollection('App\Models\BlogCategory', 'blog-categories.show', 'monthly', 0.6, [], 'slug');
    
    // Services - using slug column
    $helper->addModelCollection('App\Models\Service', 'services.show', 'monthly', 0.7, [], 'slug');
    
    // Testimonials - using slug column
    $helper->addModelCollection('App\Models\Testimonial', 'testimonials.show', 'monthly', 0.5, [], 'slug');
    
    // Team members - using slug column
    $helper->addModelCollection('App\Models\TeamMember', 'team.show', 'monthly', 0.6, [], 'slug');
    
    return $helper->response();
}
```

### 🏢 Business/Corporate App
```php
public function businessSitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => '/company', 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => '/leadership', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/team', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/careers', 'priority' => 0.7, 'change_freq' => 'weekly'],
        ['url' => '/news', 'priority' => 0.7, 'change_freq' => 'daily'],
        ['url' => '/press', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/investors', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/locations', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/privacy', 'priority' => 0.4, 'change_freq' => 'yearly'],
        ['url' => '/terms', 'priority' => 0.4, 'change_freq' => 'yearly'],
    ]);
    
    // Company departments - using slug column for SEO-friendly URLs
    $helper->addModelCollection('App\Models\Department', 'departments.show', 'monthly', 0.6, [], 'slug');
    
    // Team members - using slug column
    $helper->addModelCollection('App\Models\TeamMember', 'team.show', 'monthly', 0.6, [], 'slug');
    
    // Job openings - using slug column
    $helper->addModelCollection('App\Models\Job', 'jobs.show', 'daily', 0.7, [
        'is_active' => true
    ], 'slug');
    
    // Job categories - using slug column
    $helper->addModelCollection('App\Models\JobCategory', 'job-categories.show', 'monthly', 0.6, [], 'slug');
    
    // Job locations - using slug column
    $helper->addModelCollection('App\Models\JobLocation', 'job-locations.show', 'monthly', 0.6, [], 'slug');
    
    // News articles - using slug column
    $helper->addModelCollection('App\Models\News', 'news.show', 'weekly', 0.7, [], 'slug');
    
    // News categories - using slug column
    $helper->addModelCollection('App\Models\NewsCategory', 'news-categories.show', 'monthly', 0.6, [], 'slug');
    
    // Press releases - using slug column
    $helper->addModelCollection('App\Models\PressRelease', 'press.show', 'weekly', 0.6, [], 'slug');
    
    // Office locations - using slug column
    $helper->addModelCollection('App\Models\Office', 'offices.show', 'monthly', 0.6, [], 'slug');
    
    // Partners - using slug column
    $helper->addModelCollection('App\Models\Partner', 'partners.show', 'monthly', 0.5, [], 'slug');
    
    // Case studies - using slug column
    $helper->addModelCollection('App\Models\CaseStudy', 'case-studies.show', 'monthly', 0.7, [], 'slug');
    
    // White papers - using slug column
    $helper->addModelCollection('App\Models\WhitePaper', 'white-papers.show', 'monthly', 0.6, [], 'slug');
    
    return $helper->response();
}
```

### 🎓 Educational/Learning App
```php
public function educationalSitemap()
{
    $helper = new SitemapHelper();
    
    // Static pages
    $helper->addStaticPages([
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => '/courses', 'priority' => 0.9, 'change_freq' => 'daily'],
        ['url' => '/about', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/pricing', 'priority' => 0.7, 'change_freq' => 'monthly'],
        ['url' => '/help', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/faq', 'priority' => 0.6, 'change_freq' => 'monthly'],
        ['url' => '/privacy', 'priority' => 0.4, 'change_freq' => 'yearly'],
        ['url' => '/terms', 'priority' => 0.4, 'change_freq' => 'yearly'],
    ]);
    
    // Courses - using slug column for SEO-friendly URLs
    $helper->addModelCollection('App\Models\Course', 'courses.show', 'weekly', 0.8, [
        'is_published' => true
    ], 'slug');
    
    // Course categories - using slug column
    $helper->addModelCollection('App\Models\CourseCategory', 'course-categories.show', 'monthly', 0.7, [], 'slug');
    
    // Course tags - using slug column
    $helper->addModelCollection('App\Models\CourseTag', 'course-tags.show', 'monthly', 0.6, [], 'slug');
    
    // Course instructors - using slug column
    $helper->addModelCollection('App\Models\Instructor', 'instructors.show', 'monthly', 0.7, [], 'slug');
    
    // Course lessons - using slug column
    $helper->addModelCollection('App\Models\Lesson', 'lessons.show', 'weekly', 0.7, [], 'slug');
    
    // Course modules - using slug column
    $helper->addModelCollection('App\Models\Module', 'modules.show', 'weekly', 0.7, [], 'slug');
    
    // Course reviews - using slug column
    $helper->addModelCollection('App\Models\CourseReview', 'course-reviews.show', 'weekly', 0.5, [], 'slug');
    
    // Students - using slug column
    $helper->addModelCollection('App\Models\Student', 'students.show', 'monthly', 0.5, [], 'slug');
    
    // Certificates - using slug column
    $helper->addModelCollection('App\Models\Certificate', 'certificates.show', 'monthly', 0.6, [], 'slug');
    
    // Blog posts - using slug column
    $helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.7, [], 'slug');
    
    // Blog categories - using slug column
    $helper->addModelCollection('App\Models\BlogCategory', 'blog-categories.show', 'monthly', 0.6, [], 'slug');
    
    return $helper->response();
}
```

## One-Liner Usage with Slug Columns

```php
// Generate sitemap with everything using slug columns
Route::get('/sitemap.xml', function () {
    return SitemapHelper::quickGenerate([
        // Main models with slug columns
        ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Category', 'route' => 'categories.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
        
        // Submodels with slug columns
        ['model' => 'App\Models\Tag', 'route' => 'tags.show', 'change_freq' => 'monthly', 'priority' => 0.5, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Author', 'route' => 'authors.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Brand', 'route' => 'brands.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Review', 'route' => 'reviews.show', 'change_freq' => 'weekly', 'priority' => 0.5, 'slug_column' => 'slug'],
        
        // Archive with slug column
        ['model' => 'App\Models\Archive', 'route' => 'archives.show', 'change_freq' => 'yearly', 'priority' => 0.3, 'slug_column' => 'slug'],
    ])->response();
});
```

## Available Methods

### Add Content
- `addUrl('/page')` - Single URL
- `addStaticPages([...])` - Multiple static pages
- `addModelCollection('Model', 'route', 'change_freq', 'priority', 'constraints', 'slug_column')` - Model with route and slug column
- `addRoutes()` - All public routes

### Generate
- `->response()` - Return as HTTP response
- `->save()` - Save to file
- `->generate()` - Get as string

## Slug Column Examples

### Common Slug Column Names
```php
// Standard slug column
$helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [], 'slug');

// Custom slug column names
$helper->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [], 'url_slug');
$helper->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'seo_title');
$helper->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.5, [], 'tag_slug');
$helper->addModelCollection('App\Models\Author', 'authors.show', 'monthly', 0.6, [], 'author_slug');

// Using with constraints
$helper->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');
```

## Priority Guide

- `1.0` - Homepage
- `0.9` - Products, main content
- `0.8` - Blog posts, important pages
- `0.7` - Categories, sections
- `0.6` - Regular pages
- `0.5` - Tags, authors, reviews
- `0.4` - Legal pages
- `0.3` - Archives, old content

## Change Frequency

- `daily` - Homepage, products, news
- `weekly` - Blog posts, updates
- `monthly` - Categories, static pages
- `yearly` - Legal pages, archives

## That's It!

Just copy the examples above and modify for your models. The helper automatically:
- Excludes admin/API routes
- Generates proper XML
- Handles model relationships
- Follows sitemap protocol
- **Supports slug columns for SEO-friendly URLs**

**Need more?** Check the `SitemapHelper.php` file for all available methods.
