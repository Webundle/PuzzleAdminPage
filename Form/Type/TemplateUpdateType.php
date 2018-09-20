<?php

namespace Puzzle\Admin\PageBundle\Form\Type;

use Puzzle\Admin\PageBundle\Form\Model\AbstractTemplateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 * @author AGNES Gnagne Cédric <cecenho55@gmail.com>
 *
 */
class TemplateUpdateType extends AbstractTemplateType
{
    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);
        
        $resolver->setDefault('csrf_token_id', 'template_update');
        $resolver->setDefault('validation_groups', ['Update']);
    }
    
    public function getBlockPrefix() {
        return 'admin_page_template_update';
    }
}