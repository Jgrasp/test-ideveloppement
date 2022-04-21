<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo/list/{list}/task')]
class TaskController extends AbstractController
{
    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(TodoList $list, Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $task->setList($list);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);

            return $this->redirectToTodoList($task->getList());
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'list' => $list
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task);

            return $this->redirectToTodoList($task->getList());
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/done', name: 'app_task_toggle_done', methods: ['PUT'])]
    public function toggleDone(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('toggleDone'.$task->getId(), $request->request->get('_token'))) {
            $task->setDone(!$task->getDone());
            $taskRepository->add($task);
        }

        return $this->redirectToTodoList($task->getList());
    }

    private function redirectToTodoList(TodoList $list): RedirectResponse
    {
        return $this->redirectToRoute('app_todo_list_show', ['id' => $list->getId()], Response::HTTP_SEE_OTHER);
    }
}
