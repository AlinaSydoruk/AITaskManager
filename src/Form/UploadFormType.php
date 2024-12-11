<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class UploadFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uploadFile', FileType::class, [
                'mapped' => false,
                'label' => 'Upload file XLSX',
                'constraints' => [
                    new File([
                        'maxSize' => '16M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel', // xls
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // xlsx
                        ],
                        'mimeTypesMessage' => 'Please upload a valid XLSX',
                        'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}',
                    ])
                ],
            ])
        ;
    }
}
