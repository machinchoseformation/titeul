<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Actu;
use AppBundle\Form\ActuType;
use \DateTime;

class ActuController extends Controller
{
    
    /**
     * @Route("/actus/{page}", defaults={"page" = 1}, name="showAllActus")
     */
    public function showAllActusAction($page)
    {

        $numPerPage = 2;
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $lastActus = $actuRepo->findPagedActus($page, $numPerPage);

        $totalActus     = count($lastActus);
        $firstShowing   = $numPerPage * ($page - 1) + 1;
        $lastShowing    = $firstShowing + $numPerPage;
        if ($lastShowing > $totalActus){
            $lastShowing = $totalActus;
        }
        $hasPrevPage    = ($page > 1) ? true : false;
        $lastPage       = ceil($totalActus / $numPerPage);
        $hasNextPage    = ($page < $lastPage) ? true : false;

        $minNumLink     = ($page-5 < 1) ? 1 : $page-5;
        $maxNumLink     = ($page+5 > $lastPage) ? $lastPage : $page+5;

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
     * @Route("/actus/creation", name="createActu")
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

            //doctrine sauvegarde l'entité
            $em = $this->getDoctrine()->getManager();
            try {
                $em->persist( $newActu );
                $em->flush();
                $this->addFlash("success", "Actualité sauvegardée !");
            }
            catch(\Exception $e){
                $this->addFlash("error", "Un problème est survenu !");
            }
            //redirect

        }

        $params = array(
            "actuForm" => $actuForm->createView()
        );
        
        return $this->render('actu/create_actu.html.twig', $params);
    }


    /**
     * @Route("/actus/modification/{id}", name="editActu")
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
     * @Route("/actus/suppression/{id}", name="deleteActu")
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
}


