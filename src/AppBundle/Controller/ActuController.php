<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Actu;
use AppBundle\Entity\Comment;
use AppBundle\Form\ActuType;
use AppBundle\Form\CommentType;
use \DateTime;

use Cocur\Slugify\Slugify;

class ActuController extends Controller
{
    
    /**
     * @Route("/admin/actus/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="showAllActus")
     */
    public function showAllActusAction($page)
    {

        $numPerPage = 2;
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $lastActus = $actuRepo->findPagedActus($page, $numPerPage);

        $actuPaginator = $this->get('actu_paginator');
        $actuPaginator->setDoctrinePaginator($lastActus);
        $data = $actuPaginator->getPaginationData($numPerPage, $page);
        extract($data);

        $params = array(
            "lastActus"     => $lastActus,
            "page"          => $page, 
            "totalActus"    => $totalActus,
            "firstShowing"  => $firstShowing,
            "lastShowing"   => $lastShowing,
            "hasNextPage"   => $hasNextPage,
            "hasPrevPage"   => $hasPrevPage,
            "lastPage"      => $lastPage,
            "minNumLink"    => $minNumLink,
            "maxNumLink"    => $maxNumLink
        );

        return $this->render('actu/show_all.html.twig', $params);
    }


    /**
     * @Route("/actu/details/{slug}", requirements={"slug"="^.+-\d{2}-\d{2}-\d{2}(-[0-9a-f]{13})?$"}, name="actuDetails")
     */
    public function actuDetailsAction(Request $request, $slug)
    {
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $actu = $actuRepo->findBySlugWithComments($slug);


        //crée un commentaire pour cette actualité
        $newComment = new Comment();
        $commentForm = $this->createForm(new CommentType, $newComment);

        $commentForm->handleRequest($request);

        if ($commentForm->isValid()){
            $this->denyAccessUnlessGranted('ROLE_USER', null, 'Vous devez être connecté!');
            $newComment->setDateCreated( new DateTime() );
            $newComment->setAuthor( $this->getUser() );

            //crée la relation !
            $newComment->setActu( $actu );

            //sauvegarde le comment
            $em = $this->getDoctrine()->getManager();
            $em->persist( $newComment );
            $em->flush();

            return $this->redirectToRoute("actuDetails", array("slug" => $slug));
        }

        
        if (!$actu){
            throw $this->createNotFoundException("Cet article n'existe pas.");
        }

        $params = array(
            "actu" => $actu,
            "commentForm" => $commentForm->createView()
        );

        return $this->render('actu/actu_details.html.twig', $params);
    }


    /**
     * @Route("/admin/actus/creation", name="createActu")
     */
    public function createActuAction(Request $request)
    {

        //nouvelle instance
        $newActu = new Actu();
        $newActu->setIsPublished(false); //valeur par défaut

        //crée une instance de formulaire, associée à notre entité
        $actuForm = $this->createForm(new ActuType(), $newActu);

        //injecte les données du formulaire dans notre entité
        $actuForm->handleRequest($request);

        //si le form est soumis et valide...
        if ($actuForm->isValid()){

            //l'hydratation des champs manquants se passe dans l'entité
            $slug = str_replace(" ", "-", $newActu->getTitle());
            $slug = strtolower($slug);
            $slug .= date("-d-m-y");
            $newActu->setSlug($slug);

            //recherche ce slug en base
            $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
            $foundSlugActu = $actuRepo->findOneBySlug($newActu->getSlug());
            if ($foundSlugActu){
                $newActu->setSlug( $newActu->getSlug() . "-" . uniqid() );
            }

            //auteur
            $newActu->setAuthor($this->getUser());


            //doctrine sauvegarde l'entité
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist( $newActu );
                $em->flush();
                $this->addFlash("success", "Actualité sauvegardée !");
            }
            catch(\Exception $e){
                $this->addFlash("error", "Un problème est survenu !" . $e->getMessage());
            }
            //redirect

        }

        $params = array(
            "actuForm" => $actuForm->createView()
        );
        
        return $this->render('actu/create_actu.html.twig', $params);
    }


    /**
     * @Route("/admin/actus/modification/{id}", name="editActu")
     */
    public function editActuAction(Request $request, $id)
    {
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $actu = $actuRepo->find( $id );

        if (!$actu){
            $this->addFlash("error", "Actu inexistante !");
            return $this->redirectToRoute("showAllActus");
        }

        //crée une instance de formulaire, associée à notre entité
        $actuForm = $this->createForm(new ActuType(), $actu);

        //injecte les données du formulaire dans notre entité
        $actuForm->handleRequest($request);

        //si le form est soumis et valide...
        if ($actuForm->isValid()){

            //l'hydratation des champs manquants se passe dans l'entité

            //doctrine sauvegarde l'entité
            $em = $this->getDoctrine()->getManager();
            try {

                $em->flush();
                $this->addFlash("success", "Actualité sauvegardée !");
            }
            catch(\Exception $e){
                dump($e);
                $this->addFlash("error", "Un problème est survenu !");
            }
            //redirect

        }

        $params = array(
            "actuForm" => $actuForm->createView()
        );
        
        return $this->render('actu/edit_actu.html.twig', $params);
    }

    /**
     * @Route("/admin/actus/suppression/{id}", name="deleteActu")
     */
    public function deleteActuAction($id)
    {
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $actu = $actuRepo->find( $id );

        if (!$actu){
            $this->addFlash("error", "Actu inexistante !");
            return $this->redirectToRoute("showAllActus");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove( $actu );
        $em->flush();

        $this->addFlash("success", "L'actu a bien été effacée !");
        return $this->redirectToRoute("showAllActus");
    }


    /**
     * Appelée depuis twig !!
     * @Route("/actus/compte", name="countAllActus")
    */
    public function countAllActusAction(){
        
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $count = $actuRepo->countPublishedActus();

        $params = array(
            "publishedActusCount" => $count
        );
        return $this->render('actu/count_all_actus.html.twig', $params);
    }

}