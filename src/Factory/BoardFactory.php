<?php

namespace App\Factory;

use App\Entity\Board;
use App\Entity\Subcategory;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class BoardFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Board::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->unique()->text(50),
            'description' => self::faker()->text(200),
            'color' => self::faker()->hexColor(),
            'user' => UserFactory::randomOrCreate(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this->afterInstantiate(function(Board $board) {
            $rootCategory = new Subcategory($board);
            $rootCategory->setTitle('Categories');
            $board->addSubcategory($rootCategory);
        });

    }

    protected static function getClass(): string
    {
        return Board::class;
    }

}
