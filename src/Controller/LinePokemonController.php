<?php

namespace App\Controller;

use App\Entity\LinePokemon;
use App\Form\LinePokemonType;
use App\Repository\LinePokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/line/pokemon')]
final class LinePokemonController extends AbstractController
{
    #[Route(name: 'app_line_pokemon_index', methods: ['GET'])]
    public function index(LinePokemonRepository $linePokemonRepository): Response
    {
        return $this->render('line_pokemon/index.html.twig', [
            'line_pokemons' => $linePokemonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_line_pokemon_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $linePokemon = new LinePokemon();
        $form = $this->createForm(LinePokemonType::class, $linePokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($linePokemon);
            $entityManager->flush();

            return $this->redirectToRoute('app_line_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('line_pokemon/new.html.twig', [
            'line_pokemon' => $linePokemon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_line_pokemon_show', methods: ['GET'])]
    public function show(LinePokemon $linePokemon): Response
    {
        return $this->render('line_pokemon/show.html.twig', [
            'line_pokemon' => $linePokemon,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_line_pokemon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LinePokemon $linePokemon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LinePokemonType::class, $linePokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_line_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('line_pokemon/edit.html.twig', [
            'line_pokemon' => $linePokemon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_line_pokemon_delete', methods: ['POST'])]
    public function delete(Request $request, LinePokemon $linePokemon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$linePokemon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($linePokemon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_line_pokemon_index', [], Response::HTTP_SEE_OTHER);
    }
}
