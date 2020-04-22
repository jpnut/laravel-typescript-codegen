<?php

namespace JPNut\CodeGen;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RouteUrlGenerator
{
    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected UrlGenerator $url;

    /**
     * Create a new Route URL generator.
     *
     * @param  \Illuminate\Routing\UrlGenerator  $url
     * @return void
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * Generate a URL for the given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  array  $parameters
     * @return string
     */
    public function to($route, $parameters = [])
    {
        $domain = $this->getRouteDomain($route);

        return $this->url->format(
            $root = $this->replaceRootParameters($route, $domain, $parameters),
            $this->replaceRouteParameters($route->uri(), $parameters),
            $route
        );
    }

    /**
     * Get the formatted domain for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getRouteDomain($route)
    {
        return $route->getDomain() ? $this->formatDomain($route) : null;
    }

    /**
     * Format the domain and port for the route and request.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function formatDomain($route)
    {
        return $this->getRouteScheme($route).$route->getDomain();
    }

    /**
     * Get the scheme for the given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getRouteScheme($route)
    {
        if ($route->httpOnly()) {
            return 'http://';
        } elseif ($route->httpsOnly()) {
            return 'https://';
        }

        return $this->url->formatScheme();
    }

    /**
     * Replace the parameters on the root path.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @param  string  $domain
     * @param  array  $parameters
     * @return string
     */
    protected function replaceRootParameters($route, $domain, &$parameters)
    {
        $scheme = $this->getRouteScheme($route);

        return $this->replaceRouteParameters(
            $this->url->formatRoot($scheme, $domain), $parameters
        );
    }

    /**
     * Replace all of the wildcard parameters for a route path.
     *
     * @param  string  $path
     * @param  array  $parameters
     * @return string
     */
    protected function replaceRouteParameters($path, array &$parameters)
    {
        $path = preg_replace_callback('/{.*?}/', function ($match) use (&$parameters) {
            // Reset only the numeric keys...
            $parameters = array_merge($parameters);

            return (!isset($parameters[0]) && !Str::endsWith($match[0], '?}'))
                ? $match[0]
                : Arr::pull($parameters, 0);
        }, $path);

        return trim(preg_replace('/{.*?\?}/', '', $path), '/');
    }
}
