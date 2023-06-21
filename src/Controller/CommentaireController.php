<?php

namespace App\Controller;

use App\Entity\Rating;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator): Response
    {
        /**
         * @var Commentaire $commentaires
         */
        $commentaires = $entityManager->getRepository(Rating::class)->findBy(['valide' => 0], ['date' => 'DESC']);
        // get actual date
        $now = Carbon::now();
        foreach ($commentaires as $commentaire) {
            // calculate the difference between submitted time and actual date
            $commentaire->diffTime = $now->diffForHumans($commentaire->getDate());
        }

        // handling pagination
        $pagination = $paginator->paginate(
            $commentaires, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );
        // rendering
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $pagination,
        ]);
    }

    #[Route('/{id}/valide', name: 'app_commentaire_valide', methods: ['GET'])]
    public function valide(EntityManagerInterface $entityManager, Rating $rating): Response
    {
        /*
         * @var Rating $rating
         */
        $rating->setValide(true);
        $entityManager->persist($rating);
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_commentaire_index');
    }

    #[Route('/{id}/delete', name: 'app_commentaire_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, Rating $rating): Response
    {
        /*
         * @var EntityManager $entityManager
         */
        $entityManager->remove($rating);
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_commentaire_index');
    }
}
