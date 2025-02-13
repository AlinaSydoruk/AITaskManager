<?php

namespace App\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class NoValidateExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly bool $html5_validation
    )
    {
    }

    /**
     * @param array<string,mixed> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $attr = !$this->html5_validation ? ['novalidate' => 'novalidate'] : [];
        $view->vars['attr'] = array_merge($view->vars['attr'], $attr);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}