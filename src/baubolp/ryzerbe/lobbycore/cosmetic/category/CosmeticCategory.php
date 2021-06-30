<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic\category;

use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;

abstract class CosmeticCategory {

    /** @var array  */
    protected $cosmetics = [];

    abstract public function loadCosmetics(): void;
    abstract public function getId(): int;
    abstract public function getName(): string;

    /**
     * @param Cosmetic $cosmetic
     */
    public function registerCosmetic(Cosmetic $cosmetic): void {
        $this->cosmetics[$cosmetic->getIdentifier()] = $cosmetic;
    }

    /**
     * @param string $identifier
     * @return Cosmetic|null
     */
    public function getCosmetic(string $identifier): ?Cosmetic {
        return $this->cosmetics[$identifier] ?? null;
    }

    /**
     * @return Cosmetic[]
     */
    public function getCosmetics(): array{
        return $this->cosmetics;
    }

    /**
     * @return string
     */
    public function getIcon(): string {
        return "";
    }

    /**
     * @return int
     */
    public function getIconType(): int {
        return -1;
    }
}