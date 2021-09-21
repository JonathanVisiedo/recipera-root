<?php


namespace Ghost\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpException;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

final class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var Twig
     */
    private Twig $twig;
    /**
     * @var ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param Session $session
     * @param Twig $twig
     */
    public function __construct (ResponseFactoryInterface $responseFactory, Session $session, Twig $twig) {

        $this->session = $session;
        $this->twig = $twig;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        try {
            return $handler->handle($request);
        } catch (HttpException $httpException) {
            // Handle the http exception here
            $statusCode = $httpException->getCode();
            $response = $this->responseFactory->createResponse()->withStatus($statusCode);
            $errorMessage = sprintf('%s %s', $statusCode, $response->getReasonPhrase());

            // Log the error message
            // $this->logger->error($errorMessage);
            switch ($statusCode) {
                case 404:
                    return $response->withHeader('Location', '/404');
                    break;
            }

            return $response;
        }



    }
}