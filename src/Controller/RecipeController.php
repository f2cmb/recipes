<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Enum\RecipeRegime;

final class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $manager): Response
    {
        $recipes = $repository->findWithDurationLowerThan($duration = 40);

        // $recipe =new Recipe();
        // $recipe->setTitle('Cerveau humain')
        //     ->setContent('Uniquement pour les zombies, tâcher de surprendre un humaine car vous êtes un zombie et que nous n\avancez pas très vite et êtes con comme une chaise')
        //     ->setDuration(12)
        //     ->setCreatedAt(new \DateTimeImmutable())
        //     ->setUpdatedAt(new \DateTimeImmutable())
        //     ->setSlug('barbe-a-papa')
        //     ->setRegime(RecipeRegime::OMNI);

        //     $manager->persist($recipe);
        //     $manager->flush();

       return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9\-]+', 'id' => '\d+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', [
                'slug' => $recipe->getSlug(),
                'id' => $recipe->getId()
            ]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'Recette mise à jour avec succès !');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/recettes/create', name: 'recipe.create', methods: ['GET', 'POST'])]
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
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/recettes/{id}/delete', name: 'recipe.delete',  methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'Recette supprimée avec succès !');
        return $this->redirectToRoute('recipe.index');
    }
}
