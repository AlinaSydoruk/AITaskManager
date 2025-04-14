<?php

namespace App\Controller;

use App\Domain\User\Model\User;
use App\Entity\Board;
use App\Entity\Subcategory;
use App\Form\BoardType;
use App\Form\SubcategoryType;
use App\Repository\BoardRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/board', name: 'app_board_')]
class BoardController extends AbstractController
{
    public function __construct(
        private BoardRepository                   $boardRepository,
        private EntityManagerInterface            $entityManager,
        private readonly TranslatorInterface      $translator,
    )
    {
    }


    #[Route('/create', name: 'create')]
    public function create(Request $request, Security $security): Response
    {
        $user = $security->getUser();

        $board = new Board($user);
        $board->addSubcategory(new Subcategory($board))->setTitle('Categories');

        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($board);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('message.board_has_been_created'));
            return $this->redirectToRoute('app_home');
        }

        return $this->render('board/create.html.twig', [
            'form' => $form,
        ]);
    }



    #[Route('/{id}', name: 'show')]
    public function show(string $id): Response
    {
        $board = $this->boardRepository->find($id);
        return $this->render('board/show.html.twig', [
            'board' => $board,
            'boardId' => $board->getId(),
            ]);
    }

}
