<?php

namespace App\Form;
use App\Entity\Subcategory;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscribeTelegramType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chat_id', IntegerType::class, [
                'label' => 'form.telegram_chat.chat_id',
                'required' => true,
            ])
            ->add('chat_title', TextType::class, [
                'label' => 'form.telegram_chat.chat_title',
                'required' => false,
            ])
            ->add('teacher_id', IntegerType::class, [
                'label' => 'form.telegram_chat.teacher_id',
                'required' => true,
            ]);
    }

        public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}