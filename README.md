# Laravel Sitemap Helper

A powerful Laravel helper to generate XML sitemaps with SEO-friendly URLs, model collections, and static pages. **Built specifically for Laravel, no composer autoload required!**

## 🚀 Features

- ✅ **Laravel Native** - Built specifically for Laravel projects
- ✅ **Standalone** - No composer autoload needed
- ✅ **SEO-friendly URLs** - Support for slug columns
- ✅ **Model collections** - Add Laravel models with constraints
- ✅ **Static pages** - Easy static page management
- ✅ **Route generation** - Automatic public route inclusion
- ✅ **XML generation** - Proper sitemap protocol
- ✅ **File saving** - Save to Laravel public directory
- ✅ **Helper functions** - Simple one-liner usage
- ✅ **Comprehensive testing** - Built-in test runner

## 📦 Installation

### **Laravel Integration (Recommended)**
1. Download `SitemapHelper.php` to your Laravel project
2. Place in `app/Helpers/` directory
3. Include it where needed: `require_once base_path('app/Helpers/SitemapHelper.php');`
4. **That's it!** No composer, no autoload, no setup!

## 🎯 Quick Start

### **Laravel Route Usage**
```php
<?php
// routes/web.php
Route::get('/sitemap.xml', function () {
    require_once base_path('app/Helpers/SitemapHelper.php');
    
    return SitemapHelper::quickGenerate([
        ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
        ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
    ], [
        ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
    ])->response();
});
```

### **Laravel Controller Usage**
```php
<?php
// app/Http/Controllers/SitemapController.php
namespace App\Http\Controllers;

class SitemapController extends Controller
{
    public function generate()
    {
        require_once base_path('app/Helpers/SitemapHelper.php');
        
        $sitemap = new \SitemapHelper();
        
        // Add static pages using Laravel routes
        $sitemap->addStaticPages([
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
            ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
        ]);
        
        // Add Laravel models with slug columns
        $sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
            'status' => 'published'
        ], 'slug');
        
        return $sitemap->response();
    }
}
```

### **Laravel Artisan Command**
```php
<?php
// app/Console/Commands/GenerateSitemap.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap';

    public function handle()
    {
        require_once base_path('app/Helpers/SitemapHelper.php');
        
        $sitemap = \SitemapHelper::quickGenerate([
            ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
        ], [
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
        ]);
        
        $sitemap->save(public_path('sitemap.xml'));
        
        $this->info('Sitemap generated successfully!');
    }
}
```

### **Laravel Scheduled Generation**
```php
<?php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        require_once base_path('app/Helpers/SitemapHelper.php');
        
        $sitemap = \SitemapHelper::quickGenerate([
            ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
            ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
        ], [
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
        ]);
        
        $sitemap->save(public_path('sitemap.xml'));
    })->daily();
}
```

## 🌟 Laravel Sitemap Examples

### **E-Commerce Laravel App**
```php
<?php
// In your Laravel controller or route
require_once base_path('app/Helpers/SitemapHelper.php');

$sitemap = \SitemapHelper::quickGenerate([
    // Laravel models with slug columns
    ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
    ['model' => 'App\Models\Category', 'route' => 'categories.show', 'change_freq' => 'weekly', 'priority' => 0.7, 'slug_column' => 'slug'],
    ['model' => 'App\Models\Brand', 'route' => 'brands.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
    ['model' => 'App\Models\Tag', 'route' => 'tags.show', 'change_freq' => 'monthly', 'priority' => 0.5, 'slug_column' => 'slug'],
], [
    // Laravel routes
    ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => route('shop'), 'priority' => 0.9, 'change_freq' => 'daily'],
    ['url' => route('about'), 'priority' => 0.7, 'change_freq' => 'monthly'],
    ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
]);

// Save to Laravel public directory
$sitemap->save(public_path('sitemap.xml'));
```

### **Blog Laravel App**
```php
<?php
// In your Laravel blog controller
require_once base_path('app/Helpers/SitemapHelper.php');

$sitemap = new \SitemapHelper();

// Static pages using Laravel routes
$sitemap->addStaticPages([
    ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => route('blog.index'), 'priority' => 0.9, 'change_freq' => 'daily'],
    ['url' => route('about'), 'priority' => 0.7, 'change_freq' => 'monthly'],
    ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
]);

// Laravel models with slug columns
$sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');

// Categories and tags
$sitemap->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'slug');
$sitemap->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.5, [], 'slug');

// Generate and save to Laravel public directory
$sitemap->save(public_path('sitemap.xml'));
```

