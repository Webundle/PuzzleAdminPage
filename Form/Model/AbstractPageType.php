<?php

namespace Puzzle\Admin\PageBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'admin',
                'label' => 'cms.page.name',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('content', TextareaType::class, [
                'translation_domain' => 'admin',
                'label' => 'cms.page.content',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control summernote'],
            ])
        ;
    }
}