<?php

namespace App\Factory;

use App\Entity\Board;
use App\Entity\Enom\TaskPriority;
use App\Entity\Enom\TaskStatus;
use App\Entity\Task;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
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
        return Task::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(5),
            'description' => self::faker()->paragraph(3),
            'deadline' => self::faker()->dateTimeBetween('+1 week', '+1 month'),
            'approximateEstimate' => self::faker()->randomElement(['2h', '4h', '1d', '3d', '8h']),
            'scheduledForDate' => self::faker()->dateTimeBetween('now', '+1 month'),
            'taskPriority' => self::faker()->randomElement(TaskPriority::cases()),
            'taskStatus' => self::faker()->randomElement(TaskStatus::cases()),
            'board' => BoardFactory::randomOrCreate(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
    protected static function getClass(): string
    {
        return Task::class;
    }

}
