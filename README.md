# Laravel Sitemap Helper

A powerful Laravel package to generate XML sitemaps with SEO-friendly URLs, model collections, and static pages. Built following Laravel conventions and best practices.

## 🚀 Features

- ✅ **Laravel Native** - Follows Laravel conventions and patterns
- ✅ **Eloquent Integration** - Works seamlessly with Laravel models
- ✅ **Route Integration** - Uses Laravel named routes and route helpers
- ✅ **SEO-friendly URLs** - Support for slug columns in Eloquent models
- ✅ **Model Collections** - Add Eloquent models with query constraints
- ✅ **Static Pages** - Easy static page management using Laravel routes
- ✅ **Automatic Route Discovery** - Include public routes automatically
- ✅ **XML Generation** - Proper sitemap protocol compliance
- ✅ **File System Integration** - Save to Laravel storage and public directories
- ✅ **Artisan Commands** - Generate sitemaps via command line
- ✅ **Scheduled Tasks** - Automatic sitemap generation with Laravel scheduler
- ✅ **Testing Support** - Comprehensive testing with Laravel testing framework

## 📦 Installation

### **Laravel Package Installation**
1. Download `SitemapHelper.php` to your Laravel project
2. Place in `app/Helpers/` directory following Laravel structure
3. Add to `composer.json` autoload section:
   ```json
   "autoload": {
       "psr-4": {
           "App\\": "app/"
       }
   }
   ```
4. Run `composer dump-autoload`
5. **Ready to use!** Use proper Laravel `use` statements

## 🎯 Quick Start

### **Laravel Route Definition**
```php
<?php
// routes/web.php
use App\Helpers\SitemapHelper;

Route::get('/sitemap.xml', function () {
    return SitemapHelper::quickGenerate([
        ['model' => App\Models\Post::class, 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
        ['model' => App\Models\Product::class, 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
    ], [
        ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
        ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
        ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
    ])->response();
})->name('sitemap.xml');
```

### **Laravel Controller Implementation**
```php
<?php
// app/Http/Controllers/SitemapController.php
namespace App\Http\Controllers;

use App\Helpers\SitemapHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function generate(): Response
    {
        $sitemap = new SitemapHelper();
        
        // Add static pages using Laravel named routes
        $sitemap->addStaticPages([
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
            ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
        ]);
        
        // Add Eloquent models with slug columns
        $sitemap->addModelCollection(App\Models\Post::class, 'posts.show', 'weekly', 0.8, [
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

use App\Helpers\SitemapHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--path=public/sitemap.xml}';
    protected $description = 'Generate XML sitemap for Laravel application';

    public function handle(): int
    {
        $sitemap = SitemapHelper::quickGenerate([
            ['model' => App\Models\Post::class, 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
        ], [
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
        ]);
        
        $path = $this->option('path');
        $sitemap->save(public_path($path));
        
        $this->info("Sitemap generated successfully at: {$path}");
        
        return Command::SUCCESS;
    }
}
```

### **Laravel Scheduled Task**
```php
<?php
// app/Console/Kernel.php
namespace App\Console;

use App\Helpers\SitemapHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $sitemap = SitemapHelper::quickGenerate([
                ['model' => App\Models\Post::class, 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
                ['model' => App\Models\Product::class, 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
            ], [
                ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
                ['url' => route('about'), 'priority' => 0.8, 'change_freq' => 'monthly'],
            ]);
            
            $sitemap->save(public_path('sitemap.xml'));
        })->daily()->at('02:00');
    }
}
```

## 🌟 Laravel Implementation Examples

### **E-Commerce Laravel Application**
```php
<?php
// app/Http/Controllers/SitemapController.php
namespace App\Http\Controllers;

use App\Helpers\SitemapHelper;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = SitemapHelper::quickGenerate([
            // Eloquent models with slug columns
            ['model' => Product::class, 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
            ['model' => Category::class, 'route' => 'categories.show', 'change_freq' => 'weekly', 'priority' => 0.7, 'slug_column' => 'slug'],
            ['model' => Brand::class, 'route' => 'brands.show', 'change_freq' => 'monthly', 'priority' => 0.6, 'slug_column' => 'slug'],
            ['model' => Tag::class, 'route' => 'tags.show', 'change_freq' => 'monthly', 'priority' => 0.5, 'slug_column' => 'slug'],
        ], [
            // Laravel named routes
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => route('shop'), 'priority' => 0.9, 'change_freq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.7, 'change_freq' => 'monthly'],
            ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
        ]);
        
        // Save to Laravel public directory
        $sitemap->save(public_path('sitemap.xml'));
        
        return $sitemap->response();
    }
}
```

