<?php

namespace Kuma\BroodjesBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * The type for Product
 */
class ProductAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('price');

        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => 'Kuma\BroodjesBundle\Entity\Category',
                'placeholder' => 'Kies een categorie',
                'choice_label' => 'name',
            ]
        );

        $builder->add('ingredients');

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'product_form';
    }
}
