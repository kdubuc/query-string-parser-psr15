<?php

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Kdubuc\Middleware\QueryStringParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class QueryStringParserTest extends TestCase
{
    public function testKeepDuplicates()
    {
        $middleware = new QueryStringParser();

        $server_request = new ServerRequest('GET', '/api?foo=bar&foo=baz', [], null, '1.1', []);

        $handler = new class() implements RequestHandlerInterface {
            public $latest_query_params_used;

            public function handle(ServerRequestInterface $server_request) : ResponseInterface
            {
                $this->latest_query_params_used = $server_request->getUri()->getQuery();

                return new Response();
            }
        };

        $middleware->process($server_request, $handler);

        $this->assertSame('foo[0]=bar&foo[1]=baz', urldecode($handler->latest_query_params_used));
    }
}
