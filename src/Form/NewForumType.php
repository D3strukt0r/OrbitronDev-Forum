<?php

namespace App\Form;

use App\Form\Type\ReCaptchaType;
use App\Service\ForumHelper;
use App\Validator\Constraints\ReCaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NewForumType extends AbstractType
{
    private $helper;

    public function __construct(ForumHelper $helper)
    {
        $this->helper = $helper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'new_forum.form.name.label',
                'constraints' => [
                    new NotBlank(['message' => 'new_forum.name.not_blank']),
                    new Length([
                        'min'        => ForumHelper::$settings['forum']['name']['min_length'],
                        'minMessage' => 'new_forum.name.min_length',
                    ]),
                ],
            ])
            ->add('url', TextType::class, [
                'label'       => 'new_forum.form.url.label',
                'constraints' => [
                    new NotBlank(['message' => 'new_forum.url.not_blank']),
                    new Length([
                        'min'        => ForumHelper::$settings['forum']['url']['min_length'],
                        'minMessage' => 'new_forum.url.min_length',
                    ]),
                    new Regex([
                        'pattern' => '/[^a-zA-Z_\-0-9]/i',
                        'message' => 'new_forum.url.regex',
                        'match'   => false,
                    ]),
                    new Expression([
                        'expression' => 'value not in ["new-forum", "admin", "login", "login-check", "logout", "user", "setup"]',
                        'message'    => 'new_forum.url.not_equal_to',
                    ]),
                    new Callback(function ($object, ExecutionContextInterface $context, $payload) {
                        if ($this->helper->urlExists($object)) {
                            $context->addViolation('new_forum.url.already_in_use');
                        }
                    }),
                ],
            ])
            ->add('recaptcha', ReCaptchaType::class, [
                'attr'        => [
                    'options' => [
                        'theme' => 'light',
                        'type'  => 'image',
                        'size'  => 'normal',
                        'defer' => true,
                        'async' => true,
                    ],
                ],
                'mapped'      => false,
                'constraints' => [
                    new ReCaptchaTrue(),
                ],
            ])
            ->add('send', SubmitType::class, [
                'label' => 'new_forum.form.send.label',
            ]);
    }
}
