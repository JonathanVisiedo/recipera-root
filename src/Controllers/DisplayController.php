<?php


namespace Ghost\Controllers;


use Ghost\Exception\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class DisplayController
{

    /**
     * @var Twig
     */
    private Twig $twig;


    /**
     * DisplayController constructor.
     * @param Twig $twig
     */
    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function index (ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface {

        return $this->twig->render($response, 'Http/index.html.twig', [
            'seo_title' => 'Let\'s find something to eat...'
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {

        if($request->getMethod() == 'GET') {
            return $this->twig->render($response, 'Http/create.html.twig', [
                'seo_title' => 'Create your recipe'
            ]);
        }

        dd($request->getParsedBody());

        try {



        } catch (ValidationException $e) {

        }


    }



}