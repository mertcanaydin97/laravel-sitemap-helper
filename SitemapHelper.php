<?php

/**
 * Laravel Sitemap Helper - Standalone Version
 * 
 * A simple, powerful helper to generate XML sitemaps for your Laravel website.
 * This version can be used without composer dump-autoload.
 * 
 * Features:
 * - Generate XML sitemaps with SEO-friendly URLs
 * - Support for model collections with slug columns
 * - Static pages and routes
 * - Comprehensive testing included
 * 
 * @author Mertcan Aydın
 * @version 1.0.0
 * @license MIT
 */

if (!class_exists('SitemapHelper')) {
    class SitemapHelper
    {
        protected array $urls = [];
        protected array $excludedRoutes = [
            'admin/*', 'api/*', 'auth/*', 'password/*', 'email/*', 
            'verification/*', 'sanctum/*', 'telescope/*', 'horizon/*', 
            'log-viewer/*', 'debugbar/*', 'broadcasting/*', 'oauth/*', 'socialite/*'
        ];
        protected float $defaultPriority = 0.5;
        protected string $defaultChangeFreq = 'monthly';

        /**
         * Create a new SitemapHelper instance
         */
        public function __construct()
        {
            // Initialize with default settings
        }

        /**
         * Create a default SitemapHelper instance
         */
        public static function createDefault(): self
        {
            return new self();
        }

        /**
         * Quick generate method for one-liner usage
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

        /**
         * Add a single URL to the sitemap
         */
        public function addUrl(string $url, ?string $lastmod = null, string $changeFreq = null, float $priority = null): self
        {
            $this->urls[] = [
                'url' => $this->normalizeUrl($url),
                'lastmod' => $lastmod,
                'change_freq' => $changeFreq ?? $this->defaultChangeFreq,
                'priority' => $priority ?? $this->defaultPriority
            ];

            return $this;
        }

        /**
         * Add multiple static pages at once
         */
        public function addStaticPages(array $pages): self
        {
            foreach ($pages as $page) {
                $this->addUrl(
                    $page['url'],
                    $page['lastmod'] ?? null,
                    $page['change_freq'] ?? null,
                    $page['priority'] ?? null
                );
            }

            return $this;
        }

        /**
         * Add a model collection to the sitemap
         */
        public function addModelCollection(
            string $modelClass,
            string $routeName,
            string $changeFreq = 'monthly',
            float $priority = 0.7,
            array $constraints = [],
            string $slugColumn = null
        ): self {
            $options = [
                'change_freq' => $changeFreq,
                'priority' => $priority,
                'where' => $constraints
            ];

            if ($slugColumn) {
                $options['slug_column'] = $slugColumn;
            }

            return $this->addModels($modelClass, $routeName, $options);
        }

        /**
         * Add multiple model collections at once
         */
        public function addModelCollections(array $collections): self
        {
            foreach ($collections as $collection) {
                $this->addModelCollection(
                    $collection['model'],
                    $collection['route'],
                    $collection['change_freq'] ?? 'monthly',
                    $collection['priority'] ?? 0.7,
                    $collection['where'] ?? [],
                    $collection['slug_column'] ?? null
                );
            }

            return $this;
        }

        /**
         * Add models to the sitemap
         */
        protected function addModels(string $modelClass, string $routeName, array $options = []): self
        {
            if (!class_exists($modelClass)) {
                return $this;
            }

            $query = $modelClass::query();

            // Apply constraints
            if (isset($options['where']) && is_array($options['where'])) {
                foreach ($options['where'] as $column => $value) {
                    if (is_array($value) && count($value) === 2) {
                        // Handle comparison operators: ['>', 100]
                        $query->where($column, $value[0], $value[1]);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }

            $models = $query->get();

            foreach ($models as $model) {
                // Use slug column if specified, otherwise use model ID
                $routeParameter = isset($options['slug_column']) ? $model->{$options['slug_column']} : $model;
                $url = $this->generateRouteUrl($routeName, $routeParameter);
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
         * Generate route URL safely
         */
        protected function generateRouteUrl(string $routeName, $parameter): string
        {
            try {
                if (function_exists('route')) {
                    return route($routeName, $parameter);
                }
                // Fallback if route helper doesn't exist
                return "/{$routeName}/" . (is_object($parameter) ? $parameter->id : $parameter);
            } catch (Exception $e) {
                // Fallback URL generation
                return "/{$routeName}/" . (is_object($parameter) ? $parameter->id : $parameter);
            }
        }

        /**
         * Add all public routes to the sitemap
         */
        public function addRoutes(): self
        {
            if (!function_exists('Route') || !method_exists(Route::class, 'getRoutes')) {
                return $this;
            }

            $routes = Route::getRoutes();

            foreach ($routes as $route) {
                if ($this->shouldIncludeRoute($route)) {
                    $this->addUrl($route->uri());
                }
            }

            return $this;
        }

        /**
         * Check if a route should be included in the sitemap
         */
        protected function shouldIncludeRoute($route): bool
        {
            $uri = $route->uri();

            // Skip routes with parameters
            if (strpos($uri, '{') !== false) {
                return false;
            }

            // Check excluded patterns
            foreach ($this->excludedRoutes as $pattern) {
                if (fnmatch($pattern, $uri)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Set excluded route patterns
         */
        public function setExcludedRoutes(array $patterns): self
        {
            $this->excludedRoutes = $patterns;
            return $this;
        }

        /**
         * Set default priority for URLs
         */
        public function setDefaultPriority(float $priority): self
        {
            $this->defaultPriority = $priority;
            return $this;
        }

        /**
         * Set default change frequency for URLs
         */
        public function setDefaultChangeFreq(string $changeFreq): self
        {
            $this->defaultChangeFreq = $changeFreq;
            return $this;
        }

        /**
         * Normalize URL format
         */
        protected function normalizeUrl(string $url): string
        {
            $url = trim($url);
            
            // Ensure URL starts with protocol or slash
            if (!preg_match('/^https?:\/\//', $url) && !preg_match('/^\//', $url)) {
                $url = '/' . $url;
            }

            return $url;
        }

        /**
         * Generate XML sitemap
         */
        public function generate(): string
        {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($this->urls as $url) {
                $xml .= '  <url>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars($url['url']) . '</loc>' . "\n";
                
                if ($url['lastmod']) {
                    $xml .= '    <lastmod>' . htmlspecialchars($url['lastmod']) . '</lastmod>' . "\n";
                }
                
                $xml .= '    <changefreq>' . htmlspecialchars($url['change_freq']) . '</changefreq>' . "\n";
                $xml .= '    <priority>' . number_format($url['priority'], 1) . '</priority>' . "\n";
                $xml .= '  </url>' . "\n";
            }

            $xml .= '</urlset>';

            return $xml;
        }

        /**
         * Generate sitemap index for multiple sitemaps
         */
        public function generateIndex(array $sitemaps): string
        {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($sitemaps as $sitemap) {
                $xml .= '  <sitemap>' . "\n";
                $xml .= '    <loc>' . htmlspecialchars($sitemap) . '</loc>' . "\n";
                $xml .= '    <lastmod>' . date('c') . '</lastmod>' . "\n";
                $xml .= '  </sitemap>' . "\n";
            }

            $xml .= '</sitemapindex>';

            return $xml;
        }

        /**
         * Return as HTTP response
         */
        public function response()
        {
            if (function_exists('response')) {
                return response($this->generate(), 200, [
                    'Content-Type' => 'application/xml'
                ]);
            }
            
            // Fallback if response helper doesn't exist
            header('Content-Type: application/xml');
            echo $this->generate();
            exit;
        }

        /**
         * Save sitemap to file
         */
        public function save(string $filePath): bool
        {
            $content = $this->generate();
            $directory = dirname($filePath);
            
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            return file_put_contents($filePath, $content) !== false;
        }

        /**
         * Get all URLs
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
         * Get URL count
         */
        public function count(): int
        {
            return count($this->urls);
        }

        /**
         * Check if sitemap is empty
         */
        public function isEmpty(): bool
        {
            return empty($this->urls);
        }
    }
}

// Helper functions for easier usage
if (!function_exists('sitemap')) {
    /**
     * Create a new SitemapHelper instance
     */
    function sitemap(): SitemapHelper
    {
        return new SitemapHelper();
    }
}

if (!function_exists('sitemap_quick')) {
    /**
     * Quick generate sitemap with models and static pages
     */
    function sitemap_quick(array $models = [], array $staticPages = []): SitemapHelper
    {
        return SitemapHelper::quickGenerate($models, $staticPages);
    }
}
