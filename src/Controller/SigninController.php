<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SigninType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SigninController extends AbstractController
{
    #[Route('/signin', name: 'app_signin')]
    public function index(EntityManagerInterface $em, 
    Request $req, UserPasswordHasherInterface $passwordHasher, Recaptcha3Validator $recaptcha3Validator): Response
    {

         // Récupérer le token du formulaire
    $recaptchaToken = $req->request->get('g-recaptcha-response');

    // if (!$recaptchaToken) {
    //     throw new \Exception("Token reCAPTCHA manquant");
    // }

    
        $user = new User();

        $form = $this->createForm(SigninType::class, $user);

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // on recuper les données du formulaire Data(couple user + password)
            $user = $form->getData();
            // On met le mor de passe en claire 
            $plaintestPassword = $user->getPassword();
            // Hasher les mot des passe
            $hasherPassword = $passwordHasher->hashPassword(
                // On passe l'user
                $user, 
                // et le plain test passord en claire
                $plaintestPassword
            );

            $user->setPassword($hasherPassword);
            // Et on persiste les données
            $em->persist($user);
            $em->flush();

            // Afficher un message pour dire 'OK' ! avec un return render 
            $this->addFlash('success', 'Le compte a bien été créée');
            // return $this->redirectToRoute('app_home');
        } else {
            // Afficher une erreur 
        }   

        // Vérifie si une réponse existe avant d'appeler getScore()
        $recaptchaResponse = $recaptcha3Validator->getLastResponse();
       // test l'utilisateur est un bot 
        if ($recaptchaResponse && $recaptchaResponse()->getScore() < 0.5) { 
           $msg = "L'utilisateur est un bot"; 
           $notice = "danger"; 
        } else {
            $msg = "Bienvenue !";
            $notice = "success";
        }

        return $this->render('signin/index.html.twig', [
            'form' => $form->createView(),
            'msg' => $msg,
            'danger' => $notice
        ]);
    }
}
