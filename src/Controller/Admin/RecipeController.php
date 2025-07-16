<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Enum\RecipeRegime;

#[Route('/admin/recettes', name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $manager): Response
    {
        $recipes = $repository->findWithDurationLowerThan($duration = 60);

       return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'Recette créée avec succès !');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'Recette mise à jour avec succès !');
            return $this->redirectToRoute('admin.recipe.index');
        }
         
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe'    => $recipe,
            'form'      => $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete',  methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'Recette supprimée avec succès !');
        return $this->redirectToRoute('admin.recipe.index');
    }
}
