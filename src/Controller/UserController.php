<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Rating;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        /**
         * @var User $user
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
                    'Id' => 0,
                    'Name' => 1,
                    'Email' => 2,
                    'registerDate' => 3,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // data of the form
            $data = $form->getData();
            // get the data of the input 'filtre'
            $filterValue = $data['Filtre'];
            // for each case of the 'filtre' input
            switch ($filterValue) {
                case 0:
                    $allAppointmentsQuery = $users->createQueryBuilder('p')
                        ->where('p.email LIKE :p')
                        ->setParameter('p', '%'.$request->query->get('email').'%')
                        ->getQuery();
                    break;
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
                case 3:
                    $allAppointmentsQuery = $users->createQueryBuilder('p')
                        ->orderBy('p.registerDate', 'ASC')
                        ->where('p.email LIKE :p')
                        ->setParameter('p', '%'.$request->query->get('email').'%')
                        ->getQuery();
                    break;
            }
        }
        // basic case when the form is not submitted
        else {
            $allAppointmentsQuery = $users->createQueryBuilder('p')
                ->getQuery();
        }

        // pagination handling
        $paginatedSeries = $paginator->paginate(
            $allAppointmentsQuery,
            $request->query->getInt('page', 1),
            25
        );

        // rendering
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $paginatedSeries,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = new User();

        /**
         * @var Form $form
         */
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // if the form is rightly submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // it permit to add the variable $user to the database
            $entityManager->persist($user);
            $entityManager->flush();
            // return to the specifiy page associated to the name in parameter
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // rendering
        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        // rendering
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var Form $form
         */
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // if the form is rightly submitted
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // return to the specifiy page associated to the name in parameter
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        // rendering
        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/promote', name: 'app_user_promote', methods: ['GET'])]
    public function promoteAdmin(User $user, EntityManagerInterface $entityManager): Response
    {
        /*
         * @var User $user
         */
        $user->setAdmin(true);
        /*
         * @var EntityManager $entityManager
         */
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/ban', name: 'app_user_ban', methods: ['GET'])]
    public function banUser(int $id, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var User $user
         */
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $user->setBan(true);
        $user->setAdmin(false);
        $series = $user->getSeries();
        foreach ($series as $serie) {
            $user->removeSeries($serie);
        }
        $entityManager->flush();

        /**
         * @var Episode $episode
         */
        $episode = $user->getEpisode();
        foreach ($episode as $ep) {
            $user->removeEpisode($ep);
        }
        $entityManager->flush();

        /**
         * @var Rating $comments
         */
        $comments = $user->getRates();
        foreach ($comments as $comment) {
            $entityManager->remove($comment);
        }
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/unban', name: 'app_user_unban', methods: ['GET'])]
    public function Unban(User $user, EntityManagerInterface $entityManager): Response
    {
        /*
         * @var User $user
         */
        $user->setBan(false);
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/demote', name: 'app_user_demote', methods: ['GET'])]
    public function DemoteAdmin(User $user, EntityManagerInterface $entityManager): Response
    {
        /*
         * @var User $user
         */
        $user->setAdmin(false);
        $entityManager->flush();
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // verifie la validité et l'intégrité des données du formulaire
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        // return to the specifiy page associated to the name in parameter
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
