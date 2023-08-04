<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticlesRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
#[ApiResource(
        operations: [
            new Get(normalizationContext: ['groups' => 'articles:item']),
            new GetCollection(normalizationContext: ['groups' => 'articles:list'])
        ],
        order: ['date_add' => 'DESC'],
        paginationEnabled: true,
        paginationItemsPerPage: 25,
    )]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['articles:list', 'articles:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['articles:list', 'articles:item'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['articles:list', 'articles:item'])]
    private ?string $short_description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['articles:item'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['articles:list', 'articles:item'])]
    private ?\DateTimeInterface $date_add = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['articles:item'])]
    private ?Team $fk_team = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\OneToMany(mappedBy: 'fk_article', targetEntity: Comments::class, orphanRemoval: true)]
    #[Groups(['articles:item'])]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['articles:item', 'categories:item', 'categories:list'])]
    private ?Categories $fk_category = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['articles:list', 'articles:item'])]
    private ?string $logo = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): static
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getFkTeam(): ?Team
    {
        return $this->fk_team;
    }

    public function setFkTeam(?Team $fk_team): static
    {
        $this->fk_team = $fk_team;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFkUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFkUser() === $this) {
                $comment->setFkUser(null);
            }
        }

        return $this;
    }

    public function getFkCategory(): ?Categories
    {
        return $this->fk_category;
    }

    public function setFkCategory(?Categories $fk_category): static
    {
        $this->fk_category = $fk_category;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}
