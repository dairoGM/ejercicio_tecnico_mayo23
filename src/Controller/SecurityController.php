<?php

namespace App\Controller;

use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     * @param Utils $util
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @return Response
     */
    public function home(Utils $util, UserPasswordEncoderInterface $encoder, Request $request): Response
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'secret key';
        $secret_iv = 'secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
//        $password = openssl_decrypt(base64_decode($request->getSession()->get('password')), $encrypt_method, $key, 0, $iv);
        $password = $request->getSession()->get('password');

        $token = $util->getToken($this->getUser()->getUsername(), $password);
        return $this->render('dashboard/home.html.twig', ['token' => $token, 'user' => $this->getUser()->getUsername()]);
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
