<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Form\FormError;

use AppBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\Form\ForgotPasswordType;

class UserController extends Controller
{


	/**
	 * @Route("/mot-de-passe-oublie", name="forgotPassword")
	 */
	public function forgotPasswordAction(Request $request){

		$user = new User();
		$forgotPasswordForm = $this->createForm(new ForgotPasswordType, $user);

		$forgotPasswordForm->handleRequest($request);

		if ($forgotPasswordForm->isValid()){

			$userRepo = $this->getDoctrine()->getRepository("AppBundle:User");
			$foundUser = $userRepo->findOneByEmail( $user->getEmail() );

			if (!$foundUser){
				$forgotPasswordForm->get('email')
					->addError(new FormError('Email non trouvé'));
			}
			else {

				$mailer = $this->get('mailer');
			    $message = $mailer->createMessage()
			        ->setSubject('Mot de passe oublié ?')
			        ->setFrom('asdf@asdf.com')
			        ->setTo('gsylvestre@gmail.com')
			        ->setBody(
			            $this->renderView(
			                // app/Resources/views/Emails/registration.html.twig
			                'emails/forgot_password.html.twig',
			                array('foundUser' => $foundUser)
			            ),
			            'text/html'
			        );
		 		$mailer->send($message);				
			}
	 	}

	 	$params = array(
	 		"forgotPasswordForm" => $forgotPasswordForm->createView()
	 	);

 		return $this->render("user/forgot_password.html.twig", $params);

	}

   /**
     * @Route("/nouveau-mot-de-passe/{email}/{token}", name="resetPassword")
     */
    public function resetPasswordAction(Request $request, $email, $token){
    	die("to do");
    }



    /**
     * @Route("/inscription", name="register")
     */
    public function registerAction(Request $request){

    	$user = new User();
    	$registerForm = $this->createForm(new RegisterType(), $user);

    	$registerForm->handleRequest($request);

    	if ($registerForm->isValid()){

    		$generator = new SecureRandom();
			$salt = bin2hex($generator->nextBytes(30));
			$token = bin2hex($generator->nextBytes(20));

			$user->setSalt($salt);
			$user->setToken($token);

    		$user->setRoles( array("ROLE_USER") );

    		$encoder = $this->get('security.password_encoder');
    		$encoded = $encoder->encodePassword($user, $user->getPlainPassword());

    		$user->setPassword($encoded);

    		$user->setDateRegistered( new \DateTime() );
    		$user->setDateModified( new \DateTime() );

    		try {
	    		$em = $this->getDoctrine()->getManager();
	    		$em->persist( $user );
	    		$em->flush();

	    		return $this->redirectToRoute("login_route");
	    	}
	    	catch(\Exception $e){
	    		$this->addFlash("error", "Un problème est survenu");
	    	}
    		dump($user);
    	}

    	$params = array(
    		"registerForm" => $registerForm->createView()
    	);
    	return $this->render("user/register.html.twig", $params);
    }



    /**
     * @Route("/connexion", name="login")
     */
    public function loginAction(Request $request)
    {
    	$authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render(
	        'user/login.html.twig',
	        array(
	            // last username entered by the user
	            'last_username' => $lastUsername,
	            'error'         => $error,
	        )
	    );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction(){}

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutCheckAction(){}
}








