<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'service_create_post.form.title.label',
                'attr' => [
                    'placeholder' => 'service_create_post.form.title.placeholder',
                ],
                'data' => 'RE: '.$options['topic'],
                'constraints' => [
                    new NotBlank(['message' => 'new_post.title.not_blank']),
                ],
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'new_post.message.not_blank']),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'service_create_post.form.send.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'topic' => '',
        ]);
    }
}
