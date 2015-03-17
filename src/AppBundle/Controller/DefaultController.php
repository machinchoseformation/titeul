<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $actuRepo = $this->getDoctrine()->getRepository("AppBundle:Actu");
        $lastActus = $actuRepo->findHomeActus();

        $params = array(
            "lastActus" => $lastActus
        );

        return $this->render('default/index.html.twig', $params);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function aboutAction()
    {
        return $this->render('default/about.html.twig');
    }


    /**
     * @Route("/cgv", name="legal")
     */
    public function legalAction()
    {
        return $this->render('default/legal.html.twig');
    }



}
