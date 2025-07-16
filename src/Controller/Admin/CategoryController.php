<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;


#[Route ('/admin/category', name: 'admin.category.')]
class CategoryController extends AbstractController {
    
    #[Route (name: 'index')]
    public function index(CategoryRepository $repository)
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repository->findAll()
        ]);
    }

    #[Route ('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $category->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);
            $manager->flush();
            $this->addFlash('success', 'Catégorie créée avec succès !');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route ('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Category $category, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $manager->flush();
            $this->addFlash('success', 'Catégorie mise à jour avec succès !');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category'  => $category,
            'form'      => $form
        ]);
    }

    #[Route ('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove( Request $request, Category $category, EntityManagerInterface $manager): Response
    {
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success', 'Catégorysupprimée avec succès !');
        return $this->redirectToRoute('admin.recipe.index');
    }

}