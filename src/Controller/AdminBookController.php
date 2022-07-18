<?php


namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBookController extends AbstractController
{
    /**
     * @Route("/Admin/book-list", name="admin_book_list")
     */
    public function bookList(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();

        return $this->render('Admin/books.html.twig', ['books' => $books]);
    }

    /**
     * @Route("/Admin/book/{id}", name="admin_book")
     */
    public function showBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        return $this->render('Admin/book.html.twig', ['book' => $book]);
    }

    /**
     * @Route("/Admin/insert-book", name="admin_insert_book")
     */
    public function insertBook(EntityManagerInterface $entityManager, Request $request)
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Livre enregistré');
        }
        return $this->render("Admin/book.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/Admin/book/delete/{id}", name="admin_delete_book")
     */
    public function deleteBook($id, BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $book = $bookRepository->find($id);

        if (is_null($book)){
            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été supprimé');
        } else {
            $this->addFlash('error', 'Element introuvable');
        }
        return $this->redirectToRoute('admin_book_list');
    }

    /**
     * @Route("Admin/book/update/{id}", name="admin_update_book")
     */
    public function updateBook($id, BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $book = $bookRepository->find($id);

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été enregistré');
        }
        return $this->render("Admin/insert_book.html.twig", ['form' => $form->createView()]);
    }
}