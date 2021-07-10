<?php


namespace baubolp\ryzerbe\lobbycore\shop;


use baubolp\ryzerbe\lobbycore\shop\article\VIPRank;
use baubolp\ryzerbe\lobbycore\shop\category\RankCategory;

class ShopManager
{
    /** @var \baubolp\ryzerbe\lobbycore\shop\ShopCategory[]  */
    public static $categories = [];

    /**
     * @return \baubolp\ryzerbe\lobbycore\shop\ShopCategory[]
     */
    public static function getCategories(): array
    {
        return self::$categories;
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\shop\ShopCategory $category
     */
    public static function registerCategory(ShopCategory $category): void
    {
        self::$categories[$category->getName()] = $category;
    }

    public static function registerCategories(): void
    {
        $rankCategory = new RankCategory();
        $rankCategory->addArticle(new VIPRank());
        $rankCategory->register();
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\shop\ShopCategory $category
     */
    public static function removeCategory(ShopCategory $category): void
    {
        unset(self::$categories[$category->getName()]);
    }
}