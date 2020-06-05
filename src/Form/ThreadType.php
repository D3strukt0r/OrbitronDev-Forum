<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'service_create_thread.form.title.label',
                    'attr' => [
                        'placeholder' => 'service_create_thread.form.title.placeholder',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'new_thread.title.not_blank']),
                    ],
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(['message' => 'new_thread.message.not_blank']),
                    ],
                ]
            )
            ->add(
                'send',
                SubmitType::class,
                [
                    'label' => 'service_create_thread.form.send.label',
                ]
            )
        ;
    }
}
