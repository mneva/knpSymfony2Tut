<?php
namespace Yoda\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class, array(
                'required' =>false,
                'label' => 'Email Address',
                'attr' => array('class' => 'C-3PO')
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['email']->vars['help'] = 'Hint: it will have an @ symbol';
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yoda\UserBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'user_register';
    }
}