### **Business Laravel App**
```php
<?php
// In your Laravel business controller
require_once base_path('app/Helpers/SitemapHelper.php');

$sitemap = new \SitemapHelper();

// Set custom defaults
$sitemap->setDefaultPriority(0.7);
$sitemap->setDefaultChangeFreq('monthly');

// Add static pages using Laravel routes
$sitemap->addStaticPages([
    ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => route('services'), 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => route('team'), 'priority' => 0.6, 'change_freq' => 'monthly'],
    ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
    ['url' => route('privacy'), 'priority' => 0.4, 'change_freq' => 'yearly'],
    ['url' => route('terms'), 'priority' => 0.4, 'change_freq' => 'yearly'],
]);

// Add public routes (excluding admin/API)
$sitemap->addRoutes();

// Save to Laravel public directory
$sitemap->save(public_path('sitemap.xml'));
```

### **Portfolio Laravel App**
```php
<?php
// In your Laravel portfolio controller
require_once base_path('app/Helpers/SitemapHelper.php');

$sitemap = new \SitemapHelper();

// Static pages using Laravel routes
$sitemap->addStaticPages([
    ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => route('portfolio'), 'priority' => 0.9, 'change_freq' => 'weekly'],
    ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => route('services'), 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => route('contact'), 'priority' => 0.7, 'change_freq' => 'monthly'],
]);

// Portfolio projects with Laravel models
$sitemap->addModelCollection('App\Models\Project', 'projects.show', 'weekly', 0.8, [
    'is_published' => true
], 'slug');

// Project categories
$sitemap->addModelCollection('App\Models\ProjectCategory', 'project-categories.show', 'monthly', 0.7, [], 'slug');

// Save to Laravel public directory
$sitemap->save(public_path('sitemap.xml'));
```

## 🔧 Advanced Laravel Features

### **Model Constraints with Laravel Query Builder**
```php
// Only published posts
$sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');

// Posts with high views
$sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'views' => ['>', 1000]
], 'slug');

// Featured posts only
$sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.9, [
    'status' => 'published',
    'is_featured' => true
], 'slug');

// Active products with stock
$sitemap->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [
    'is_active' => true,
    'stock' => ['>', 0]
], 'slug');
```

### **Custom Slug Columns for Laravel Models**
```php
// Use 'url_slug' column instead of 'slug'
$sitemap->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [], 'url_slug');

// Use 'seo_title' column
$sitemap->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'seo_title');

// Use 'custom_slug' column
$sitemap->addModelCollection('App\Models\Brand', 'brands.show', 'monthly', 0.6, [], 'custom_slug');
```

### **Laravel Route Exclusion**
```php
$sitemap->setExcludedRoutes([
    'admin/*',
    'api/*',
    'temp/*',
    'draft/*',
    'auth/*',
    'password/*'
]);
```

## 📋 Methods Reference

### **Core Methods**
- `addUrl($url, $lastmod, $changeFreq, $priority)` - Add single URL
- `addStaticPages($pages)` - Add multiple static pages
- `addModelCollection($modelClass, $routeName, $changeFreq, $priority, $constraints, $slugColumn)` - Add Laravel model collection
- `addModelCollections($collections)` - Add multiple Laravel model collections
- `addRoutes()` - Add all public Laravel routes

### **Configuration Methods**
- `setDefaultPriority($priority)` - Set default priority (0.0 - 1.0)
- `setDefaultChangeFreq($changeFreq)` - Set default change frequency
- `setExcludedRoutes($patterns)` - Set excluded Laravel route patterns

### **Output Methods**
- `generate()` - Generate XML string
- `generateIndex($sitemaps)` - Generate sitemap index
- `response()` - Return as Laravel HTTP response
- `save($filePath)` - Save to Laravel file system
- `getUrls()` - Get all URLs array

### **Utility Methods**
- `clear()` - Clear all URLs
- `count()` - Get URL count
- `isEmpty()` - Check if sitemap is empty

### **Static Methods**
- `createDefault()` - Create default instance
- `quickGenerate($models, $staticPages)` - Quick generation for Laravel

## 📊 Priority Guide

- `1.0` - Homepage
- `0.9` - Products, main content
- `0.8` - Blog posts, important pages
- `0.7` - Categories, sections
- `0.6` - Regular pages
- `0.5` - Tags, authors, reviews
- `0.4` - Legal pages
- `0.3` - Archives, old content

