<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\Supplement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChangeSupplementsSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [
            FormEvents::PRE_SET_DATA => 'onPreSet',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSet(FormEvent $event)
    {
        /**
         * @var EndProduct
         */
        $endProduct = $event->getData();
        $form = $event->getForm();

        $product = $endProduct->getProduct();

        if (is_null($product)) {
            $supplements = $this->em->getRepository('KumaBroodjesBundle:Supplement')->findAll();
            $productInfo = 'Geen ingrediënten';
        } else {
            $supplements = $product->getCategory()->getSupplements();
            $productInfo = $product->getIngredients();
        }

        $form->add('supplements', EntityType::class, [
                'class' => 'Kuma\BroodjesBundle\Entity\Supplement',
                'choices' => $supplements,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
        ]);

        $form->add('ingredients', TextareaType::class, [
            'mapped' => false,
            'required' => false,
            'data' => $productInfo,
            'attr' => [
                'disabled' => 'disabled',
            ],
        ]);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $productid = $event->getData();
        $form = $event->getForm();

        $product = $this->em->getRepository('KumaBroodjesBundle:Product')->find($productid['product']);

        if (is_null($product)) {
            $supplements = $this->em->getRepository('KumaBroodjesBundle:Supplement')->findAll();
            $productInfo = 'Geen ingrediënten';
        } else {
            $supplements = $product->getCategory()->getSupplements();
            $productInfo = $product->getIngredients();
            if (is_null($productInfo)) {
                $productInfo = 'Geen ingrediënten';
            }
        }

        $form->add('ingredients', TextareaType::class, [
            'mapped' => false,
            'required' => false,
            'data' => $productInfo,
            'compound' => true,
            'attr' => [
                'disabled' => 'disabled',
            ],
        ]);

        $form->add('supplements', EntityType::class, [
            'class' => 'Kuma\BroodjesBundle\Entity\Supplement',
            'choices' => $supplements,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ]);
    }
}
