<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Utils;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController1 extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request, 
        UserRepository $repo,
        UserPasswordHasherInterface $hash, Utils $utils, 
        EntityManagerInterface $em, 
        Recaptcha3Validator $recaptcha3Validator): Response
    {
        
        //variable qui stocke un nouvel utilisateur
        $user = new User();
        //variable qui stocke le formulaire
        $form = $this->createForm(UserType::class, $user);
        //récupérer le contenu du formulaire
        $form->handleRequest($request);
        //tester si le fomulaire est submit
        if($form->isSubmitted()){
            //récupération et nettoyage du mail
            $user->setEmail($utils->cleanInput($_POST['user']['email']));
            //récupérer l'utilisateur
            $recup = $repo->findBy(['email'=>$user->getEmail()]);
            //test des doublons
            if($recup == null){
                //variable pour récupérer le mot de passe en clair et le nettoyer
                $pass = $utils->cleanInput($_POST['user']['password']);
                //nettoyage des valeurs (nom et prénom)
                $user->setNom($utils->cleanInput($_POST['user']['nom']));
                $user->setPrenom($utils->cleanInput($_POST['user']['prenom']));
                //hash du mot de passe
                $pass = $hash->hashPassword($user, $pass);
                //set du password hash
                $user->setPassword($pass);
                //set du role
                $user->setRoles(['ROLE_USER']);
                //set l'activation
                // $user->setActivated(false);
                //persist les données
                $em->persist($user);
                //sauvegarder en BDD
                $em->flush();


            }
            
            //test l'utilisateur est un bot 
            if ($recaptcha3Validator->getLastResponse()->getScore() < 0.5) { 
                $msg = "L'utilisateur est un bot"; 
                $notice = "danger"; 
            } 
            return $this->render('user/index.html.twig', [
                'controller_name' => 'UserController',
                'msg' => $msg,
                'notice' => $notice
            ]);
        }
    } 

}