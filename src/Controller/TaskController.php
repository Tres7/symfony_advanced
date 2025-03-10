<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Security\Voter\TaskVoter;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/index', name: 'index_task')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $tasks = $entityManager->getRepository(Task::class)->createQueryBuilder('t')->getQuery()->getResult();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/create', name: 'create_task')]
    public function createTask(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the product initially to assign an ID if needed
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tache ajoutée avec succès');

            return $this->redirectToRoute('index_task');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit_task')]
    public function edit(Task $task, int $id, Request $request, EntityManagerInterface $entityManager, TaskService $taskService): Response
    {
        try {
            $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier cette tâche.');
            return $this->redirectToRoute('index_task');
        }
        //verify if the task can be edited
        if (!$taskService->canEdit($task)) {
            $this->addFlash('error', 'Cette tâche ne peut pas être modifiée car elle a été créée il y a plus de 7 jours.');
            return $this->redirectToRoute('index_task');
        }


        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Tâche mise à jour avec succès.');

            return $this->redirectToRoute('index_task');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/view/{id}', name: 'view_task')]
    public function view(Task $task, int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted(TaskVoter::VIEW, $task);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de voir cette tâche.');
            return $this->redirectToRoute('index_task');
        }

        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_task', methods: ['POST'])]
    public function delete(Task $task, int $id, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer cette tâche.');
            return $this->redirectToRoute('index_task');
        }

        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'Tâche supprimée avec succès.');

        return $this->redirectToRoute('index_task');
    }
}
