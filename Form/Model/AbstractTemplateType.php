<?php

namespace Puzzle\Admin\PageBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 */
class AbstractTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('name', TextType::class, [
                'translation_domain' => 'admin',
                'label' => 'cms.template.name',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('content', TextareaType::class, [
                'translation_domain' => 'admin',
                'label' => 'cms.template.content',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control summernote'],
            ])
        ;
    }
}