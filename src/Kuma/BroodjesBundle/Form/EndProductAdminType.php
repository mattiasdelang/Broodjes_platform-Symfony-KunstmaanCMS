<?php

namespace Kuma\BroodjesBundle\Form;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Form\EventListener\AddUserListener;
use Kuma\BroodjesBundle\Form\EventListener\ChangeSupplementsSubscriber;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * The type for EndProduct
 */
class EndProductAdminType extends AbstractType
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
    private $em;
    private $addUserListener;
    private $changeSupplementsSubscriber;

    public function __construct(EntityManager $em, AddUserListener $addUserListener, ChangeSupplementsSubscriber $changeSupplementsSubscriber)
    {
        $this->em = $em;
        $this->addUserListener = $addUserListener;
        $this->changeSupplementsSubscriber = $changeSupplementsSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slack_name', TextType::class);

        $builder->add(
            'product',
            EntityType::class,
            [
                'class' => 'Kuma\BroodjesBundle\Entity\Product',
                'choice_label' => 'name',
                'placeholder' => 'Kies een product',
                'required' => true,
            ]
        );

        $builder->add(
            'ingredients',
            TextareaType::class,
            [
                'mapped' => false,
                'required' => false,
                'data' => 'testtest',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ]
        );

        $builder->add(
            'supplements',
            EntityType::class,
            [
                'class' => 'Kuma\BroodjesBundle\Entity\Supplement',
                'placeholder' => 'Kies je product',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]
        );

        $builder->add('extra_info', TextType::class);

        $builder->addEventSubscriber($this->addUserListener);
        $builder->addEventSubscriber($this->changeSupplementsSubscriber);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'endproduct_form';
    }

    public function getName()
    {
        return 'kumabroodjesbundle_form_endproduct';
    }
}
