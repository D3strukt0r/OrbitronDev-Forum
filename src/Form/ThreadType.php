<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \App\Entity\Board $board */
        $board = $options['board'];

        $builder
            ->add('parent', HiddenType::class, [
                'data' => $board->getId(),
            ])
            ->add('title', TextType::class, [
                'label'       => 'service_create_thread.form.title.label',
                'attr'        => [
                    'placeholder' => 'service_create_thread.form.title.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'service_create_thread.form.title.constraints.not_blank']),
                ],
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'service_create_thread.form.message.constraints.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'service_create_thread.form.send.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'board' => null,
        ]);
    }
}
