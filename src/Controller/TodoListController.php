<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo/list')]
class TodoListController extends AbstractController
{
    #[Route('/', name: 'app_todo_list_index', methods: ['GET'])]
    public function index(TodoListRepository $todoListRepository): Response
    {
        return $this->render('todo_list/index.html.twig', [
            'todoLists' => $todoListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_todo_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TodoListRepository $todoListRepository): Response
    {
        $todoList = new TodoList();
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoListRepository->add($todoList);
            return $this->redirectToRoute('app_todo_list_show', ['id' => $todoList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo_list/new.html.twig', [
            'todoList' => $todoList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todo_list_show', methods: ['GET'])]
    public function show(TodoList $todoList): Response
    {
        $form = $this->createForm(TodoListType::class, $todoList, ['action' => $this->generateUrl('app_todo_list_edit', ['id' => $todoList->getId()])]);

        return $this->renderForm('todo_list/show.html.twig', [
            'todoList' => $todoList,
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'app_todo_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TodoList $todoList, TodoListRepository $todoListRepository): Response
    {
        $form = $this->createForm(TodoListType::class, $todoList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoListRepository->add($todoList);
            return $this->redirectToRoute('app_todo_list_show', ['id' => $todoList->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('todo_list/edit.html.twig', [
            'todoList' => $todoList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todo_list_delete', methods: ['POST'])]
    public function delete(Request $request, TodoList $todoList, TodoListRepository $todoListRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todoList->getId(), $request->request->get('_token'))) {
            $todoListRepository->remove($todoList);
        }

        return $this->redirectToRoute('app_todo_list_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/task/done', name: 'app_todo_list_delete_task_done', methods: ['DELETE'])]
    public function deleteDoneTasks(Request $request, TodoList $todoList, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('deleteDoneTasks', $request->request->get('_token'))) {
            $taskRepository->removeDoneByList($todoList);
        }

        return $this->redirectToRoute('app_todo_list_show', ['id' => $todoList->getId()], Response::HTTP_SEE_OTHER);
    }
}
