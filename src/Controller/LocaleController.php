<?php

namespace App\Controller;


use App\Entity\Language;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/locale', name: 'app_locale_')]
class LocaleController extends AbstractController
{
    public function __construct(
        private readonly Security               $security,
        private readonly EntityManagerInterface $entityManager,
        private readonly string                 $defaultLocale
    )
    {

    }


    #[Route('/change/{locale}', name: 'change')]
    public function change(string $locale, Request $request): RedirectResponse
    {
        $language = Language::tryFrom($locale)? :$this->defaultLocale;
        $request->getSession()->set('_locale', $language->value);
        $user = $this->security->getUser();
        if($user){
            $user->setLocale($language->value);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
