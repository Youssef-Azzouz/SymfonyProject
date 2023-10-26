<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showauthor/{var}', name: 'show_author')]
    public function show($var): Response
    {
        return $this->render('author/show.html.twig', array('nameAuthor' => $var));
    }

    #[Route('/listauthor', name: 'list_author')]
    public function list(): Response
    {
        $authors = array(

            array('id' => 1, 'username' => ' Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),

            array('id' => 2, 'username' => 'William Shakespeare', 'email' =>

            'william.shakespeare@gmail.com', 'nb_books' => 200),

            array('id' => 3, 'username' => ' Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),

        );
        return $this->render('author/list.html.twig', array("tabAuthors" => $authors));
    }

    #[Route('/showmore/{id}', name: 'show_more')]
    public function showmore($id)
    {
        $authors = array(
            array('id' => 1, 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100),
            array('id' => 2, 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200),
            array('id' => 3, 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
        );
        $author = null;
        foreach ($authors as $a) {
            if ($a['id'] == $id) {
                $author = $a;
                break;
            }
        }

        return $this->render('author/showmore.html.twig', [
            'author' => $author,
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/authorsList', name: 'Authors')]
    public function listAuthors(AuthorRepository $authorRepository): Response
    {
        $authorsSorted = $authorRepository->sortAuthorsByEmail();
        $authors = $authorRepository->findAll();
        return $this->render('author/listAuthors.html.twig', [
            "tabAuthors" => $authors,
            "tabSortedAuthors" => $authorsSorted
        ]);
    }


    #[Route('/add', name: 'addAuthors')]
    public function add(ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $author->setUsername('Mariem');
        $author->setEmail('maram@gmail.com');
        $em = $managerRegistry->getManager();
        $em->persist($author);
        $em->flush();
        return $this->redirectToRoute('Authors');
    }

    #[Route('/edit/{id}', name: 'editAuthor')]
    public function edit(AuthorRepository $authorRepository, $id, ManagerRegistry $managerRegistry)
    {
        $author = $authorRepository->find($id);
        $author->setUsername('Rami');
        $author->setEmail('Rami@gmail.com');
        $em = $managerRegistry->getManager();
        $em->flush();
        return $this->redirectToRoute('Authors');
    }

    #[Route('/delete/{id}', name: 'deleteAuthor')]
    public function delete(AuthorRepository $authorRepository, $id, ManagerRegistry $managerRegistry)
    {
        $author = $authorRepository->find($id);
        $em = $managerRegistry->getManager();

        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('Authors');
    }

    #[Route('/addAuthor', name: 'addAuthor')]
    public function addAuthor(Request $request, ManagerRegistry $managerRegistry)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $em->persist($author);
            $em->flush();
            return new Response("done");
        }
        return $this->render("author/add.html.twig", array("formulaireAuthor" => $form->createView()));
    }
}
