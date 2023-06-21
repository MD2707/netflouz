<?php

namespace App\Controller;

use App\Entity\Series;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/FavoriteUser')]
class UserInfoController extends AbstractController
{
    #[Route('/', name: 'app_user_info', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,
    Request $request,
    PaginatorInterface $paginator): Response
    {
        /**
         * @var User $users
         */
        $users = $entityManager
            ->getRepository(User::class);

        /**
         * @var Form $form
         */
        $form = $this->createFormBuilder()
        ->setMethod('GET')
        ->add('Filtre', ChoiceType::class, [
            'label' => false,
            'choices' => [
                'Name' => 1,
                'Email' => 2,
            ],
        ])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get the data from the form
            $data = $form->getData();
            // the value of the input 'filtre'
            $filterValue = $data['Filtre'];
            // for each case in the 'Filtre' input the query change
            switch ($filterValue) {
                case 1:
                    $allAppointmentsQuery = $users->createQueryBuilder('p')
                    ->orderBy('p.name', 'ASC ')
                    ->where('p.email LIKE :p')
                    ->setParameter('p', '%'.$request->query->get('email').'%')
                    ->getQuery();
                    break;
                case 2:
                    $allAppointmentsQuery = $users->createQueryBuilder('p')
                    ->orderBy('p.email', 'ASC')
                    ->where('p.email LIKE :p')
                    ->setParameter('p', '%'.$request->query->get('email').'%')
                    ->getQuery();
                    break;
            }
        }
        // if the form is not submitted then choose the basic query
        else {
            $allAppointmentsQuery = $users->createQueryBuilder('p')
                ->getQuery();
        }

        // pagination handling
        $paginatedSeries = $paginator->paginate(
            $allAppointmentsQuery->getResult(),
            $request->query->getInt('page', 1),
            24
        );
        // rendering
        return $this->render('user_info/index.html.twig', [
            'form' => $form->createView(),
            'users' => $paginatedSeries,
        ]);
    }

    #[Route('/{id}', name: 'app_userpref_show', methods: ['GET'])]
    public function show(User $user, EntityManagerInterface $entityManager,
    Request $request,
    PaginatorInterface $paginator): Response
    {
        /**
         * @var Series $series
         */
        $series = $entityManager->getRepository(Series::class)->createQueryBuilder('s')
            ->getQuery();

        // rendering
        return $this->render('user_info/show.html.twig', [
            'users' => $user,
            'serie' => $series,
        ]);
    }
}
