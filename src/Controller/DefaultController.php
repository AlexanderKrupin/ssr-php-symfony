<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Data;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{
    private $reactPath;
    private $domPath;
    private $domServerPath;
    private $ssrPath;

    public function __construct(
        string $reactPath,
        string $domPath,
        string $domServerPath,
        string $ssrPath
    ) {
        $this->reactPath = $reactPath;
        $this->domPath = $domPath;
        $this->domServerPath = $domServerPath;
        $this->ssrPath = $ssrPath;
    }

    /**
     * @Route(path="/")
     */
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route(path="/save")
     */
    public function save(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $data = (new Data())->setData(json_decode($request->getContent(), true));
        $em->persist($data);
        $em->flush();

        return new JsonResponse(['id' => $data->getId()]);
    }

    /**
     * @Route(path="/publish/{id}")
     */
    public function renderPage(int $id): Response
    {
        $data = $this->getDoctrine()->getManager()->find(Data::class, $id);

        if (!$data) {
            return new Response('<h1>Page not found</h1>', Response::HTTP_NOT_FOUND);
        }

        $engine = new \V8Js();

        ob_start();
        $engine->executeString($this->createJsString($data));

        return new Response(ob_get_clean());
    }

    private function createJsString(Data $data): string
    {
        $props = json_encode($data->getData());
        $bundle = $this->getRenderString();

        return <<<JS
var global = global || this, self = self || this, window = window || this;
$bundle;
print(ReactDOMServer.renderToString(React.createElement(Render, $props)));
JS;
    }

    private function getRenderString(): string
    {
        return
            sprintf(
                "%s\n%s\n%s\n%s",
                file_get_contents(
                    $this->reactPath,
                    true
                ),
                file_get_contents(
                    $this->domPath,
                    true
                ),
                file_get_contents(
                    $this->domServerPath,
                    true
                ),
                file_get_contents(
                    $this->ssrPath,
                    true
                )
            );
    }
}
