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
     * @Route("/actus/creation", name="createActu")
     */
    public function createActuAction(Request $request)
    {

        //nouvelle instance
        $newActu = new Actu();

        //crée une instance de formulaire, associée à notre entité
        $actuForm = $this->createForm(new ActuType(), $newActu);

        //injecte les données du formulaire dans notre entité
        $actuForm->handleRequest($request);

        //si le form est soumis et valide...
        if ($actuForm->isValid()){

            dump($newActu);
            die();

            //hydratation
            $newActu->setTitle( "bla test" );
            $newActu->setContent( "content content lorem ipsum dolor sit amet." );
            $newActu->setExcerpt( "excerpt ipsum dolor sit amet." );
            $newActu->setIsPublished( true );
            $newActu->setDateCreated( new DateTime() );
            $newActu->setDateModified( $newActu->getDateCreated() );
            $newActu->setDatePublished( $newActu->getDateCreated() );

            //doctrine sauvegarde l'entité
            //dump( $newActu );
            $em = $this->getDoctrine()->getManager();
            $em->persist( $newActu );
            $em->flush();
        }

        $params = array(
            "actuForm" => $actuForm->createView()
        );
        
        return $this->render('actu/create_actu.html.twig', $params);
    }


    /**
     * @Route("/actus/modification/{id}", name="editActu")
     */
    public function editActuAction($id)
    {
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $actu = $actuRepo->find( $id );

        if (!$actu){
            $this->addFlash("error", "Actu inexistante !");
            return $this->redirectToRoute("index");
        }

        $actu->setTitle("yoyoyoyo");
        $actu->setDateModified(new DateTime());

        $em = $this->getDoctrine()->getManager();
        //pas besoin de persist(), Doctrine connaissant déjà cette entité
        $em->flush();

        return $this->render('actu/edit_actu.html.twig');
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
            return $this->redirectToRoute("index");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove( $actu );
        $em->flush();

        $this->addFlash("success", "L'actu a bien été effacée !");
        return $this->redirectToRoute("index");
    }
}


