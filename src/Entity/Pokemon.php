<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'pokemon')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?int $numPokemon = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $img = null;

    /**
     * @var Collection<int, TypePokemon>
     */
    #[ORM\ManyToMany(targetEntity: TypePokemon::class, inversedBy: 'pokemon')]
    private Collection $type;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, mappedBy: 'pokemons')]
    private Collection $teams;

    /**
     * @var Collection<int, LinePokemon>
     */
    #[ORM\OneToMany(targetEntity: LinePokemon::class, mappedBy: 'pokemon')]
    private Collection $linePokemon;

    public function __construct()
    {
        $this->type = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->linePokemon = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getNumPokemon(): ?int
    {
        return $this->numPokemon;
    }

    public function setNumPokemon(int $numPokemon): static
    {
        $this->numPokemon = $numPokemon;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection<int, TypePokemon>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(TypePokemon $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
        }

        return $this;
    }

    public function removeType(TypePokemon $type): static
    {
        $this->type->removeElement($type);

        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->addPokemon($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            $team->removePokemon($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, LinePokemon>
     */
    public function getLinePokemon(): Collection
    {
        return $this->linePokemon;
    }

    public function addLinePokemon(LinePokemon $linePokemon): static
    {
        if (!$this->linePokemon->contains($linePokemon)) {
            $this->linePokemon->add($linePokemon);
            $linePokemon->setPokemon($this);
        }

        return $this;
    }

    public function removeLinePokemon(LinePokemon $linePokemon): static
    {
        if ($this->linePokemon->removeElement($linePokemon)) {
            // set the owning side to null (unless already changed)
            if ($linePokemon->getPokemon() === $this) {
                $linePokemon->setPokemon(null);
            }
        }

        return $this;
    }
}
