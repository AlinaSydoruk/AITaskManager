<?php

namespace App\Form;
use App\Entity\Board;
use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'board.title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'board.description',
                'required' => false,

            ])
            ->add('color', ChoiceType::class, [
                'label' => 'board.color',
                'choices' => [
                    'Red' => 'red',
                    'Blue' => 'blue',
                    'Green' => 'green',
                    'Yellow' => 'yellow',
                    'Gray' => 'gray',
                ],
                'expanded' => false,
                'multiple' => false,

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
        ]);
    }
}