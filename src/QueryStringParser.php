<?php

namespace Kdubuc\Middleware;

use GuzzleHttp\Psr7\Query;
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
        // Parse a query string into an associative array using Guzzle
        // Handles duplicates fields.
        $params = Query::parse($server_request->getUri()->getQuery());

        // Build the new query string
        $query_string = http_build_query($params);

        // Assign new query string arguments
        $server_request = $server_request->withUri($server_request->getUri()->withQuery($query_string));
        $server_request = $server_request->withQueryParams($params);

        // Continue to process server request
        return $handler->handle($server_request);
    }
}
