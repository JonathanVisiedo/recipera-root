<?php


namespace Ghost\Middlewares;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashMessageMiddleware implements MiddlewareInterface
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
     * FlashMessageMiddleware constructor.
     * @param Session $session
     * @param Twig $twig
     */
    public function __construct (Session $session, Twig $twig) {

        $this->session = $session;
        $this->twig = $twig;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Flash message to display
        if ($this->session->has('flash')) {
            $this->twig->getEnvironment()->addGlobal('flash', $this->session->get('flash'));
            $this->session->remove('flash');
        }

        // Recover data from form input errors
        if ($this->session->has('recovery')) {
            $this->twig->getEnvironment()->addGlobal('recovery', $this->session->get('recovery'));
            $this->session->remove('recovery');
        }

        return $handler->handle($request);
    }
}