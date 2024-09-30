<?php

namespace App\Controller;

use App\Entity\Name;
use App\Entity\Person;
use App\Entity\Ranking;
use App\Form\NameType;
use App\Form\PersonType;
use Doctrine\ORM\EntityManagerInterface;
use Rating\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(name: 'name_rank_')]
class DefaultController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ){}

    #[Route(path: '/person/new', name: 'person_name')]
    public function newPersonAction(Request $request)
    {
        $people = $this->em->getRepository(Person::class)->findAll();

        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save
            $this->em->persist($person);

            $this->em->flush();

            return $this->redirectToRoute('name_rank_compare_as');
        }

        return $this->render(
            'person.html.twig',
            [
                'newform' => $form->createView(),
                'people' => $people
            ]
        );
    }

    #[Route(path: '/name/new', name: 'new')]
    public function newAction(Request $request)
    {
        $names = $this->em->getRepository(Name::class);

        $name = new Name();
        $form = $this->createForm(NameType::class, $name);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save
            $this->em->persist($name);
            $this->createRankingForAllPeople($name);

            $this->em->flush();

            return $this->redirectToRoute('name_rank_new');
        }

        return $this->render(
            'newname.html.twig',
            [
                'names' => $names->findAll(),
                'newform' => $form->createView()
            ]
        );
    }

    #[Route(path: '/name/delete/{name}', name: 'delete')]
    public function deleteNameAction(Name $name)
    {
        foreach($name->getRanking() as $ranking)
        {
            $this->em->remove($ranking);
        }

        $this->em->remove($name);

        $this->em->flush();

        return $this->redirectToRoute('name_rank_names');

    }

    #[Route(path: '/name/updateall', name: 'name_updateall')]
    public function updateAllAction(Request $request)
    {
        $number_of_names_to_update = 0;

        $names = $this->em->getRepository(Name::class)->findAll();

        $form = $this->createFormBuilder()
            ->add('UpdateAll', SubmitType::class, ['label' => 'Update All Rankings'])
            ->getForm()
        ;

        $form->handleRequest($request);


        if($form->isSubmitted()) {
            foreach ($names as $name) {
                $this->createRankingForAllPeople($name);
            }
        }

        // Get countof those that need updating
        foreach($names as $name)
        {
            if ($this->createRankingForAllPeople($name, true))
            {
                $number_of_names_to_update++;
            }
        }

        return $this->render(
            'updateall.html.twig',
            [
                'numtoupdate' => $number_of_names_to_update,
                'form' => $form->createView()
            ]
        );

    }

    private function createRankingForAllPeople($name, $count = false)
    {
        // Fetch all people who don't already have a ranking for $name
        $query = $this->em->createQuery("
          SELECT p FROM ".Person::class." p WHERE p.id NOT IN (
            SELECT IDENTITY(r.person) FROM ".Ranking::class." r WHERE r.name = :nameid
           )");
        $query->setParameter('nameid', $name->getId());
        $people_missing_ranking = $query->execute();
        if($count)
        {
            return sizeof($people_missing_ranking);
        }
        foreach($people_missing_ranking as $person)
        {
            $ranking = new Ranking();
            $name->addRanking($ranking);
            $person->addRanking($ranking);

            $this->em->persist($ranking);
            $this->em->persist($name);
            $this->em->persist($person);
        }
        return $this->em->flush();
    }

    #[Route(path: '/names', name: 'names')]
    public function listNamesAction()
    {
        $people = $this->em->getRepository(Person::class);
        $names = $this->em->createQuery('
            SELECT n, SUM(r.rank) as HIDDEN overallrank
            FROM '.Name::class.' n
            JOIN '.Ranking::class.' r
            WHERE n.id = r.name
            GROUP BY n.id
            ORDER BY overallrank DESC
        ')->execute();

        return $this->render(
            'names.html.twig',
            [
                'people' => $people->findAll(),
                'names' => $names,

            ]
        );

    }

    #[Route(path: '/compare/', name: 'compare_as')]
    #[Route(path: '/', name: 'compare_as_home')]
    public function compareAs()
    {
        $people = $this->em->getRepository(Person::class);

        return $this->render(
            'compareas.html.twig',
            [
                'people' => $people->findAll()
            ]
        );

    }

    #[Route(path: '/compare/{person}/{gender}', name: 'compare')]
    public function compareNamesAction(Request $request, Person $person, string $gender = null)
    {
        $random_gender = False;
        if ($gender === null) {
            $ismale = random_int(0,1);
            $gender = $ismale ? 'male' : 'female';
            $random_gender = True;
        } else {
            $ismale = $gender === 'male' ? 1 : 0;
        }


        $query = $this->em->createQuery('
          SELECT r, (random() * (r.numberOfComparisons + 1)) as HIDDEN randcomp
          FROM ' . Ranking::class. ' r JOIN '.Name::class.' n
          WHERE n.id = r.name
          AND n.is_male = :ismale
          AND r.person = :person
          ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('ismale', $ismale);
        $query->setParameter('person', $person->getId());
        $ranking1 = $query->execute();
        if (sizeof($ranking1) < 1) {
            $this->addFlash('error', "Not enough $gender names");
            return $this->redirectToRoute('name_rank_new');
        }
        $name1 = $ranking1[0]->getName();


        $query = $this->em->createQuery('
          SELECT r, (random() * (r.numberOfComparisons + 1)) as HIDDEN randcomp
          FROM ' . Ranking::class. ' r JOIN '.Name::class.' n
          WHERE n.id = r.name
          AND n.is_male = :ismale
          AND r.id != :id
          AND r.person = :person
          ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('id', $ranking1[0]->getId());
        $query->setParameter('ismale', $ismale);
        $query->setParameter('person', $person);
        $ranking2 = $query->execute();
        if (sizeof($ranking2) < 1) {
            $this->addFlash('error', "Not enough $gender names");
            return $this->redirectToRoute('name_rank_new');
        }
        $name2 = $ranking2[0]->getName();

        $form = $this->createFormBuilder()
            ->add('name1', SubmitType::class, ['label' => $name1->getName()])
            ->add('name2', SubmitType::class, ['label' => $name2->getName()])
            ->add('name1val', HiddenType::class, ['data' => $ranking1[0]->getId()])
            ->add('name2val', HiddenType::class, ['data' => $ranking2[0]->getId()])
            ->getForm()
            ;

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $rankings = $this->em->getRepository(Ranking::class);
            $ranking1 = $rankings->findById($form->get('name1val')->getData())[0];
            $ranking2 = $rankings->findById($form->get('name2val')->getData())[0];
            if($form->get('name1')->isClicked())
            {
                // Name1 wins
                $rating = new \App\Rating\Rating($ranking1->getRank(), $ranking2->getRank(), 1, 0);
            }
            if($form->get('name2')->isClicked())
            {
                // Name 1 lost
                $rating = new \App\Rating\Rating($ranking1->getRank(), $ranking2->getRank(), 0, 1);
            }

            $results = $rating->getNewRatings();
            $ranking1->setRank($results['a']);
            $ranking1->incrementNumberOfComparisons();
            $ranking2->setRank($results['b']);
            $ranking2->incrementNumberOfComparisons();
            $this->em->persist($ranking1);
            $this->em->persist($ranking2);
            $this->em->flush();

            if ($random_gender)
            {
                return $this->redirectToRoute('name_rank_compare', ['person' => $person->getId()]);
            }
            return $this->redirectToRoute('name_rank_compare', ['person' => $person->getId(), 'gender' => $gender]);

        }

        return $this->render(
            'compare.html.twig',
            [
                'name1' => $ranking1[0],
                'name2' => $ranking2[0],
                'ismale' => $ismale,
                'form' => $form->createView(),
                'person' => $person,
            ]
        );
    }
}

