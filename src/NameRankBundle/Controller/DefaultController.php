<?php

namespace NameRankBundle\Controller;

use NameRankBundle\Entity\Name;
use NameRankBundle\Entity\Person;
use NameRankBundle\Entity\Ranking;
use NameRankBundle\Form\NameType;
use NameRankBundle\Form\PersonType;
use Rating\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function newPersonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $people = $em->getRepository('NameRankBundle:Person')->findAll();


        $person = new Person();
        $form = $this->createForm(new PersonType(), $person);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // save
            $em->persist($person);

            $em->flush();

            return $this->redirectToRoute('name_rank_person_new');
        }

        return $this->render(
            'NameRankBundle:Default:person.html.twig',
            [
                'newform' => $form->createView(),
                'people' => $people
            ]
        );
    }

    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $names = $em->getRepository('NameRankBundle:Name');

        $name = new Name();
        $form = $this->createForm(new NameType(), $name);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // save
            $em->persist($name);
            $this->createRankingForAllPeople($name);

            $em->flush();

            return $this->redirectToRoute('name_rank_new');
        }

        return $this->render(
            'NameRankBundle:Default:newname.html.twig',
            [
                'names' => $names->findAll(),
                'newform' => $form->createView()
            ]
        );
    }

    public function updateAllAction(Request $request)
    {
        $number_of_names_to_update = 0;

        $em = $this->getDoctrine()->getManager();
        $names = $em->getRepository('NameRankBundle:Name')->findAll();

        $form = $this->createFormBuilder()
            ->add('UpdateAll', 'submit', array('label' => 'Update All Rankings'))
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
            'NameRankBundle:Default:updateall.html.twig',
            [
                'numtoupdate' => $number_of_names_to_update,
                'form' => $form->createView()
            ]
        );

    }

    private function createRankingForAllPeople($name, $count = false)
    {
        // Fetch all people who don't already have a ranking for $name
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("
          SELECT p FROM NameRankBundle\Entity\Person p WHERE p.id NOT IN (
            SELECT IDENTITY(r.person) FROM NameRankBundle\Entity\Ranking r WHERE r.name = :nameid
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

            $em->persist($ranking);
            $em->persist($name);
            $em->persist($person);
        }
        return $em->flush();
    }

    public function listNamesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $people = $em->getRepository('NameRankBundle:Person');

        return $this->render(
            'NameRankBundle:Default:names.html.twig',
            [
                'people' => $people->findAll()
            ]
        );

    }

    public function compareAsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $people = $em->getRepository('NameRankBundle:Person');

        return $this->render(
            'NameRankBundle:Default:compareas.html.twig',
            [
                'people' => $people->findAll()
            ]
        );

    }

    public function compareNamesAction(Request $request, $personid)
    {
        $ismale = rand(0,1);

        $em = $this->getDoctrine()->getManager();
        $people = $em->getRepository('NameRankBundle:Person');
        $person = $people->findById($personid)[0];

        $query = $em->createQuery('
          SELECT r, (RAND() * (n.numberOfComparisons + 1)) as HIDDEN randcomp
          FROM NameRankBundle\Entity\Ranking r JOIN NameRankBundle\Entity\Name n
          WHERE n.is_male = :ismale
          AND r.person = :person
          ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('ismale', $ismale);
        $query->setParameter('person', $personid);
        $ranking1 = $query->execute();
        $name1 = $ranking1[0]->getName();


        $query = $em->createQuery('
          SELECT r, (RAND() * (n.numberOfComparisons + 1)) as HIDDEN randcomp
          FROM NameRankBundle\Entity\Ranking r JOIN NameRankBundle\Entity\Name n
          WHERE  n.is_male = :ismale AND r.id != :id
          AND r.person = :person
          ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('id', $ranking1[0]->getId());
        $query->setParameter('ismale', $ismale);
        $query->setParameter('person', $personid);
        $ranking2 = $query->execute();
        $name2 = $ranking2[0]->getName();

        $form = $this->createFormBuilder()
            ->add('name1', 'submit', array('label' => $name1->getName()))
            ->add('name2', 'submit', array('label' => $name2->getName()))
            ->add('name1val', 'hidden', ['data' => $ranking1[0]->getId()])
            ->add('name2val', 'hidden', ['data' => $ranking2[0]->getId()])
            ->getForm()
            ;

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $rankings = $em->getRepository('NameRankBundle:Ranking');
            $ranking1 = $rankings->findById($form->get('name1val')->getData())[0];
            $ranking2 = $rankings->findById($form->get('name2val')->getData())[0];
            if($form->get('name1')->isClicked())
            {
                // Name1 wins
                $rating = new Rating($ranking1->getRank(), $ranking2->getRank(), 1, 0);
            }
            if($form->get('name2')->isClicked())
            {
                // Name 1 lost
                $rating = new Rating($ranking1->getRank(), $ranking2->getRank(), 0, 1);
            }

            $results = $rating->getNewRatings();
            $ranking1->setRank($results['a']);
            $ranking1->incrementNumberOfComparisons();
            $ranking2->setRank($results['b']);
            $ranking2->incrementNumberOfComparisons();
            $em->persist($ranking1);
            $em->persist($ranking2);
            $em->flush();

            return $this->redirectToRoute('name_rank_compare', ['personid' => $personid]);
        }

        return $this->render(
            'NameRankBundle:Default:compare.html.twig',
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

