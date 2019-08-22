<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Créer un article avec un new User
        /*$builder
            ->add('name')
            ->add('content')
            ->add('createdAt')
            ->add('imageUrl')
            ->add('published')
            ->add('expiredAt')
            ->add('user', UserType::class)
            ->add('submit', SubmitType::class);*/

        // Version avec selection d'un User
        $builder
            ->add('name')
            ->add('content')
            ->add('createdAt')
            ->add('imageUrl')
            ->add('published')
            ->add('expiredAt')
            ->add('user', EntityType::class, [
                        'class' => User::class,
                        'choice_label' => 'firstname'
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
