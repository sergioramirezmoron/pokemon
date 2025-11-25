<?php

namespace App\Controller;

use App\Entity\LinePokemon;
use App\Entity\Pokemon;
use App\Entity\User;
use App\Form\PokemonType;
use App\Repository\LinePokemonRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/pokemon')]
final class PokemonController extends AbstractController
{
    #[Route(name: 'app_pokemon_index', methods: ['GET'])]
    public function index(PokemonRepository $pokemonRepository, Request $request): Response
    {
        if ($name = $request->query->get('name')) {
            $pokemons = $pokemonRepository->searchByName($name);
        } else {
            $pokemons = $pokemonRepository->findAllOrdered();
        }


        return $this->render('pokemon/index.html.twig', [
            'pokemon' => $pokemons,
        ]);
    }

    #[Route('/pokedex', name: 'app_pokemon_pokedex', methods: ['GET'])]
    public function pokedex(PokemonRepository $pokemonRepository, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            $pokemons = [];
        } elseif ($name = $request->query->get('name')) {
            $pokemons = $pokemonRepository->searchByNameInPokedex($name, $user);
        } else {
            // Extraer los Pokemon de los LinePokemon
            $pokemons = [];
            foreach ($user->getLinePokemon() as $linePokemon) {
                $pokemons[] = $linePokemon->getPokemon();
            }
        }

        return $this->render('pokemon/index.html.twig', [
            'pokemon' => $pokemons,
        ]);
    }

    #[Route('/new', name: 'app_pokemon_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pokemon->setCreatedBy($this->getUser());

            $imgFile = $form->get('img')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();

                $imgFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $pokemon->setImg($newFilename);
            } else {
                $pokemon->setImg('default.png');
            }

            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('app_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pokemon/new.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form,
        ]);
    }

    #[Route('catch/{id}', name: 'app_pokemon_catch', methods: ['GET'])]
    public function catch(Pokemon $pokemon, EntityManagerInterface $entityManager, LinePokemonRepository $linePokemonRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Debes iniciar sesión para atrapar Pokémon.');
        }

        $existingLinePokemon = $linePokemonRepository->findOneBy([
            'trainer' => $user,
            'pokemon' => $pokemon,
        ]);

        if ($existingLinePokemon) {
            $this->addFlash('warning', 'Ya tienes este Pokémon capturado.');
            return $this->redirectToRoute('app_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        $linePokemon = new LinePokemon();
        $linePokemon->setPokemon($pokemon);
        $linePokemon->setTrainer($user);
        $linePokemon->setName($pokemon->getName());

        $entityManager->persist($linePokemon);
        $entityManager->flush();
        return $this->redirectToRoute('app_pokemon_pokedex', [], Response::HTTP_SEE_OTHER); 
    }

    #[Route('kill/{id}', name: 'app_pokemon_kill', methods: ['GET'])]
    public function kill(Pokemon $pokemon, EntityManagerInterface $entityManager, LinePokemonRepository $linePokemonRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Debes iniciar sesión para liberar un Pokémon.');
        }

        $linePokemon = $linePokemonRepository->findOneBy([
            'trainer' => $user,
            'pokemon' => $pokemon,
        ]);

        if (!$linePokemon) {
            $this->addFlash('warning', 'No tienes este Pokémon capturado.');
            return $this->redirectToRoute('app_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        $entityManager->remove($linePokemon);
        $entityManager->flush();

        $this->addFlash('success', 'Pokémon liberado con éxito.');
        return $this->redirectToRoute('app_pokemon_pokedex', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_pokemon_show', methods: ['GET'])]
    public function show(Pokemon $pokemon): Response
    {
        return $this->render('pokemon/show.html.twig', [
            'pokemon' => $pokemon,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pokemon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pokemon $pokemon, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgFile = $form->get('img')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();

                $imgFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $pokemon->setImg($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_pokemon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pokemon/edit.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pokemon_delete', methods: ['POST'])]
    public function delete(Request $request, Pokemon $pokemon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pokemon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pokemon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pokemon_index', [], Response::HTTP_SEE_OTHER);
    }
}
