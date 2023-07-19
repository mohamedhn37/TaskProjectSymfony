<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;


class TaskController extends AbstractController
{
    private $taskRepository;
    private $entityManager;
    public function __construct(TaskRepository $taskRepository , EntityManagerInterface $entityManager){
        $this-> taskRepository = $taskRepository;
        $this-> entityManager = $entityManager;
    }

    #[Route('/', name: 'tasks_list')]
    public function index()
    {
        $user = $this->getUser();
        $tasks = [];
        if ($user) {
            $tasks = $this->taskRepository->findBy(['user' => $user]);
        }
        
        return $this->render('tasks.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/{id}', name: 'task_show')]

    public function showTask(int $id)
    {
        $task = $this->taskRepository->find($id);
        return $this->render('show.html.twig', [
            'task' => $task,
        ]);

    }

    #[Route('/create/task', name: 'task_create')]
    public function createTask(request $request)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form-> isSubmitted() && $form-> isValid()){

            $task = $form->getData();
            $user = $this->getUser();
            $task->setUser($user);
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Votre Task est ajoutée avec succes!'
            );
            return $this->redirectToRoute('tasks_list');
        }

        return $this->render('add.html.twig', ['form' => $form,]);

    }

    #[Route('/edit/task/{id}', name: 'task_edit')]
    public function editTask(Task $task,request $request)
   {
       $form = $this->createForm(TaskType::class, $task);

       $form->handleRequest($request);

       if ($form-> isSubmitted() && $form-> isValid()){

           $task = $form->getData();
           $this->entityManager->persist($task);
           $this->entityManager->flush();
           $this->addFlash(
            'success',
            'Votre Task est Modifier avec succes!'
        );
           return $this->redirectToRoute('tasks_list');
       }

       return $this->render('edit.html.twig', ['form' => $form,]);

   }

    #[Route('/delete/task/{id}', name: 'task_delete')]
    public function deleteTask(Task $task)
   {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Votre Task est Supprimer avec succes!'
        );
        return $this->redirectToRoute('tasks_list');

   }
}