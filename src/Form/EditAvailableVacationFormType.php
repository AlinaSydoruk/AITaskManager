<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class EditAvailableVacationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uploadFile', FileType::class, [
                'mapped' => false,
                'label' => 'upload.upload_file_xlsx',
                'constraints' => [
                    new File([
                        'maxSize' => '16M',
                        'mimeTypes' => [
                            'application/vnd.ms-excel', // xls
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // xlsx
                        ],
                        'mimeTypesMessage' => 'error.please_upload_a_valid_xlsx',
                        'maxSizeMessage' => 'error.file_too_large',
                    ])
                ],
            ])
        ;
    }
}
