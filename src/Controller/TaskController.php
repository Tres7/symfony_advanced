<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Security\Voter\TaskVoter;
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
    public function edit(Task $task, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
//        $task = $entityManager->getRepository(Task::class)->find($id);
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);


        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt(new \DateTimeImmutable());
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
    public function view(Task $task,int $id, EntityManagerInterface $entityManager): Response
    {
//        $task = $entityManager->getRepository(Task::class)->find($id);
        $this->denyAccessUnlessGranted(TaskVoter::VIEW, $task);


        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_task', methods: ['POST'])]
    public function delete(Task $task,int $id, EntityManagerInterface $entityManager): Response
    {
//        $task = $entityManager->getRepository(Task::class)->find($id);

        $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task);
        if (!$task) {
            throw $this->createNotFoundException('La tâche demandée n\'existe pas.');
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'Tâche supprimée avec succès.');

        return $this->redirectToRoute('index_task');
    }

}
