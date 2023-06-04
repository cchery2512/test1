<?php
// src/Entity/CurrencyRate.php

namespace App\Entity;
use App\Repository\CurrencyRateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRateRepository::class)]
class CurrencyRate{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3)]
    private ?string $baseCurrency = null;

    #[ORM\Column(length: 3)]
    private ?string $targetCurrency = null;

    #[ORM\Column]
    private ?float $rate = null;

    // Getters and setters
    public function getId(): ?int{
        return $this->id;
    }

    public function getBaseCurrency(): ?string{
        return $this->baseCurrency;
    }

    public function setBaseCurrency(string $baseCurrency): self{
        $this->baseCurrency = $baseCurrency;
        return $this;
    }

    public function getTargetCurrency(): ?string{
        return $this->targetCurrency;
    }

    public function setTargetCurrency(string $targetCurrency): self{
        $this->targetCurrency = $targetCurrency;
        return $this;
    }

    public function getRate(): ?float{
        return $this->rate;
    }

    public function setRate(float $rate): self{
        $this->rate = $rate;
        return $this;
    }
    
}