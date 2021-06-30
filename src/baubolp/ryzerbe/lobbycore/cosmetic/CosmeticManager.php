<?php

namespace baubolp\ryzerbe\lobbycore\cosmetic;

use baubolp\ryzerbe\lobbycore\cosmetic\category\CosmeticCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\category\type\ItemRainCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\category\type\ParticleCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\category\type\SpecialsCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\category\type\WalkingBlocksCategory;
use baubolp\ryzerbe\lobbycore\cosmetic\type\Cosmetic;
use pocketmine\utils\SingletonTrait;
use function is_null;
use function var_dump;

class CosmeticManager {
    use SingletonTrait;

    public const CATEGORY_PARTICLE = 0;
    public const CATEGORY_ITEM_RAIN = 1;
    public const CATEGORY_WALKING_BLOCKS = 2;
    public const CATEGORY_SPECIALS = 3;

    /** @var array  */
    private $categories = [];

    /**
     * CosmeticManager constructor.
     */
    public function __construct(){
        $categories = [
            new ParticleCategory(),
            new ItemRainCategory(),
            new SpecialsCategory(),
            new WalkingBlocksCategory()
        ];
        foreach($categories as $category) {
            $this->registerCategory($category);
        }
    }

    /**
     * @param CosmeticCategory $category
     */
    public function registerCategory(CosmeticCategory $category): void {
        $this->categories[$category->getId()] = $category;
        $category->loadCosmetics();
    }

    /**
     * @param int $id
     * @return CosmeticCategory|null
     */
    public function getCategory(int $id): ?CosmeticCategory {
        return $this->categories[$id] ?? null;
    }

    /**
     * @return CosmeticCategory[]
     */
    public function getCategories(): array{
        return $this->categories;
    }

    /**
     * @param string $identifier
     * @return Cosmetic|null
     */
    public function getCosmetic(string $identifier): ?Cosmetic {
        foreach($this->getCategories() as $category) {
            $cosmetic = $category->getCosmetic($identifier);
            if(!is_null($cosmetic)) return $cosmetic;
        }
        return null;
    }
}