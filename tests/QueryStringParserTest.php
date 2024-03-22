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

        $server_request = $server_request->withQueryParams([
            'key' => 'value',
        ]);

        $handler = new class() implements RequestHandlerInterface {
            public $server_request;

            public function handle(ServerRequestInterface $server_request) : ResponseInterface
            {
                $this->server_request = $server_request;

                return new Response();
            }
        };

        $middleware->process($server_request, $handler);

        $this->assertSame('foo[0]=bar&foo[1]=baz&key=value', urldecode($handler->server_request->getUri()->getQuery()));
        $this->assertSame(['foo' => ['bar', 'baz'], 'key' => 'value'], $handler->server_request->getQueryParams());
    }
}
