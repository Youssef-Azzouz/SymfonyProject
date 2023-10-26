<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/books', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/book', name: 'book')]
    public function book()
    {

        return $this->render('book/book.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook1', name: 'addBooks')]
    public function addBook1(ManagerRegistry $managerRegistry)
    {
        $book = new Book();
        $book->setRef("154");
        $book->setTitle('Book1');
        $book->setPublished(true);
        //$book->setPublicationDate(2009 - 06 - 21);
        $em = $managerRegistry->getManager();
        $em->persist($book);
        $em->flush();
        return $this->redirectToRoute('book');
    }

    #[Route('/booksList', name: 'Books')]
    public function listBooks(BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findAll();
        return $this->render('book/listBooks.html.twig', array("tabBooks" => $books));
    }

    #[Route('/findBookByAuthor/{id}', name: 'findBookByAuthor')]
    public function listBooksSorted(BookRepository $bookRepository, $id): Response
    {
        $booksByAuthor = $bookRepository->findBookByAuthor($id);
        return $this->render('book/findBookByAuthor.html.twig', array("tabBooksByAuthor" => $booksByAuthor));
    }

    #[Route('/addBook', name: 'addBook')]
    public function addBook(Request $request, ManagerRegistry $managerRegistry)
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('Books');
        }
        return $this->render("book/add.html.twig", array("formulaireBook" => $form->createView()));
    }

    #[Route('/editBook/{id}', name: 'editBook')]
    public function edit(BookRepository $bookRepository, $id, Request $request, ManagerRegistry $managerRegistry)
    {
        $book = $bookRepository->find($id);

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            $em->flush();

            return $this->redirectToRoute('Books');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/deleteBook/{id}', name: 'deleteBook')]
    public function delete(BookRepository $bookRepository, $id, ManagerRegistry $managerRegistry)
    {
        $book = $bookRepository->find($id);
        $em = $managerRegistry->getManager();

        $em->remove($book);
        $em->flush();
        return $this->redirectToRoute('Books');
    }
}