### **Blog Laravel Application**
```php
<?php
// app/Http/Controllers/SitemapController.php
namespace App\Http\Controllers;

use App\Helpers\SitemapHelper;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = new SitemapHelper();
        
        // Static pages using Laravel named routes
        $sitemap->addStaticPages([
            ['url' => route('home'), 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => route('blog.index'), 'priority' => 0.9, 'change_freq' => 'daily'],
            ['url' => route('about'), 'priority' => 0.7, 'change_freq' => 'monthly'],
            ['url' => route('contact'), 'priority' => 0.6, 'change_freq' => 'monthly'],
        ]);
        
        // Eloquent models with slug columns
        $sitemap->addModelCollection(Post::class, 'posts.show', 'weekly', 0.8, [
            'status' => 'published'
        ], 'slug');
        
        // Categories and tags
        $sitemap->addModelCollection(Category::class, 'categories.show', 'monthly', 0.6, [], 'slug');
        $sitemap->addModelCollection(Tag::class, 'tags.show', 'monthly', 0.5, [], 'slug');
        
        // Generate and save to Laravel public directory
        $sitemap->save(public_path('sitemap.xml'));
        
        return $sitemap->response();
    }
}
```

### **Business Laravel Application**
```php
<?php
// app/Http/Controllers/SitemapController.php
namespace App\Http\Controllers;

use App\Helpers\SitemapHelper;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = new SitemapHelper();
        
        // Set custom defaults
        $sitemap->setDefaultPriority(0.7);
        $sitemap->setDefaultChangeFreq('monthly');
        
        // Add static pages using Laravel named routes
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
        
        return $sitemap->response();
    }
}
```

## 🔧 Advanced Laravel Features

### **Eloquent Query Constraints**
```php
// Only published posts
$sitemap->addModelCollection(Post::class, 'posts.show', 'weekly', 0.8, [
    'status' => 'published'
], 'slug');

// Posts with high views
$sitemap->addModelCollection(Post::class, 'posts.show', 'weekly', 0.8, [
    'views' => ['>', 1000]
], 'slug');

// Featured posts only
$sitemap->addModelCollection(Post::class, 'posts.show', 'weekly', 0.9, [
    'status' => 'published',
    'is_featured' => true
], 'slug');

// Active products with stock
$sitemap->addModelCollection(Product::class, 'products.show', 'daily', 0.9, [
    'is_active' => true,
    'stock' => ['>', 0]
], 'slug');
```

### **Custom Slug Columns for Eloquent Models**
```php
// Use 'url_slug' column instead of 'slug'
$sitemap->addModelCollection(Product::class, 'products.show', 'daily', 0.9, [], 'url_slug');

// Use 'seo_title' column
$sitemap->addModelCollection(Category::class, 'categories.show', 'monthly', 0.6, [], 'seo_title');

// Use 'custom_slug' column
$sitemap->addModelCollection(Brand::class, 'brands.show', 'monthly', 0.6, [], 'custom_slug');
```

### **Laravel Route Exclusion Patterns**
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

## 📋 API Reference

### **Core Methods**
- `addUrl($url, $lastmod, $changeFreq, $priority)` - Add single URL
- `addStaticPages($pages)` - Add multiple static pages
- `addModelCollection($modelClass, $routeName, $changeFreq, $priority, $constraints, $slugColumn)` - Add Eloquent model collection
- `addModelCollections($collections)` - Add multiple Eloquent model collections
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

## 📊 Priority Guidelines

- `1.0` - Homepage
- `0.9` - Products, main content
- `0.8` - Blog posts, important pages
- `0.7` - Categories, sections
- `0.6` - Regular pages
- `0.5` - Tags, authors, reviews
- `0.4` - Legal pages
- `0.3` - Archives, old content

