<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\Rating;
use App\Entity\Series;
use App\Form\SeriesType;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'app_series_index', methods: ['GET'])]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        /**
         * @var Series $series
         */
        $Series = $entityManager->getRepository(Series::class);

        $AllSeries = $Series->createQueryBuilder('p');

        if ($this->getUser()) {
            /**
             * @var Form $form
             */
            $form = $this->createFormBuilder()
                ->setMethod('GET')
                ->add('Filtre', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'label' => false,
                    'placeholder' => 'Select a sort',
                    'required' => false,
                    'choices' => [
                        'Default' => 0,
                        'Title' => 1,
                        'Date' => 2,
                        'Genre' => 3,
                        'Rating' => 4,
                        'Follow' => 5,
                    ],
                ])
                ->add('Genre', EntityType::class, [
                    'label' => false,
                    'class' => Genre::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Select a genre',
                    'required' => false,
                    'mapped' => true,
                    'attr' => ['class' => 'hidden'],
                ])
                ->add('Title', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Enter the name of a series',
                    ],
                ])
                ->getForm();
        } else {
            /**
             * @var Form $form
             */
            $form = $this->createFormBuilder()
                ->setMethod('GET')
                ->add('Filtre', ChoiceType::class, [
                    'label' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'placeholder' => 'Select a sort',
                    'required' => false,
                    'choices' => [
                        'Default' => 0,
                        'Title' => 1,
                        'Date' => 2,
                        'Genre' => 3,
                    ],
                ])
                ->add('Genre', EntityType::class, [
                    'label' => false,
                    'class' => Genre::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Select a genre',
                    'required' => false,
                    'mapped' => true,
                    'attr' => ['class' => 'hidden'],
                ])
                ->add('Title', TextType::class, [
                    'required' => false,
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Enter the name of a series',
                    ],
                ])
                ->getForm();
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get the data from the form
            $data = $form->getData();
            // the value of the input 'filtre'
            $filterValue = $data['Filtre'];
            // Verif if Filter is set
            if (isset($data['Filtre'])) {
                // Checks which filter is activated
                if (in_array(3, $filterValue) && !in_array(5, $filterValue)) {
                    if (isset($data['Genre']) && '' !== $data['Genre']) {
                        $AllSeries = $Series->createQueryBuilder('p')
                            ->leftJoin('p.genre', 'g')
                            ->where('g.id IN (:genre)')
                            ->setParameter('genre', $data['Genre']);
                    }
                }
                if (in_array(5, $filterValue) && !in_array(3, $filterValue)) {
                    /** @var \App\Entity\User */
                    $user = $this->getUser();
                    $currentUserId = $user->getId();
                    $AllSeries = $Series->createQueryBuilder('p')
                        ->join('p.user', 'u')
                        ->where('u.id = :userId')
                        ->setParameter('userId', $currentUserId);
                }
                if (in_array(5, $filterValue) && in_array(3, $filterValue)) {
                    if (!isset($data['Genre'])) {
                        /** @var \App\Entity\User */
                        $user = $this->getUser();
                        $currentUserId = $user->getId();
                        $AllSeries = $Series->createQueryBuilder('p')
                            ->join('p.user', 'u')
                            ->where('u.id = :userId')
                            ->setParameter('userId', $currentUserId);
                    }
                }
                if (in_array(5, $filterValue) && in_array(3, $filterValue)) {
                    if (isset($data['Genre']) && '' !== $data['Genre']) {
                        /** @var \App\Entity\User */
                        $user = $this->getUser();
                        $currentUserId = $user->getId();
                        $AllSeries = $Series->createQueryBuilder('p')
                            ->leftJoin('p.genre', 'g')
                            ->join('p.user', 'u')
                            ->where('u.id = :userId')
                            ->andWhere('g.id IN (:genre)')
                            ->setParameter('userId', $currentUserId)
                            ->setParameter('genre', $data['Genre']);
                    }
                }
                if (in_array(4, $filterValue)) {
                    $AllSeries->leftJoin('p.rates', 'c')
                        ->addSelect(' ROUND(AVG(c.value),1) as HIDDEN avg_value')
                        ->groupBy('p.id')->orderBy('avg_value', 'desc');
                }
                if (isset($data['Title']) && '' !== $data['Title']) {
                    $AllSeries
                        ->andWhere('p.title LIKE :title')
                        ->setParameter('title', '%'.$data['Title'].'%');
                }
                if (in_array(2, $filterValue)) {
                    $AllSeries->addOrderBy('p.yearStart', 'DESC');
                }
                if (in_array(1, $filterValue)) {
                    $AllSeries->addOrderBy('p.title', 'ASC');
                }
            }
        }

        // pagination handling
        $paginatedSeries = $paginator->paginate(
            $AllSeries,
            $request->query->getInt('page', 1),
            9 // Limit per 9
        );

        // rendering
        return $this->render('series/index.html.twig', [
            'series' => $paginatedSeries,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_series_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var Series $series
         */
        $series = new Series();

        /**
         * @var Form $form
         */
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($series);
            $entityManager->flush();

            return $this->redirectToRoute('app_series_index', [], Response::HTTP_SEE_OTHER);
        }

        // rendering
        return $this->renderForm('series/new.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_series_show', methods: ['GET'])]
    public function show(Series $series, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /**
         * @var Form $form
         */
        $form = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('comment', TextareaType::class, [
                'label' => 'Comment:',
                'required' => false,
                'empty_data' => 'null',
                'attr' => [
                    'placeholder' => 'Enter your comment here',
                ],
            ])
            ->add('rate', IntegerType::class, [
                'attr' => ['class' => 'my_css_class', 'placeholder' => 'Rate', 'min' => 0, 'max' => 5],
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // get the data from the form
            $data = $form->getData();
            // the value of the input 'comment'
            $comment = $data['comment'];
            // the value of the input 'rate'
            $rate = $data['rate'];

            return $this->redirectToRoute('app_series_comment', ['id' => $request->get('id'), 'comment' => $comment, 'rate' => $rate], Response::HTTP_SEE_OTHER);
        }

        /**
         * @var Rating $rating
         */
        $ratings = $entityManager->getRepository(Rating::class)->findBy(['series' => $series], ['date' => 'DESC']);
        $now = Carbon::now();
        foreach ($ratings as $rating) {
            $rating->diffTime = $now->diffForHumans($rating->getDate());
        }

        // pagination handling
        $paginatedSeries = $paginator->paginate(
            $ratings,
            $request->query->getInt('page', 1),
            9
        );

        /**
         * @var Rating $moyenne
         */

        // moyenne
        $moy = $entityManager->getRepository(Rating::class)->createQueryBuilder('r');
        $moy->select('AVG(r.value)/2 as average')
            ->where('r.series = :series')
            ->setParameter('series', $series);
        $result = $moy->getQuery()->getSingleResult();
        $average = $result['average'];

        // percentage of views
        $nbEpVue = '';
        /** @var \App\Entity\User */
        $user = $this->getUser();
        if (null != $user) {
            $epi = $entityManager->getRepository(Episode::class)->createQueryBuilder('e')
                ->leftJoin('e.user', 'us')
                ->leftJoin('e.season', 'seas')
                ->leftJoin('seas.series', 'ser')
                ->where('ser.id = :series')
                ->andWhere('us.id = :user')
                ->setParameter('series', $series->getId())
                ->setParameter('user', $user->getId())
                ->getQuery()
                ->getResult();

            $nbEp = $entityManager->getRepository(Episode::class)->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->leftJoin('e.season', 'season')
                ->leftJoin('season.series', 'series')
                ->where('series.id = :series_id')
                ->setParameter('series_id', $series->getId())
                ->getQuery()
                ->getSingleScalarResult();

            $nbEpVue = (count($epi) / $nbEp) * 100;
            if ($nbEpVue > 0) {
                $nbEpVue = number_format($nbEpVue, 0).' % watched';
            } else {
                $nbEpVue = '';
            }
        }

        // rending
        return $this->render('series/show.html.twig', [
            'series' => $series,
            'page' => $paginatedSeries,
            'average' => $average,
            'percentage_watched' => $nbEpVue,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_series_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        /**
         * @var Form $form
         */
        $form = $this->createForm(SeriesType::class, $series);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_series_index', [], Response::HTTP_SEE_OTHER);
        }

        // rending
        return $this->renderForm('series/edit.html.twig', [
            'series' => $series,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_series_delete', methods: ['POST'])]
    public function delete(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$series->getId(), $request->request->get('_token'))) {
            $entityManager->remove($series);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_series_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/suivre/{id}', name: 'app_series_fav', methods: ['GET'])]
    public function fav(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $series = $entityManager->getRepository(Series::class)->find($request->get('id'));
        $user->addSeries($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/suprFav/{id}', name: 'app_series_suprFav', methods: ['GET'])]
    public function suprSeriesFav(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $series = $entityManager->getRepository(Series::class)->find($request->get('id'));
        $user->removeSeries($series);
        $entityManager->flush();

        return $this->redirectToRoute('app_series_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/suivreEp/{id}/{idEp}', name: 'app_series_favEp', methods: ['GET'])]
    public function favEp(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $episode = $entityManager->getRepository(Episode::class)->find($request->get('idEp'));
        $user->addEpisode($episode);
        $season = $episode->getSeason();
        $series = $season->getSeries();
        $user->addEpisode($episode);
        foreach ($series->getSeasons() as $sea) {
            if ($sea->getNumber() < $season->getNumber()) {
                foreach ($sea->getEpisodes() as $ep) {
                    $user->addEpisode($ep);
                }
            } elseif ($sea->getNumber() == $season->getNumber()) {
                foreach ($sea->getEpisodes() as $ep) {
                    if ($ep->getNumber() < $episode->getNumber()) {
                        $user->addEpisode($ep);
                    }
                }
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_series_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/suprSuivreEp/{id}/{idEp}', name: 'app_series_suprFavEp', methods: ['GET'])]
    public function suprfavEp(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $episode = $entityManager->getRepository(Episode::class)->find($request->get('idEp'));
        $user->removeEpisode($episode);
        $entityManager->flush();

        return $this->redirectToRoute('app_series_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/comment/{id}/{comment}/{rate}', name: 'app_series_comment', methods: ['GET'])]
    public function comment(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $series = $entityManager->getRepository(Series::class)->find($request->get('id'));
        $alreadyComment = false;

        foreach ($user->getRates() as $rate) {
            if ($rate->getSeries()->getID() == $series->getId()) {
                $alreadyComment = true;
            }
        }
        if (false == $alreadyComment) {
            // Create a new instance of the Rating entity
            $rating = new Rating();

            // Define field values
            $rating->setSeries($series);
            $rating->setUser($user);
            $rating->setDate(new \DateTime());
            $rating->setValue($request->get('rate'));
            if ('null' == $request->get('comment')) {
                // $rating->setComment(null);
            } else {
                $rating->setComment($request->get('comment'));
            }

            // Persist the entity (add it to the list of entities to save)
            $entityManager->persist($rating);

            // Execute SQL queries to save the entity to the database
            $entityManager->flush();
            $this->addFlash('great', 'En Cours de vérification');
        } else {
            $this->addFlash('error', 'DEJA COMMENTE');
        }

        return $this->redirectToRoute('app_series_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/{rate}', name: 'app_series_showByRate', methods: ['GET'])]
    public function showByRate(Series $series, Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $form = $this->createFormBuilder()
        ->setMethod('GET')
        ->add('comment', TextareaType::class, [
                'label' => 'Comment:',
                'required' => false,
                'empty_data' => 'null',
                'attr' => [
                'placeholder' => 'Enter your comment here',
                ],
            ])
            ->add('rate', IntegerType::class, [
                'attr' => ['class' => 'my_css_class', 'placeholder' => 'Rate', 'min' => 0, 'max' => 5],
            ])
        ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $comment = $data['comment'];
            $rate = $data['rate'];

            return $this->redirectToRoute('app_series_comment', ['id' => $request->get('id'), 'comment' => $comment, 'rate' => $rate], Response::HTTP_SEE_OTHER);
        }

        $ratings = $entityManager->getRepository(Rating::class)->createQueryBuilder('r')
        ->where('r.series = :series')
        ->andWhere('r.value = :rate*2')
        ->setParameter('series', $series)
        ->setParameter('rate', $request->get('rate'))
        ->getQuery()
        ->getResult();

        $now = Carbon::now();
        foreach ($ratings as $rating) {
            $rating->diffTime = $now->diffForHumans($rating->getDate());
        }
        $paginatedSeries = $paginator->paginate(
            $ratings,
            $request->query->getInt('page', 1),
            9
        );
        // pourcentage de vue
        /** @var \App\Entity\User */
        $user = $this->getUser();
        $epi = $entityManager->getRepository(Episode::class)->createQueryBuilder('e')
        ->leftJoin('e.user', 'us')
        ->leftJoin('e.season', 'seas')
        ->leftJoin('seas.series', 'ser')
        ->where('ser.id = :series')
        ->andWhere('us.id = :user')
        ->setParameter('series', $series->getId())
        ->setParameter('user', $user->getId())
        ->getQuery()
        ->getResult();

        $nbEp = $entityManager->getRepository(Episode::class)->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.season', 'season')
            ->leftJoin('season.series', 'series')
            ->where('series.id = :series_id')
            ->setParameter('series_id', $series->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $nbEpVue = (count($epi) / $nbEp) * 100;
        if ($nbEpVue > 0) {
            $nbEpVue = number_format($nbEpVue, 0).' % watched';
        } else {
            $nbEpVue = '';
        }
        // moyenne
        $moy = $entityManager->getRepository(Rating::class)->createQueryBuilder('r');
        $moy->select('AVG(r.value)/2 as average')
            ->where('r.series = :series')
            ->setParameter('series', $series);
        $result = $moy->getQuery()->getSingleResult();
        $average = $result['average'];

        return $this->render('series/show.html.twig', [
            'series' => $series,
            'page' => $paginatedSeries,
            'percentage_watched' => $nbEpVue,
            'average' => $average,
            'form' => $form->createView(),
        ]);
    }
}
