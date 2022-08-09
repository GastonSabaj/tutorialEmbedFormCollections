<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    //#[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Exp::class)]

    /**
     * @ORM\OneToMany(targetEntity=Exp::class, mappedBy="usuario",cascade={"persist"})
     */
     private $exps;

    public function __construct()
    {
        $this->exps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, Exp>
     */
    public function getExps()
    {
        return $this->exps;
    }

    public function addExp(Exp $exp): self
    {
        if (!$this->exps->contains($exp)) {
            $this->exps->add($exp);
            $exp->setUsuario($this);
        }

        return $this;
    }

    public function removeExp(Exp $exp): self
    {
        if ($this->exps->removeElement($exp)) {
            // set the owning side to null (unless already changed)
            if ($exp->getUsuario() === $this) {
                $exp->setUsuario(null);
            }
        }

        return $this;
    }
}