## 📅 Change Frequency Guidelines

- `daily` - Homepage, products, news
- `weekly` - Blog posts, updates
- `monthly` - Categories, static pages
- `yearly` - Legal pages, archives

## 🧪 Testing

### **Laravel Testing Integration**
```bash
php simple-test.php
```

**No PHPUnit required!** Built-in test runner covers all functionality.

### **Test Coverage**
- ✅ **14 comprehensive tests**
- ✅ **All core functionality covered**
- ✅ **Clear pass/fail results**
- ✅ **Production-ready validation**

## 📁 Laravel Project Structure

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
│   └── web.php                  # Sitemap route definition
├── composer.json                 # Autoload configuration
├── usage-example.php            # Usage examples
├── simple-test.php              # Test runner
├── README.md                    # This documentation
└── public/
    └── sitemap.xml             # Generated sitemap
```

## 🚀 Production Laravel Usage

### **Environment-Specific Sitemaps**
```php
<?php
// app/Providers/AppServiceProvider.php
namespace App\Providers;

use App\Helpers\SitemapHelper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->environment('production')) {
            $sitemap = SitemapHelper::quickGenerate([
                // Production Eloquent models...
            ], [
                // Production Laravel routes...
            ]);
            
            $sitemap->save(public_path('sitemap.xml'));
        }
    }
}
```

### **Multiple Sitemaps for Large Laravel Applications**
```php
<?php
// Generate separate sitemaps for different sections
$postsSitemap = SitemapHelper::quickGenerate([
    ['model' => Post::class, 'route' => 'posts.show', 'change_freq' => 'weekly', 'priority' => 0.8, 'slug_column' => 'slug'],
], []);
$postsSitemap->save(public_path('sitemap-posts.xml'));

$productsSitemap = SitemapHelper::quickGenerate([
    ['model' => Product::class, 'route' => 'products.show', 'change_freq' => 'daily', 'priority' => 0.9, 'slug_column' => 'slug'],
], []);
$productsSitemap->save(public_path('sitemap-products.xml'));

// Generate sitemap index using Laravel URL helper
$indexSitemap = new SitemapHelper();
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
    return SitemapHelper::quickGenerate([
        // Your Eloquent models...
    ], [
        // Your Laravel routes...
    ])->generate();
});

return response($sitemap, 200, ['Content-Type' => 'application/xml']);
```

### **Laravel Queue Integration**
```php
<?php
// app/Jobs/GenerateSitemapJob.php
namespace App\Jobs;

use App\Helpers\SitemapHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $sitemap = SitemapHelper::quickGenerate([
            // Your Eloquent models...
        ], [
            // Your Laravel routes...
        ]);
        
        $sitemap->save(public_path('sitemap.xml'));
    }
}

// Dispatch the job
GenerateSitemapJob::dispatch();
```

## 🌟 Laravel Best Practices

- **Use proper Laravel autoloading** with `composer dump-autoload`
- **Use Eloquent model classes** instead of string names
- **Use Laravel named routes** with `route()` helper
- **Use Laravel file system helpers** like `public_path()`
- **Follow Laravel naming conventions** for controllers and commands
- **Use Laravel dependency injection** where appropriate
- **Follow Laravel scheduling patterns** for automated tasks
- **Use Laravel caching strategies** for performance
- **Follow Laravel queue patterns** for background processing

## 📝 Requirements

- **PHP 8.0+** (for modern Laravel compatibility)
- **Laravel 9+** (required for full functionality)
- **Composer** (for proper autoloading)

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Issues**: Create an issue on GitHub
- **Questions**: Check the Laravel usage examples
- **Testing**: Run `php simple-test.php`

## 🎉 Getting Started

Your Laravel Sitemap Helper is ready to use! Simply:

1. **Download** `SitemapHelper.php`
2. **Place** in your Laravel `app/Helpers/` directory
3. **Configure** autoloading in `composer.json`
4. **Run** `composer dump-autoload`
5. **Use** proper Laravel `use` statements

**Built following Laravel conventions with proper autoloading!** 🚀
