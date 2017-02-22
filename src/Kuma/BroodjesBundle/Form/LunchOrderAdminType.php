<?php

namespace Kuma\BroodjesBundle\Form;

use Doctrine\ORM\EntityRepository;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * The type for LunchOrder
 */
class LunchOrderAdminType extends AbstractType
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

    private $token;

    public function __construct(TokenStorage $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var LunchOrder $order */
        $order = $options['data'];
        $builder->add(
            'endProducts',
            EntityType::class,
            array(
                'class' => 'Kuma\BroodjesBundle\Entity\EndProduct',
                'query_builder' => function (EntityRepository $er) use ($order) {
                    return $er->createQueryBuilder('e')
                        //->join('e.lunchOrders', 'o') only ordered endproducts in list
                        ->where('e.user = :user')
                        ->setParameter('user', $order->getUser());
                },
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'lunchorder_form';
    }

    public function getName()
    {
        return 'kumabroodjesbundle_form_lunchorder';
    }
}
