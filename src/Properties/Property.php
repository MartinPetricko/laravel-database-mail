<?php

declare(strict_types=1);

namespace MartinPetricko\LaravelDatabaseMail\Properties;

class Property
{
    protected string $name;

    protected bool $isHidden = false;

    protected bool $isNullable = false;

    protected bool $isBoolean = false;

    protected bool $isTraversable = false;

    protected ?Property $parent = null;

    /**
     * List of properties that are children of this property.
     *
     * @var array<Property>
     */
    protected array $properties = [];

    protected ?string $accessor = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hidden(bool $isHidden = true): static
    {
        $this->isHidden = $isHidden;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function nullable(bool $isNullable = true): static
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function boolean(bool $isBoolean = true): static
    {
        $this->isBoolean = $isBoolean;
        return $this;
    }

    public function isBoolean(): bool
    {
        return $this->isBoolean;
    }

    public function traversable(bool $isTraversable = true): static
    {
        $this->isTraversable = $isTraversable;
        return $this;
    }

    public function isTraversable(): bool
    {
        return $this->isTraversable;
    }

    public function parent(Property $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    public function getParent(): ?Property
    {
        return $this->parent;
    }

    /** @param array<Property> $properties */
    public function properties(array $properties): static
    {
        $this->properties = [];
        foreach ($properties as $property) {
            $this->properties[$property->getName()] = $property->parent($this);
        }
        return $this;
    }

    public function hasProperties(): bool
    {
        return $this->properties !== [];
    }

    /** @return array<Property> */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function accessor(?string $accessor): static
    {
        $this->accessor = $accessor;
        return $this;
    }

    public function getAccessor(bool $absolute = true): string
    {
        if ($this->accessor !== null) {
            if ($absolute === false || !$this->hasParent()) {
                return $this->accessor;
            }

            return $this->getParent()?->getAccessor() . $this->accessor;
        }

        if (!$this->hasParent()) {
            return '$' . $this->getName();
        }

        if ($absolute === false) {
            return '[\'' . $this->getName() . '\']';
        }

        return $this->getParent()?->getAccessor() . $this->getAccessor(false);
    }
}
