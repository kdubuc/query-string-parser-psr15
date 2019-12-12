<?php

namespace Kdubuc\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/*
 * If multiple fields of the same name exist in a query string, getQueryParams()
 * silently overwrites them (because of parse_str()). To handle uri like :
 * ?id=1&id=2, we normalize the input into a PHP compatible one
 * (eg : ?id[]=1&id[]=2).
 * GitHub Discussion : https://github.com/slimphp/Slim/issues/2378
 * Heavily inspired of https://secure.php.net/manual/fr/function.parse-str.php#76792 (thanks Evan K !)
 */
final class QueryStringParser implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     */
    public function process(ServerRequestInterface $server_request, RequestHandlerInterface $handler) : ResponseInterface
    {
        // Initialize the results array
        $results = [];

        // Loop on current query string splited on outer delimiter
        foreach (explode('&', $server_request->getUri()->getQuery()) as $pair) {
            // Split into name and value
            [$name, $value] = explode('=', $pair, 2);

            // URL decode
            $name  = rawurldecode($name);
            $value = rawurldecode($value);

            // If name already exists
            if (isset($results[$name])) {
                // Stick multiple values into an array
                if (\is_array($results[$name])) {
                    $results[$name][] = $value;
                } else {
                    $results[$name] = [$results[$name], $value];
                }
            }
            // Otherwise, simply stick it in a scalar
            else {
                $results[$name] = $value;
            }
        }

        // Build the new request with the correct query string
        $server_request = $server_request->withUri($server_request->getUri()->withQuery(http_build_query($results)));

        // Continue to process server request
        return $handler->handle($server_request);
    }
}
