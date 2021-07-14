<?php


namespace baubolp\ryzerbe\lobbycore\cosmetic\type\special;


class HeadCanonSpecialCosmetic extends SpecialCosmetic
{

    public function getName(): string
    {
        return "Head Canon";
    }

    public function getPrice(): int
    {
        return 10000;
    }

    public function getIdentifier(): string
    {
        return "special:head_canon";
    }
}