<?php

namespace NameRankBundle\Controller;

use NameRankBundle\Entity\Name;
use NameRankBundle\Form\NameType;
use Rating\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $name = new Name();
        $form = $this->createForm(new NameType(), $name);

        $form->handleRequest($request);

        if ($form->isValid()) {
            dump($name);

            // save
            $em->persist($name);

            $em->flush();

            return $this->redirectToRoute('name_rank_new');
        }

        return $this->render(
            'NameRankBundle:Default:newname.html.twig',
            [
                'newform' => $form->createView()
            ]
        );
    }

    public function listNamesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $names = $em->getRepository('NameRankBundle:Name')->findBy(array(),array('rank' => 'DESC'));

        return $this->render(
            'NameRankBundle:Default:names.html.twig',
            [
                'names' => $names
            ]
        );

    }

    public function compareNamesAction(Request $request)
    {
        $ismale = rand(0,1);

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT n, (RAND() * (n.numberOfComparisons + 1)) as HIDDEN randcomp FROM NameRankBundle\Entity\Name n WHERE n.is_male = :ismale ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('ismale', $ismale);
        $name1 = $query->execute();
        //$name1 = $em->getRepository('NameRankBundle:Name')->findOneBy(array(),array('rank' => 'DESC'));
        $query = $em->createQuery('SELECT n, (RAND() * (n.numberOfComparisons + 1)) as HIDDEN randcomp FROM NameRankBundle\Entity\Name n WHERE  n.is_male = :ismale AND n.id != :id ORDER BY randcomp');
        $query->setMaxResults(1);
        $query->setParameter('id', $name1[0]->getId());
        $query->setParameter('ismale', $ismale);
        $name2 = $query->execute();

        $form = $this->createFormBuilder()
            ->add('name1', 'submit', array('label' => $name1[0]->getName()))
            ->add('name2', 'submit', array('label' => $name2[0]->getName()))
            ->add('name1val', 'hidden', ['data' => $name1[0]->getId()])
            ->add('name2val', 'hidden', ['data' => $name2[0]->getId()])
            ->getForm()
            ;

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $names = $em->getRepository('NameRankBundle:Name');
            $name1 = $names->findById($form->get('name1val')->getData())[0];
            $name2 = $names->findById($form->get('name2val')->getData())[0];
            if($form->get('name1')->isClicked())
            {
                // Name1 wins
                $rating = new Rating($name1->getRank(), $name2->getRank(), 1, 0);
            }
            if($form->get('name2')->isClicked())
            {
                // Name 1 lost
                $rating = new Rating($name1->getRank(), $name2->getRank(), 0, 1);
            }

            $results = $rating->getNewRatings();
            $name1->setRank($results['a']);
            $name1->incrementNumberOfComparisons();
            $name2->setRank($results['b']);
            $name2->incrementNumberOfComparisons();
            $em->persist($name1);
            $em->persist($name2);
            $em->flush();

            return $this->redirectToRoute('name_rank_compare');
        }

        return $this->render(
            'NameRankBundle:Default:compare.html.twig',
            [
                'name1' => $name1[0],
                'name2' => $name2[0],
                'form' => $form->createView(),
            ]
        );
    }
}

