<?php

namespace App\Controller;

use App\Entity\TypePokemon;
use App\Form\TypePokemonType;
use App\Repository\TypePokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/type')]
final class TypePokemonController extends AbstractController
{
    #[Route(name: 'app_type_pokemon_index', methods: ['GET'])]
    public function index(TypePokemonRepository $typePokemonRepository): Response
    {
        return $this->render('type_pokemon/index.html.twig', [
            'type_pokemons' => $typePokemonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_pokemon_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typePokemon = new TypePokemon();
        $form = $this->createForm(TypePokemonType::class, $typePokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typePokemon);
            $entityManager->flush();

            return $this->redirectToRoute('app_type_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_pokemon/new.html.twig', [
            'type_pokemon' => $typePokemon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_pokemon_show', methods: ['GET'])]
    public function show(TypePokemon $typePokemon): Response
    {
        return $this->render('type_pokemon/show.html.twig', [
            'type_pokemon' => $typePokemon,
            'pokemons' => $typePokemon->getPokemon(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_pokemon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypePokemon $typePokemon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypePokemonType::class, $typePokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_type_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_pokemon/edit.html.twig', [
            'type_pokemon' => $typePokemon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_pokemon_delete', methods: ['POST'])]
    public function delete(Request $request, TypePokemon $typePokemon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typePokemon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($typePokemon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_type_pokemon_index', [], Response::HTTP_SEE_OTHER);
    }
}
