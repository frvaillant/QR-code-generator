<?php
namespace App\Controller;

use App\Form\Colors;
use App\Form\FormManager;
use App\Form\Levels;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Home page
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $qrCode = null;
        $errors = null;
        $maxSize = FormManager::MAX_SIZE;

        $request = Request::createFromGlobals();
        $data    = $request->request->all();

        $formManager = new FormManager($data);

        if($formManager->isSubmitted && $formManager->hasNotErrors()) {
            $qrCode = [
                'url'         => $formManager->getUrl(),
                'size'        => $formManager->getSize(),
                'color'       => $formManager->getColor(),
                'light_color' => $formManager->getLightColor(),
                'quality'     => $formManager->getQuality()
            ];
        }
        $errors = $formManager->getErrors();

        return $this->publish($this->twig->render('index.html.twig', [
            'url'          => $formManager->getUrl(),
            'size'         => $formManager->getSize(),
            'color'        => $formManager->getColor(),
            'light_color'  => $formManager->getLightColor(),
            'quality'      => $formManager->getQuality(),
            'max_size'     => $maxSize,
            'qr_code'      => $qrCode,
            'errors'       => $errors,
            'is_ok'        => $formManager->hasNotErrors(),
            'dark_colors'  => Colors::DARK_COLORS,
            'light_colors' => Colors::LIGHT_COLORS,
            'qualities'    => Levels::LEVELS,
        ]));
    }

    /**
     * @Route("/test", name="app_test")
     */
    public function test(): Response
    {
        $request = Request::createFromGlobals();

        if($request->request->get('url')) {
            var_dump('oui'); die;
        }

        return $this->publish(
            $this->twig->render(
                'test.html.twig', [

                ]
            )
        );
    }


}