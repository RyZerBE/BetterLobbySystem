<?php


namespace baubolp\ryzerbe\lobbycore\shop;


abstract class ShopCategory
{
    /** @var \baubolp\ryzerbe\lobbycore\shop\ShopArticle[]  */
    private $articles = [];

    abstract public function getName(): string;

    /**
     * @return \baubolp\ryzerbe\lobbycore\shop\ShopArticle[]
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * @param \baubolp\ryzerbe\lobbycore\shop\ShopArticle $article
     */
    public function addArticle(ShopArticle $article)
    {
        $this->articles[$article->getName()] = $article;
    }

    public function removeArticle(ShopArticle $article)
    {
        unset($this->articles[$article->getName()]);
    }

    public function register(): void {
        ShopManager::registerCategory($this);
    }

    public function unregister(): void {
        ShopManager::removeCategory($this);
    }
}