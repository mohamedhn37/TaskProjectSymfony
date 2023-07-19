<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $passwordEncoder;
    public function __construct(UserRepository $userRepository , EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder){
        $this-> userRepository = $userRepository;
        $this-> entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/register', name: 'app_register')]
    public function registerUser(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('tasks_list');
        }
        
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form-> isSubmitted() && $form-> isValid()){
            $user = $form->getData();
            $encodedPassword = $this->passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Votre User est ajoutée avec succes!'
            );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', ['form' => $form,]);
        // return $this->render('register/index.html.twig', [
        //     'controller_name' => 'RegisterController',
        // ]);
    }
}
