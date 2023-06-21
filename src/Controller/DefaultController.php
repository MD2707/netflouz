<?php

namespace App\Controller;

use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        /**
         * @var Series $Series
         */
        $Series = $entityManager->getRepository(Series::class);

        $randomSeries = $entityManager
        ->createQuery("SELECT q FROM App\Entity\Series q ORDER BY RAND()")
        ->setMaxResults(3);

        // handling pagination
        $paginatedSeries = $paginator->paginate(
            $randomSeries,
            $request->query->getInt('page', 1),
            9 // limit per page
        );

        // rendering
        return $this->render('default/index.html.twig', [
            'series' => $paginatedSeries,
        ]);
    }
}
