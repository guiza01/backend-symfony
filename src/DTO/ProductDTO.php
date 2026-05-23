<?php
namespace App\DTO;

class ProductDTO
{
    public ?int $id = null;
    public string $name = '';
    public ?string $description = null;
    public float $price = 0.0;
    public int $stock = 0;
    private bool $stockIsInteger = true;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        if (isset($data['id'])) $dto->id = (int) $data['id'];
        if (isset($data['name'])) $dto->name = (string) $data['name'];
        if (isset($data['description'])) $dto->description = $data['description'] !== null ? (string) $data['description'] : null;
        if (isset($data['price'])) $dto->price = (float) $data['price'];
        if (isset($data['stock'])) {
            $dto->stock = (int) $data['stock'];
            $dto->stockIsInteger = filter_var($data['stock'], FILTER_VALIDATE_INT) !== false;
        }
        return $dto;
    }

    public function toArray(): array
    {
        $out = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
        if ($this->id !== null) $out['id'] = $this->id;
        return $out;
    }

    public function validate(): void
    {
        $errors = [];
        if (trim($this->name) === '') {
            $errors['name'] = 'O nome é obrigatório';
        } elseif (mb_strlen($this->name) < 3) {
            $errors['name'] = 'O nome deve ter no mínimo 3 caracteres';
        }
        if (!is_numeric($this->price) || $this->price <= 0) {
            $errors['price'] = 'O preço deve ser maior que zero';
        }
        if (!$this->stockIsInteger) {
            $errors['stock'] = 'O estoque deve ser inteiro';
        } elseif ($this->stock < 0) {
            $errors['stock'] = 'O estoque deve ser maior ou igual a zero';
        }
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }
    }
}
