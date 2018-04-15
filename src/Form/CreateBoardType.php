<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'admin.form.create_board.name.label',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'admin.form.create_board.description.label',
                'required' => false,
            ])
            ->add('parent', ChoiceType::class, [
                'label' => 'admin.form.create_board.parent.label',
                'required' => true,
                'choices' => $options['board_list'],
                'expanded' => false, // select tag
                'multiple' => false,
                'choice_translation_domain' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'admin.form.create_board.type.label',
                'required' => false,
                'choices' => [
                    'admin.form.create_board.type.options.board' => 1,
                    'admin.form.create_board.type.options.category' => 2,
                ],
                'placeholder' => false,
                'expanded' => true, // radio buttons
                'multiple' => false,
            ])
            ->add('send', SubmitType::class, [
                'label' => 'admin.form.create_board.send.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'board_list' => null,
        ]);
    }
}
