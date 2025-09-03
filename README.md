# Laravel Sitemap Helper - Standalone Version

A simple, powerful Laravel sitemap helper that generates XML sitemaps with SEO-friendly URLs, model collections, and static pages. **No composer autoload required!**

## 🚀 Features

- ✅ **Standalone** - No composer autoload needed
- ✅ **SEO-friendly URLs** - Support for slug columns
- ✅ **Model collections** - Add models with constraints
- ✅ **Static pages** - Easy static page management
- ✅ **Route generation** - Automatic public route inclusion
- ✅ **XML generation** - Proper sitemap protocol
- ✅ **File saving** - Save to any location
- ✅ **Helper functions** - Simple one-liner usage
- ✅ **Comprehensive testing** - Built-in test runner

## 📦 Installation

### **Option 1: Direct Download (Recommended)**
1. Download `SitemapHelper.php`
2. Include it in your project: `require_once 'SitemapHelper.php';`
3. **That's it!** No composer, no autoload, no setup!

### **Option 2: Copy & Paste**
1. Copy the `SitemapHelper` class into your project
2. Use it immediately

## 🎯 Quick Start

### **Basic Usage**
```php
<?php
require_once 'SitemapHelper.php';

// Create sitemap
$sitemap = new SitemapHelper();

// Add static pages
$sitemap->addStaticPages([
    ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
]);

// Generate XML
echo $sitemap->generate();
```

### **One-Liner Usage**
```php
<?php
require_once 'SitemapHelper.php';

// Generate sitemap in one line
$sitemap = SitemapHelper::quickGenerate([], [
    ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
]);

// Save to file
$sitemap->save('public/sitemap.xml');
```

### **Helper Functions**
```php
<?php
require_once 'SitemapHelper.php';

// Using helper functions
$sitemap = sitemap(); // Creates new instance
$sitemap->addUrl('/blog', '2024-01-01', 'weekly', 0.7);

// Quick generate with helper
$sitemap = sitemap_quick([], [['url' => '/', 'priority' => 1.0]]);
```

## 📋 Methods Reference

### **Core Methods**
- `addUrl($url, $lastmod, $changeFreq, $priority)` - Add single URL
- `addStaticPages($pages)` - Add multiple static pages
- `addModelCollection($modelClass, $routeName, $changeFreq, $priority, $constraints, $slugColumn)` - Add model collection
- `addModelCollections($collections)` - Add multiple model collections
- `addRoutes()` - Add all public routes

### **Configuration Methods**
- `setDefaultPriority($priority)` - Set default priority (0.0 - 1.0)
- `setDefaultChangeFreq($changeFreq)` - Set default change frequency
- `setExcludedRoutes($patterns)` - Set excluded route patterns

### **Output Methods**
- `generate()` - Generate XML string
- `generateIndex($sitemaps)` - Generate sitemap index
- `response()` - Return as HTTP response
- `save($filePath)` - Save to file
- `getUrls()` - Get all URLs array

### **Utility Methods**
- `clear()` - Clear all URLs
- `count()` - Get URL count
- `isEmpty()` - Check if sitemap is empty

### **Static Methods**
- `createDefault()` - Create default instance
- `quickGenerate($models, $staticPages)` - Quick generation

## 🌟 Usage Examples

### **E-Commerce Sitemap**
```php
<?php
require_once 'SitemapHelper.php';

$sitemap = SitemapHelper::quickGenerate([
    // Products with slug columns
    ['model' => 'App\Models\Product', 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
    ['model' => 'App\Models\Category', 'route' => 'categories.show', 'change_freq' => 'weekly', 'priority' => 0.7, 'slug_column' => 'slug'],
    ['model' => 'App\Models\Brand', 'route' => 'brands.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
], [
    // Static pages
    ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => '/shop', 'priority' => 0.9, 'change_freq' => 'daily'],
    ['url' => '/about', 'priority' => 0.7, 'change_freq' => 'monthly'],
    ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
]);

// Save to public directory
$sitemap->save('public/sitemap.xml');
```

