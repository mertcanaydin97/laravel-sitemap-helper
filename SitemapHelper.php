<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SitemapHelper
{
    /**
     * The base URL for the sitemap
     */
    protected string $baseUrl;

    /**
     * Array of URLs to include in the sitemap
     */
    protected array $urls = [];

    /**
     * Array of excluded routes/patterns
     */
    protected array $excludedRoutes = [];

    /**
     * Default priority for URLs
     */
    protected float $defaultPriority = 0.5;

    /**
     * Default change frequency
     */
    protected string $defaultChangeFreq = 'weekly';

    /**
     * Constructor
     */
    public function __construct(string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? config('app.url');
        $this->excludedRoutes = [
            'admin/*',
            'api/*',
            'auth/*',
            'password/*',
            'email/*',
            'verification/*',
            'sanctum/*',
            'telescope/*',
            'horizon/*',
            'log-viewer/*',
            'debugbar/*',
            'broadcasting/*',
            'oauth/*',
            'socialite/*',
        ];
    }

    /**
     * Add a single URL to the sitemap
     */
    public function addUrl(
        string $url,
        string $lastModified = null,
        string $changeFreq = null,
        float $priority = null
    ): self {
        $this->urls[] = [
            'url' => $this->normalizeUrl($url),
            'last_modified' => $lastModified ?? now()->toISOString(),
            'change_freq' => $changeFreq ?? $this->defaultChangeFreq,
            'priority' => $priority ?? $this->defaultPriority,
        ];

        return $this;
    }

    /**
     * Add multiple URLs at once
     */
    public function addUrls(array $urls): self
    {
        foreach ($urls as $url) {
            if (is_string($url)) {
                $this->addUrl($url);
            } elseif (is_array($url)) {
                $this->addUrl(
                    $url['url'] ?? '',
                    $url['last_modified'] ?? null,
                    $url['change_freq'] ?? null,
                    $url['priority'] ?? null
                );
            }
        }

        return $this;
    }

    /**
     * Add all public routes to the sitemap
     */
    public function addRoutes(array $excludedRoutes = []): self
    {
        $excludedRoutes = array_merge($this->excludedRoutes, $excludedRoutes);
        
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            if ($this->shouldIncludeRoute($route, $excludedRoutes)) {
                $this->addUrl($route->uri());
            }
        }

        return $this;
    }

    /**
     * Add static pages
     */
    public function addStaticPages(array $pages): self
    {
        foreach ($pages as $page) {
            if (is_string($page)) {
                $this->addUrl($page);
            } elseif (is_array($page)) {
                $this->addUrl(
                    $page['url'] ?? '',
                    $page['last_modified'] ?? null,
                    $page['change_freq'] ?? null,
                    $page['priority'] ?? null
                );
            }
        }

        return $this;
    }

    /**
     * Add model-based URLs (e.g., blog posts, products)
     * Now accepts model class name as string for easier use in routes/controllers
     */
    public function addModels(string $modelClass, string $routeName, array $options = []): self
    {
        if (!class_exists($modelClass)) {
            return $this;
        }

        $models = $modelClass::query();
        
        // Apply any additional query constraints
        if (isset($options['where'])) {
            $models->where($options['where']);
        }
        
        // Apply ordering
        if (isset($options['orderBy'])) {
            $models->orderBy($options['orderBy']);
        }
        
        // Apply limit
        if (isset($options['limit'])) {
            $models->limit($options['limit']);
        }

        $models = $models->get();

        foreach ($models as $model) {
            // Generate route parameter (slug, ID, or custom)
            $routeParameter = $this->generateRouteParameter($model, $options);
            $url = route($routeName, $routeParameter);
            $lastModified = $model->updated_at ?? $model->created_at;
            
            $this->addUrl(
                $url,
                $lastModified ? $lastModified->toISOString() : null,
                $options['change_freq'] ?? 'monthly',
                $options['priority'] ?? 0.7
            );
        }

        return $this;
    }

    /**
     * Generate route parameter for the model
     */
    protected function generateRouteParameter($model, array $options): string
    {
        // If slug column is specified and exists, use it
        if (isset($options['slug_column']) && isset($model->{$options['slug_column']})) {
            return $model->{$options['slug_column']};
        }
        
        // If title column exists, generate slug from it
        if (isset($options['title_column']) && isset($model->{$options['title_column']})) {
            return $this->generateSlug($model->{$options['title_column']});
        }
        
        // Auto-detect title columns and generate slugs
        $titleColumn = $this->detectTitleColumn($model);
        if ($titleColumn && isset($model->{$titleColumn})) {
            return $this->generateSlug($model->{$titleColumn});
        }
        
        // Fallback to model ID
        return $model;
    }

    /**
     * Auto-detect title column in the model
     */
    protected function detectTitleColumn($model): ?string
    {
        $possibleColumns = ['title', 'name', 'headline', 'subject', 'label'];
        
        foreach ($possibleColumns as $column) {
            if (isset($model->{$column})) {
                return $column;
            }
        }
        
        return null;
    }

    /**
     * Generate a URL-friendly slug from text
     */
    protected function generateSlug(string $text): string
    {
        return Str::slug($text);
    }

    /**
     * Quick method to add models with common options
     * Perfect for use in routes and controllers
     */
    public function addModelCollection(
        string $modelClass, 
        string $routeName, 
        string $changeFreq = 'monthly',
        float $priority = 0.7,
        array $constraints = [],
        string $slugColumn = null,
        string $titleColumn = null
    ): self {
        $options = [
            'change_freq' => $changeFreq,
            'priority' => $priority,
            'where' => $constraints
        ];
        
        if ($slugColumn) {
            $options['slug_column'] = $slugColumn;
        }
        
        if ($titleColumn) {
            $options['title_column'] = $titleColumn;
        }
        
        return $this->addModels($modelClass, $routeName, $options);
    }

    /**
     * Add multiple model collections at once
     * Great for bulk sitemap generation
     */
    public function addModelCollections(array $collections): self
    {
        foreach ($collections as $collection) {
            if (isset($collection['model']) && isset($collection['route'])) {
                $this->addModelCollection(
                    $collection['model'],
                    $collection['route'],
                    $collection['change_freq'] ?? 'monthly',
                    $collection['priority'] ?? 0.7,
                    $collection['constraints'] ?? [],
                    $collection['slug_column'] ?? null,
                    $collection['title_column'] ?? null
                );
            }
        }

        return $this;
    }

    /**
     * Set excluded routes
     */
    public function setExcludedRoutes(array $routes): self
    {
        $this->excludedRoutes = $routes;
        return $this;
    }

    /**
     * Set default priority
     */
    public function setDefaultPriority(float $priority): self
    {
        $this->defaultPriority = $priority;
        return $this;
    }

    /**
     * Set default change frequency
     */
    public function setDefaultChangeFreq(string $changeFreq): self
    {
        $this->defaultChangeFreq = $changeFreq;
        return $this;
    }

    /**
     * Generate the XML sitemap
     */
    public function generate(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($this->urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['url']) . '</loc>' . "\n";
            
            if ($url['last_modified']) {
                $xml .= '    <lastmod>' . $url['last_modified'] . '</lastmod>' . "\n";
            }
            
            if ($url['change_freq']) {
                $xml .= '    <changefreq>' . $url['change_freq'] . '</changefreq>' . "\n";
            }
            
            if ($url['priority'] !== null) {
                $xml .= '    <priority>' . number_format($url['priority'], 1) . '</priority>' . "\n";
            }
            
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Generate and save the sitemap to a file
     */
    public function save(string $path = null): bool
    {
        $path = $path ?? public_path('sitemap.xml');
        
        try {
            File::put($path, $this->generate());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Generate and return the sitemap as a response
     */
    public function response(string $filename = 'sitemap.xml')
    {
        return response($this->generate(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get the current URLs array
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * Clear all URLs
     */
    public function clear(): self
    {
        $this->urls = [];
        return $this;
    }

    /**
     * Check if a route should be included in the sitemap
     */
    protected function shouldIncludeRoute($route, array $excludedRoutes): bool
    {
        // Skip routes without GET method
        if (!in_array('GET', $route->methods())) {
            return false;
        }

        // Skip routes with parameters
        if (str_contains($route->uri(), '{')) {
            return false;
        }

        // Skip excluded routes
        foreach ($excludedRoutes as $pattern) {
            if (fnmatch($pattern, $route->uri())) {
                return false;
            }
        }

        return true;
    }

    /**
     * Normalize URL to ensure it's absolute
     */
    protected function normalizeUrl(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return rtrim($this->baseUrl, '/') . $url;
        }

        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }

    /**
     * Generate a sitemap index file for multiple sitemaps
     */
    public static function generateIndex(array $sitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($sitemap['url']) . '</loc>' . "\n";
            
            if (isset($sitemap['last_modified'])) {
                $xml .= '    <lastmod>' . $sitemap['last_modified'] . '</lastmod>' . "\n";
            }
            
            $xml .= '  </sitemap>' . "\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Create a sitemap with common Laravel routes
     */
    public static function createDefault(): self
    {
        $helper = new self();
        
        // Add common static pages
        $helper->addStaticPages([
            ['url' => '/', 'priority' => 1.0, 'change_freq' => 'daily'],
            ['url' => '/about', 'priority' => 0.8, 'change_freq' => 'monthly'],
            ['url' => '/contact', 'priority' => 0.6, 'change_freq' => 'monthly'],
            ['url' => '/privacy-policy', 'priority' => 0.4, 'change_freq' => 'yearly'],
            ['url' => '/terms-of-service', 'priority' => 0.4, 'change_freq' => 'yearly'],
        ]);

        // Add public routes
        $helper->addRoutes();

        return $helper;
    }

    /**
     * Quick sitemap generation for common use cases
     * Perfect for routes and controllers
     */
    public static function quickGenerate(array $models = [], array $staticPages = []): self
    {
        $helper = new self();
        
        // Add static pages
        if (!empty($staticPages)) {
            $helper->addStaticPages($staticPages);
        }
        
        // Add model collections
        if (!empty($models)) {
            $helper->addModelCollections($models);
        }
        
        // Add public routes
        $helper->addRoutes();
        
        return $helper;
    }
}

