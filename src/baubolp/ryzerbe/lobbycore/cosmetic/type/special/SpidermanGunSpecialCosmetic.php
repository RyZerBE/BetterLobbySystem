<?php


namespace baubolp\ryzerbe\lobbycore\cosmetic\type\special;


class SpidermanGunSpecialCosmetic extends SpecialCosmetic
{

    public function getName(): string
    {
        return "Spiderman Gun";
    }

    public function getPrice(): int
    {
        return 10000;
    }

    public function getIdentifier(): string
    {
        return "special:spiderman_gun";
    }
}