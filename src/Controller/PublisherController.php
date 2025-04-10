<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublisherController extends AbstractController
{
    #[Route('/', name: 'index_publisher')]
    public function index(): Response
    {
        $markets = ['BTC-USD', 'ETH-USD', 'LTC-USD'];
        return $this->render('publisher/index.html.twig', [
            'markets' => $markets,
        ]);
    }
}
