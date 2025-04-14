<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use App\Repository\BoardRepository;
use App\Repository\SubcategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/board/{boardId}/subcategory', name: 'app_subcategory_')]
class SubcategoryController extends AbstractController
{
    public function __construct(
        private readonly SubcategoryRepository $subcategoryRepository,
        private EntityManagerInterface         $entityManager,
        private readonly TranslatorInterface   $translator,
        private readonly BoardRepository       $boardRepository
    )
    {
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, int $boardId): Response
    {

        $parentId = $request->get('parentId') ?: null;
        $parent = null;
        if ($parentId) {
            $parent = $this->subcategoryRepository->find($parentId);
        }

        $subcategory = new Subcategory($this->boardRepository->find($boardId));
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subcategory->setParent($parent);
            $this->entityManager->persist($subcategory);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_subcategory_show', [
                'id' => $subcategory->getId(),
                'boardId' => $boardId,
                'subcategory' => $subcategory,
            ]);
        }

        return $this->render('subcategory/create.html.twig', [
            'form' => $form,
            'boardId' => $boardId,
            'parentId' => $parentId,
            'subcategory'=> $parent
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, int $boardId, int $id): Response
    {



        return $this->render('subcategory/create.html.twig', [

        ]);
    }




    #[Route('/{id}', name: 'show')]
    public function show(int $boardId, ?int $id=null): Response
    {
        $board = $this->boardRepository->find($boardId);
        if (!$board) {
            return new Response("Board with id : $boardId does not exist", Response::HTTP_NOT_FOUND);
        }
        if ($id) {
            $subcategory = $this->subcategoryRepository->findWithChildren($id);
            if (!$subcategory) {
                return new Response("Subcategory with id : $id does not exist", Response::HTTP_NOT_FOUND);
            }
        } else {
            $subcategory = $this->subcategoryRepository->findRootByBoard($board);
        }
        return $this->render('subcategory/show.html.twig', [
            'subcategory' => $subcategory,
            'boardId' => $boardId,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $boardId, int $id): Response
    {
        $board = $this->boardRepository->find($boardId);
        if (!$board) {
            return new Response("Board with id : $boardId does not exist", Response::HTTP_NOT_FOUND);
        }

        $subcategory = $this->subcategoryRepository->find($id);
        $subcategoryParent = $subcategory->getParent();
        if (!$subcategory) {
            return new Response("Subcategory with id : $id does not exist", Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($subcategory);
        $this->entityManager->flush();

        if ($subcategoryParent) {
            return $this->redirectToRoute('app_subcategory_show',[
                'subcategory' => $subcategoryParent->getId(),
                'boardId' => $boardId,
            ]);
        }
        return $this->redirectToRoute('app_board_show', [
            'id' => $boardId,
        ]);
    }
}
