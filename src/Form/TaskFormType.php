<?php

namespace App\Form;

use App\Entity\Board;
use App\Entity\Enom\TaskPriority;
use App\Entity\Enom\TaskStatus;
use App\Entity\Subcategory;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,

    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'task.title',
            ])
            ->add('deadline', DateType::class, [
                'label' => 'task.deadline',
                'required' => false,
            ])
            ->add('approximateEstimateDays', IntegerType::class, [
                'label' => 'task.approximate_estimate_days',
                'attr' => [
                    'class' => 'py-2.5 px-4 w-full ',
                    'min' => 0
                ],
                'row_attr' => ['class' => ' mt-2 '],
                'mapped' => false,
            ])
            ->add('approximateEstimateHours', TimeType::class, [
                'label' => 'task.approximate_estimate_hours',
                'attr' => ['class' => 'border-2 py-2.5 px-4 w-36'],
                'row_attr' => ['class' => 'w-full flex flex-col mt-2 items-end text-right'],
                'mapped' => false,
            ])
            ->add('scheduledForDate', DateType::class, [
                'label' => 'task.scheduled_for_date',
            ])
            ->add('taskPriority', EnumType::class, [
                'class' => TaskPriority::class,
                'choice_label' => function ($choice) {
                    return $this->translator->trans($choice->getTranslationKey());
                },
                'label' => 'task.priority',
                'attr' => ['class' => 'border-2 py-2.5 px-3 w-full'],
                'row_attr' => ['class' => 'my-2 '],
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_choose_the_task_priority',
                    ]),
                ],
            ])
            ->add('taskStatus', EnumType::class, [
                'class' => TaskStatus::class,
                'choice_label' => function ($choice) {
                    return $this->translator->trans($choice->getTranslationKey());
                },
                'label' => 'task.status',
                'attr' => ['class' => 'border-2 py-2.5 px-3 w-full'],
                'row_attr' => ['class' => 'my-2 '],
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_choose_the_task_priority',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'task.description',
                'required' => false,
            ])
            ->add('subcategory', EntityType::class, [
                'class' => Subcategory::class,
                'required' => false,
                'attr' => ['type' => 'hidden'],
                'choice_label' => 'id',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}