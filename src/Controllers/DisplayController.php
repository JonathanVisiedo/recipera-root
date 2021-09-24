<?php


namespace Ghost\Controllers;


use Ghost\Exception\ValidationException;
use Ghost\Models\Recipe;
use Ghost\Services\FilesHandler;
use Ghost\Services\FoodApiService;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;
use Symfony\Component\HttpFoundation\Session\Session;

class DisplayController
{

    /**
     * @var Twig
     */
    private Twig $twig;
    private Session $session;
    private FilesHandler $filesHandler;
    private Recipe $recipe;
    private Logger $logger;


    /**
     * DisplayController constructor.
     * @param Twig $twig
     * @param Session $session
     * @param Logger $logger
     * @param FilesHandler $filesHandler
     * @param Recipe $recipe
     */
    public function __construct(Twig $twig, Session $session, Logger $logger, FilesHandler $filesHandler, Recipe $recipe)
    {
        $this->twig = $twig;
        $this->session = $session;
        $this->filesHandler = $filesHandler;
        $this->recipe = $recipe;
        $this->logger = $logger;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {

        return $this->twig->render($response, 'Http/index.html.twig', [
            'seo_title' => 'Let\'s find something to eat...',
            'recipes' => $this->recipe->getAll()
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        if ($request->getMethod() == 'GET') {
            return $this->twig->render($response, 'Http/create.html.twig', [
                'seo_title' => 'Create your recipe'
            ]);
        }


        try {
            $post = $request->getParsedBody();
            $files = $request->getUploadedFiles();
            $uploaded = false;
            $uploaded = $this->filesHandler->uploadImage($files['picture'], 'recipes', (!empty($post['slug']) ? $post['slug'] : null));
            $post['picture'] = $uploaded['file'];

            $this->recipe->create($post);
            $this->session->set('flash', ['success' => 'Votre recette a été créé avec succès.']);

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $response->withHeader('Location', $routeParser->UrlFor('index'));

        } catch (ValidationException $e) {
            if ($uploaded !== false) $this->filesHandler->deleteImage($uploaded['dest']);

            $this->session->set('flash', ['danger' => $e->getMessage()]);
            $this->session->set('recovery', $post);
            $this->logger->error($e->getMessage(), ['type' => 'Recipe::create', 'code' => $e->getCode(), 'data' => $post]);

            return $response->withHeader('Location', $routeParser->UrlFor('index'));
        }


    }

    public function view(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {

        $recipe = $this->recipe->getBySlug($args['slug']);
        $api = new FoodApiService();


        foreach ($recipe['ingredients'] as $key => $ingredient) {
            $data = $api->call($ingredient['barcode']); // nutella : 3017620422003  eau : 3254381025887 beurre: 3451790011245 coca: 5000112546415  jusorange: 3502110009449
            $recipe['ingredients'][$key]['nutriments'] = $api->getNutriments($data);
        }

        $recipe['nutri_table'] = $api->nutri_table($recipe);

        return $this->twig->render($response, 'Http/view.html.twig', ['recipe' => $recipe]);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {

        $recipe = $this->recipe->getBySlug($args['slug']);
        $this->filesHandler->deleteImage($recipe['picture']);
        $this->recipe->delete($recipe['id']);

        $this->session->set('flash', [
            'info' => 'Votre recette a été supprimée'
        ]);
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        return $response->withHeader('Location', $routeParser->UrlFor('index'));

    }


}