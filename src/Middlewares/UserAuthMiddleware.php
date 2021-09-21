<?php


namespace Ghost\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;

final class UserAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var Session
     */
    private Session $session;

    /**
     * AdminAuthMiddleware constructor.
     * @param Session $session
     */
    public function __construct(Session $session) {

        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $session = $this->session->has('userAuthSession');
        $response = $handler->handle($request);

        if(!$session) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $this->session->set('flash', ['danger' => 'Veuillez vous connecter']);
            return $response->withHeader('Location', $routeParser->urlFor('index'));
        }

        return $response;
    }
}