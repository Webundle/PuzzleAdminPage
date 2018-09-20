<?php

namespace Puzzle\Admin\PageBundle\Form\Type;

use Puzzle\Admin\PageBundle\Form\Model\AbstractPageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 * 
 */
class PageCreateType extends AbstractPageType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'page_create');
        $resolver->setDefault('validation_groups', ['Create']);
    }
    
    public function getBlockPrefix() {
        return 'admin_page_page_create';
    }
}