<?php

namespace App\Twig\Extension;

use App\Entity\Subcategory;
use App\Service\SubcategoryHelper;
use App\Service\TaskService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TaskHelperExtension extends AbstractExtension
{
    public function __construct(
        private TaskService $taskService
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('minutes_to_estimate_parts', [$this, 'convertMinutesToEstimateParts']),
        ];
    }

    public function convertMinutesToEstimateParts(int $minutes): array
    {
        return $this->taskService->convertMinutesToEstimateParts($minutes);
    }

}