## 📅 Change Frequency

- `daily` - Homepage, products, news
- `weekly` - Blog posts, updates
- `monthly` - Categories, static pages
- `yearly` - Legal pages, archives

## 🧪 Testing

### **Run Built-in Tests**
```bash
php simple-test.php
```

**No PHPUnit required!** The built-in test runner covers all functionality.

### **Test Results**
- ✅ **14 comprehensive tests**
- ✅ **All core functionality covered**
- ✅ **Clear pass/fail results**
- ✅ **Production-ready validation**

## 📁 Laravel File Structure

```
your-laravel-project/
├── app/
│   ├── Helpers/
│   │   └── SitemapHelper.php    # Main helper class
│   ├── Http/
│   │   └── Controllers/
│   │       └── SitemapController.php
│   └── Console/
│       └── Commands/
│           └── GenerateSitemap.php
├── routes/
│   └── web.php                  # Sitemap route
├── usage-example.php            # Usage examples
├── simple-test.php              # Test runner
├── README.md                    # This file
└── public/
    └── sitemap.xml             # Generated sitemap
```

## 🚀 Production Laravel Usage

### **Environment-Specific Sitemaps**
```php
<?php
// In your Laravel service provider or controller
if (app()->environment('production')) {
    require_once base_path('app/Helpers/SitemapHelper.php');
    
    $sitemap = \SitemapHelper::quickGenerate([
        // Production Laravel models...
    ], [
        // Production Laravel routes...
    ]);
    
    $sitemap->save(public_path('sitemap.xml'));
}
```

### **Multiple Sitemaps for Large Laravel Sites**
```php
<?php
// Generate separate sitemaps for different sections
$postsSitemap = \SitemapHelper::quickGenerate([
    ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
], []);
$postsSitemap->save(public_path('sitemap-posts.xml'));

$productsSitemap = \SitemapHelper::quickGenerate([
    ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
], []);
$productsSitemap->save(public_path('sitemap-products.xml'));

// Generate sitemap index using Laravel URL helper
$indexSitemap = new \SitemapHelper();
$index = $indexSitemap->generateIndex([
    url('sitemap-posts.xml'),
    url('sitemap-products.xml'),
]);
file_put_contents(public_path('sitemap-index.xml'), $index);
```

### **Laravel Cache Integration**
```php
<?php
// Cache sitemap generation for performance
$sitemap = Cache::remember('sitemap', 3600, function () {
    require_once base_path('app/Helpers/SitemapHelper.php');
    
    return \SitemapHelper::quickGenerate([
        // Your Laravel models...
    ], [
        // Your Laravel routes...
    ])->generate();
});

return response($sitemap, 200, ['Content-Type' => 'application/xml']);
```

### **Laravel Queue Integration**
```php
<?php
// Queue sitemap generation for large sites
class GenerateSitemapJob implements ShouldQueue
{
    public function handle()
    {
        require_once base_path('app/Helpers/SitemapHelper.php');
        
        $sitemap = \SitemapHelper::quickGenerate([
            // Your Laravel models...
        ], [
            // Your Laravel routes...
        ]);
        
        $sitemap->save(public_path('sitemap.xml'));
    }
}

// Dispatch the job
GenerateSitemapJob::dispatch();
```

## 🌟 Why Laravel-First + Standalone?

- **Laravel native** - Built specifically for Laravel projects
- **No setup required** - Just include the file
- **No dependencies** - Works with basic PHP
- **No composer issues** - No autoload conflicts
- **Easy distribution** - Single file solution
- **Immediate usage** - Start generating sitemaps right away
- **Production ready** - Comprehensive testing included
- **Laravel integration** - Works seamlessly with routes, controllers, commands
- **Laravel helpers** - Uses `route()`, `url()`, `public_path()` helpers

## 📝 Requirements

- **PHP 7.4+** (for type hints)
- **Laravel** (required for full functionality)
- **No external packages** required

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

- **Issues**: Create an issue on GitHub
- **Questions**: Check the Laravel usage examples
- **Testing**: Run `php simple-test.php`

## 🎉 That's It!

Your Laravel Sitemap Helper is ready to use! Just:

1. **Download** `SitemapHelper.php`
2. **Include** it in your Laravel project
3. **Start generating** Laravel sitemaps immediately

**Built specifically for Laravel - no composer, no autoload, no setup!** 🚀
