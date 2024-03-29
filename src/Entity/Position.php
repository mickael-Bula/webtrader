<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Position
{
    /**
     * déclaration d'une constante pour gérer la valeur d'une ligne
     *
     * @var integer
     */
    public const LINE_VALUE = 750;

    /**
     * déclaration du pourcentage de baisse fixant le niveau de Buy_limit
     *
     * @var integer
     */
    public const SPREAD = 0.06; // on fixe ici la limite à 6 % de baisse, le palier d'achat étant fixé à 2 % pout 3 lignes

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $buyTarget;

    /**
     * @ORM\Column(type="float")
     */
    private $sellTarget;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $buyDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $sellDate;

    /**
     * @ORM\Column(type="boolean", options={"default" : "0"})
     */
    private $isActive = false;

    /**
     * @ORM\Column(type="boolean", options={"default" : "0"})
     */
    private $isClosed = false;

    /**
     * @ORM\Column(type="boolean", options={"default" : "0"})
     */
    private $isWaiting = false;

    /**
     * @ORM\Column(type="boolean", options={"default" : "0"})
     */
    private $isRunning = false;

    /**
     * @ORM\ManyToOne(targetEntity=LastHigh::class, inversedBy="positions", cascade={"persist", "remove"})
     */
    private $buyLimit;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="positions")
     */
    private $User;

    /**
     * @ORM\Column(type="float")
     */
    private $lvcBuyTarget;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lvcSellTarget;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyTarget(): ?float
    {
        return $this->buyTarget;
    }

    public function setBuyTarget(float $buyTarget): self
    {
        $this->buyTarget = $buyTarget;

        return $this;
    }

    public function getSellTarget(): ?float
    {
        return $this->sellTarget;
    }

    public function setSellTarget(float $sellTarget): self
    {
        $this->sellTarget = $sellTarget;

        return $this;
    }

    public function getBuyDate(): ?\DateTimeInterface
    {
        return $this->buyDate;
    }

    public function setBuyDate(\DateTimeInterface $buyDate): self
    {
        $this->buyDate = $buyDate;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isIsClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function isIsWaiting(): ?bool
    {
        return $this->isWaiting;
    }

    public function setIsWaiting(bool $isWaiting): self
    {
        $this->isWaiting = $isWaiting;

        return $this;
    }

    public function isIsRunning(): ?bool
    {
        return $this->isRunning;
    }

    public function setIsRunning(bool $isRunning): self
    {
        $this->isRunning = $isRunning;

        return $this;
    }

    public function getBuyLimit(): ?LastHigh
    {
        return $this->buyLimit;
    }

    public function setBuyLimit(?LastHigh $buyLimit): self
    {
        $this->buyLimit = $buyLimit;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * méthode appelée avant chaque Event persist et update de l'entité Position pour fixer la cible de revente à +10%
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function SellTargetEvent()
    {
        if ($this->buyTarget) {
            $this->setSellTarget($this->buyTarget * 1.1);
        }
    }

    public function getSellDate(): ?\DateTimeInterface
    {
        return $this->sellDate;
    }

    public function setSellDate(?\DateTimeInterface $sellDate): self
    {
        $this->sellDate = $sellDate;

        return $this;
    }

    public function getLvcBuyTarget(): ?float
    {
        return $this->lvcBuyTarget;
    }

    public function setLvcBuyTarget(float $lvcBuyTarget): self
    {
        $this->lvcBuyTarget = $lvcBuyTarget;

        return $this;
    }

    public function getLvcSellTarget(): ?float
    {
        return $this->lvcSellTarget;
    }

    public function setLvcSellTarget(?float $lvcSellTarget): self
    {
        $this->lvcSellTarget = $lvcSellTarget;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
