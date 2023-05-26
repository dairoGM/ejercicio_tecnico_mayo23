<?php

namespace App\Entity;

use App\Repository\StockRepository;
use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Sales
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Product $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="float")
     */
    private $saleValue;

    /**
     * @ORM\Column(type="date")
     */
    private $registerDate;

    public function __construct()
    {
        $this->registerDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return \App\Entity\Product|null
     */
    public function getProduct(): ?\App\Entity\Product
    {
        return $this->product;
    }

    /**
     * @param \App\Entity\Product|null $product
     */
    public function setProduct(?\App\Entity\Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return \DateTime
     */
    public function getRegisterDate(): \DateTime
    {
        return $this->registerDate;
    }

    /**
     * @param \DateTime $registerDate
     */
    public function setRegisterDate(\DateTime $registerDate): void
    {
        $this->registerDate = $registerDate;
    }

    /**
     * @return mixed
     */
    public function getSaleValue()
    {
        return $this->saleValue;
    }

    /**
     * @param mixed $saleValue
     */
    public function setSaleValue($saleValue): void
    {
        $this->saleValue = $saleValue;
    }


}
