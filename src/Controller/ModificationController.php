<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ModificationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modify')]
class ModificationController extends AbstractController
{
    #[Route('/{id}', name: 'app_modify')]
    public function modify($id, User $user, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var Form $form
         */
        $form = $this->createForm(ModificationFormType::class, $user);
        $form->handleRequest($request);
        // if the form is rightly submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if (null != $form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }
            // if country is not a int value, set it to the current value

            if (null != $form->get('country')->getData()) {
                $user->setCountry($form->get('country')->getData());
            } else {
                $user->setCountry($user->getCountry());
            }
            if (null != $form->get('name')->getData()) {
                $user->setName($form->get('name')->getData());
            } else {
                $user->setName($user->getName());
            }
            // add it definitively in the database
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_default');
        }

        return $this->render('modification/index.html.twig', [
            'modificationForm' => $form->createView(),
            'id' => $id]);
    }
}
