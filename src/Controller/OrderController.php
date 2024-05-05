<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'order_list', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/order-list.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/create-order', name: 'create_order', methods: ['GET', 'POST'])]
    public function CreateOrder(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('order_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/create-order.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'display_order', methods: ['GET'])]
    public function DisplayOrder(Order $order): Response
    {
        return $this->render('order/display-order.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit-order', name: 'edit_order', methods: ['GET', 'POST'])]
    public function EditOrder(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('order_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/edit-order.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete_order', methods: ['POST'])]
    public function DeleteOrder(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($order);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order_list', [], Response::HTTP_SEE_OTHER);
    }
}
