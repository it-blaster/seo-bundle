<?php

namespace ItBlaster\SeoBundle\Service;

use ItBlaster\SeoBundle\Model\SeoParam;
use ItBlaster\SeoBundle\Model\SeoParamQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Seo service.
 */
class SeoService
{
    /**
     * Cache for models.
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * Object of Request
     * @var Request
     */
    protected $request;

    /**
     * Constructor
     *
     * @param RequestStack $request_stack
     */
    public function __construct(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    /**
     * Return query string and path info or url.
     *
     * @param string|null $url Url.
     *
     * @see Symfony\Component\HttpFoundation\Request
     *
     * @return array
     */
    public function getUrlInfo($url = null)
    {
        if (!$url) {
            $request = $this->request;

            $path = $request->getPathInfo();
            $query = $request->getQueryString();
        } else {
            $info = parse_url($url);

            $path  = isset($info['path'])  ? $info['path']  : null;
            $query = isset($info['query']) ? $info['query'] : null;
        }

        return array(
            'path'  => '/' . trim($path, '/'),
            'query' => !empty($query) ? '?' . $query : '',
        );
    }

    /**
     * Return array with upper-levels path.
     *
     * @param string $path Url path.
     *
     * @return array
     */
    public function getAvailablePaths($path)
    {
        $paths = array();
        // prepare string
        $path = '/' . trim($path, '/');
        while (!in_array($path, array('/', '\\'))) {
            $paths[] = $path;
            $path = dirname($path);
        }

        $paths[] = '/';

        return $paths;
    }

    /**
     * Try to find seo params for current page.
     *
     * @param string|null $url Url.
     *
     * @see ItBlaster\SeoBundle\Model\SeoParam
     *
     * @throw \UnexpectedValueException if SeoParam have unexpected type.
     *
     * @return SeoParam|null
     */
    public function getSeoParamByUrl($url = null)
    {
        $info = $this->getUrlInfo($url);
        $paths = $this->getAvailablePaths($info['path']);

        $main_url = $info['path'] . $info['query'];
        $model = $this->getCachedModel($main_url, $this->request->getLocale());

        if ($model === null && !empty($info['query'])) {
            $model = $this->getCachedModel($info['path'], $this->request->getLocale());
        }

        if ($model === null) {
            foreach ($paths as $path) {
                $model = $this->getCachedModel(rtrim($path, '/') . '/*', $this->request->getLocale());

                if ($model) {
                    break;
                }
            }
        }

        return $model;
    }

    /**
     * Try to load a model from db or get it from cache.
     *
     * If the model found in database it will be stored in cache before return.
     *
     * @param mixed $key Filter by url.
     * @param string $locale
     *
     * @see ItBlaster\SeoBundle\Model\SeoParam
     *
     * @return ItBlaster\SeoBundle\Model\SeoParam|null
     */
    protected function getCachedModel($key, $locale)
    {
        $cache_key = $key . '_' . $locale;

        if (!array_key_exists($cache_key, self::$cache)) {
            self::$cache[$cache_key] = SeoParamQuery::create()
                ->filterByUrl($key, \Criteria::LIKE)
                ->joinWithI18n($locale)
                ->orderByUrl()
                ->findOne()
            ;
        }

        return self::$cache[$cache_key];
    }
}
