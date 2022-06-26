<?php

namespace App\Controller;

use App\Utils\HandleOrder;
use Monolog\Handler\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class OrderController extends AbstractController
{

    private $client;
    private $handleOrder;

    public function __construct(HttpClientInterface $client, HandleOrder $handleOrder)
    {
        $this->client = $client;
        $this->handleOrder = $handleOrder;
    }

    /**
     * @Route("/order", name="orders-to-csv")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(Request $request): Response
    {


        if ($request->getMethod() == "POST") {
            $content = $this->handleOrder->hendle();

            $response = new Response($content);

            $response->headers->set('Content-Type', 'text/csv');

            return $response;
        }



        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
}
