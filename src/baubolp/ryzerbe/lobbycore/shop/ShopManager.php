<?php


namespace baubolp\ryzerbe\lobbycore\shop;


use baubolp\ryzerbe\lobbycore\shop\article\StatsResetTokenArticle;
use baubolp\ryzerbe\lobbycore\shop\article\VIPRank;
use baubolp\ryzerbe\lobbycore\shop\category\RankCategory;
use baubolp\ryzerbe\lobbycore\shop\category\StatsCategory;

class ShopManager
{
    /** @var ShopCategory[] */
    public static $categories = [];

    /**
     * @return ShopCategory[]
     */
    public static function getCategories(): array
    {
        return self::$categories;
    }

    /**
     * @param ShopCategory $category
     */
    public static function registerCategory(ShopCategory $category): void
    {
        self::$categories[$category->getName()] = $category;
    }

    public static function registerCategories(): void
    {
       /* $rankCategory = new RankCategory();
        $rankCategory->addArticle(new VIPRank());
        $rankCategory->register();*/
        $statsCategory = new StatsCategory();
        $statsCategory->addArticle(new StatsResetTokenArticle());
        $statsCategory->register();
    }

    /**
     * @param ShopCategory $category
     */
    public static function removeCategory(ShopCategory $category): void
    {
        unset(self::$categories[$category->getName()]);
    }
}