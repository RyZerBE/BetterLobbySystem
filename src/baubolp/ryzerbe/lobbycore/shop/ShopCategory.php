<?php

namespace baubolp\ryzerbe\lobbycore\shop;

abstract class ShopCategory {
    /** @var ShopArticle[] */
    private $articles = [];

    abstract public function getName(): string;

    /**
     * @return ShopArticle[]
     */
    public function getArticles(): array{
        return $this->articles;
    }

    /**
     * @param ShopArticle $article
     */
    public function addArticle(ShopArticle $article){
        $this->articles[$article->getName()] = $article;
    }

    public function removeArticle(ShopArticle $article){
        unset($this->articles[$article->getName()]);
    }

    public function register(): void{
        ShopManager::registerCategory($this);
    }

    public function unregister(): void{
        ShopManager::removeCategory($this);
    }
}