<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('tasks_list');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
    #[Route('/update/image', name: 'upload_image')]
    public function updateProfileImage(Request $request)
    {
        if ($request->files->get("image")) {
            $image = $request->files->get("image");
            $image_name = $image->getClientOriginalName();
            $image->move($this->getParameter("image_directory"), $image_name);
            $user = $this->getUser();
            $user->setImage($image_name);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre Image est modifier avec succes!');

            return $this->redirectToRoute('app_profile');
            
        } else {
            return $this->redirectToRoute('app_profile');
        }


    }
}