<?php

namespace App\Entity;

use App\Repository\OrderDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderDetailsRepository::class)
 */
class OrderDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myOrder;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productName;

    public function __toString(){

        return $this->getProductName() . ' (' . number_format($this->getPrice()/100, 2, ',', ' ') . ' €)' . ' x ' . $this->getQuantity();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyOrder(): ?Order
    {
        return $this->myOrder;
    }

    public function setMyOrder(?Order $myOrder): self
    {
        $this->myOrder = $myOrder;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $id): self
    {
        $this->productId = $id;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $name): self
    {
        $this->productName = $name;

        return $this;
    }
}
