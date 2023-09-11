<?php

namespace App\Controller;

use App\Entity\Tasks;
use App\Form\TasksType;
use App\Repository\TasksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TasksController extends AbstractController
{
    #[Route('/task', name: 'app_tasks_index', methods: ['GET'])]
    public function index(TasksRepository $tasksRepository, Request $request): Response
    {
        $user = $this->getUser();
        if ($user) {
            $tasks = $tasksRepository->findBy(['user' => $user]);
        } else {
            if ($request->headers->get('accept') === 'application/json') {
                return new JsonResponse(['error' => 'You are not authorized to delete this task.'], 403);
            } else {
                return $this->redirectToRoute('app_login');
            } 
        }
        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks ?? [],
        ]);
    }

    #[Route('/new', name: 'app_tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($request->headers->get('accept') === 'application/json') {
                return new JsonResponse(['error' => 'Authentication required'], 401);
            } else {
                return $this->redirectToRoute('app_login');
            }
        }

        $task = new Tasks();
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tasks/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tasks_show', methods: ['GET'])]
    public function show(Tasks $task, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($task->getUser() !== $user) {
            return new JsonResponse(['error' => 'You are not authorized to view this task.'], 403);
        }

        return $this->render('tasks/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tasks_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TasksType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                return $this->redirectToRoute('app_login');
            }
            if ($task->getUser() !== $user) {
                return new JsonResponse(['error' => 'You are not authorized to update this task.'], 403);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_tasks_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tasks/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tasks_delete', methods: ['POST'])]
    public function delete(Request $request, Tasks $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();
            if ($user !== $task->getUser()) {
                return new JsonResponse(['error' => 'You are not authorized to delete this task.'], 403);
            }
            $entityManager->remove($task);
            $entityManager->flush();
        } else {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 400);
        }
        
        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
