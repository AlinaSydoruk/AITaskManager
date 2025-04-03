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
        if ($parentId){
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
            'parentId' => $parentId
        ]);
    }


    #[Route('/{id}', name: 'show')]
    public function show(int $boardId,int $id): Response
    {
        $subcategory = $this->subcategoryRepository->findWithChildren($id);

        if (!$subcategory) {
            $this->addFlash('error', $this->translator->trans('error.folder not found'));
            return $this->redirectToRoute('app_home');
        }

        return $this->render('subcategory/show.html.twig', [
            'subcategory' => $subcategory,
            'boardId' => $boardId,
        ]);
    }
}
