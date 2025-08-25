<?php

class User {
    private ?int $id = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $telephone;
    private string $mot_de_passe;

    // Геттеры
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getTelephone(): string {
        return $this->telephone;
    }

    public function getMotDePasse(): string {
        return $this->mot_de_passe;
    }

    // Сеттеры
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setNom(string $nom): self {
        $this->nom = trim($nom);
        return $this;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = trim($prenom);
        return $this;
    }

    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format.");
        }
        $this->email = $email;
        return $this;
    }

    public function setTelephone(string $telephone): self {
        $this->telephone = trim($telephone);
        return $this;
    }

    public function setMotDePasse(string $mot_de_passe): self {
        
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }
}