### **Blog Sitemap**
```php
<?php
require_once 'SitemapHelper.php';

$sitemap = new SitemapHelper();

// Static pages
$sitemap->addStaticPages([
    ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => '/blog', 'priority' => 0.9, 'change_freq' => 'daily'],
    ['url' => '/about', 'priority' => 0.7, 'change_freq' => 'monthly'],
]);

// Blog posts with slug columns
$sitemap->addModelCollection('App\Models\Post', 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');

// Categories and tags
$sitemap->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'slug');
$sitemap->addModelCollection('App\Models\Tag', 'tags.show', 'monthly', 0.5, [], 'slug');

// Generate and save
$sitemap->save('public/sitemap.xml');
```

### **Business Website Sitemap**
```php
<?php
require_once 'SitemapHelper.php';

$sitemap = new SitemapHelper();

// Set custom defaults
$sitemap->setDefaultPriority(0.7);
$sitemap->setDefaultChangeFreq('monthly');

// Add static pages
$sitemap->addStaticPages([
    ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => '/services', 'priority' => 0.8, 'change_freq' => 'monthly'],
    ['url' => '/team', 'priority' => 0.6, 'change_freq' => 'monthly'],
    ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
    ['url' => '/privacy', 'priority' => 0.4, 'change_freq' => 'yearly'],
    ['url' => '/terms', 'priority' => 0.4, 'change_freq' => 'yearly'],
]);

// Add public routes (excluding admin/API)
$sitemap->addRoutes();

// Save to file
$sitemap->save('public/sitemap.xml');
```

## 🔧 Advanced Features

### **Model Constraints**
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
```

### **Custom Slug Columns**
```php
// Use 'url_slug' column instead of 'slug'
$sitemap->addModelCollection('App\Models\Product', 'products.show', 'daily', 0.9, [], 'url_slug');

// Use 'seo_title' column
$sitemap->addModelCollection('App\Models\Category', 'categories.show', 'monthly', 0.6, [], 'seo_title');
```

### **Excluded Routes**
```php
$sitemap->setExcludedRoutes([
    'admin/*',
    'api/*',
    'temp/*',
    'draft/*'
]);
```

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

## 📁 File Structure

```
your-project/
├── SitemapHelper.php          # Main helper class
├── usage-example.php          # Usage examples
├── simple-test.php            # Test runner
├── README.md                  # This file
└── public/
    └── sitemap.xml           # Generated sitemap
```

## 🚀 Production Usage

### **In Laravel Routes**
```php
// routes/web.php
Route::get('/sitemap.xml', function () {
    require_once base_path('SitemapHelper.php');
    
    return SitemapHelper::quickGenerate([
        ['model' => 'App\Models\Post', 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
    ], [
        ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
    ])->response();
});
```

### **In Laravel Controllers**
```php
// app/Http/Controllers/SitemapController.php
public function generate()
{
    require_once base_path('SitemapHelper.php');
    
    $sitemap = new SitemapHelper();
    // ... add your URLs ...
    
    return $sitemap->response();
}
```

### **Scheduled Generation**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        require_once base_path('SitemapHelper.php');
        
        $sitemap = SitemapHelper::quickGenerate([
            // Your models...
        ], [
            // Your static pages...
        ]);
        
        $sitemap->save(public_path('sitemap.xml'));
    })->daily();
}
```

## 🌟 Why Standalone?

- **No setup required** - Just include the file
- **No dependencies** - Works with basic PHP
- **No composer issues** - No autoload conflicts
- **Easy distribution** - Single file solution
- **Immediate usage** - Start generating sitemaps right away
- **Production ready** - Comprehensive testing included

## 📝 Requirements

- **PHP 7.4+** (for type hints)
- **Laravel** (for model usage)
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
- **Questions**: Check the usage examples
- **Testing**: Run `php simple-test.php`

## 🎉 That's It!

Your Laravel Sitemap Helper is ready to use! Just:

1. **Download** `SitemapHelper.php`
2. **Include** it in your project
3. **Start generating** sitemaps immediately

**No composer, no autoload, no setup - just pure sitemap generation power!** 🚀
