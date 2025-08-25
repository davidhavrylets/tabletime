<?php

<?php

require_once __DIR__ . '/../config/database.php';

class Reservation {
    private ?int $id = null;
    private int $user_id;
    private int $restaurant_id;
    private string $date;
    private string $time;
    private int $guests;

    // Геттеры
    public function getId(): ?int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function getRestaurantId(): int {
        return $this->restaurant_id;
    }

    public function getDate(): string {
        return $this->date;
    }

    public function getTime(): string {
        return $this->time;
    }

    public function getGuests(): int {
        return $this->guests;
    }

    // Сеттеры
    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function setUserId(int $user_id): self {
        $this->user_id = $user_id;
        return $this;
    }

    public function setRestaurantId(int $restaurant_id): self {
        $this->restaurant_id = $restaurant_id;
        return $this;
    }

    public function setDate(string $date): self {
        $this->date = $date;
        return $this;
    }

    public function setTime(string $time): self {
        $this->time = $time;
        return $this;
    }

    public function setGuests(int $guests): self {
        if ($guests <= 0) {
            throw new InvalidArgumentException("Количество гостей должно быть больше нуля.");
        }
        $this->guests = $guests;
        return $this;
    }
}