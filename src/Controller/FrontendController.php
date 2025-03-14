<?php

namespace App\Controller;

use App\Repository\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontendController extends AbstractController
{
    public function __construct(
        private BoardRepository       $boardRepository
    )
    {
    }
    #[Route('/', name: 'app_frontend')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }


    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        $allBoards = $this->boardRepository->findAll();
        return $this->render("welcome.html.twig",[
            'boards' =>$allBoards
        ]);
    }
}
