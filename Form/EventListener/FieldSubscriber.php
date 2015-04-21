<?php

namespace Mapbender\WmtsBundle\Form\EventListener;

use Mapbender\WmtsBundle\Entity\WmtsInstanceLayer;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

/**
 * FieldSubscriber class
 */
class FieldSubscriber implements EventSubscriberInterface
{

    /**
     * A FieldSubscriber's Factory
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $factory;

    /**
     * Creates an instance
     * @param \Symfony\Component\Form\FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Returns defined events
     * @return array events
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * Presets a form data
     * @param FormEvent $event
     * @return type
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data || !$data instanceof WmtsInstanceLayer) {
            return;
        }
        $form
            ->remove('toggle')
            ->add($this->factory->createNamed(
                'toggle',
                'checkbox',
                null,
                array(
                    'disabled' => false,
                    "required" => false,
                    'auto_initialize' => false
                )
            ))
            ->remove('allowtoggle')
            ->add($this->factory->createNamed(
                'allowtoggle',
                'checkbox',
                null,
                array(
                    'required' => false,
                    'disabled' => false,
                    'auto_initialize' => false
                )
            ))
            ->remove('toggle')
            ->add($this->factory->createNamed(
                'toggle',
                'checkbox',
                null,
                array(
                    'disabled' => true,
                    "required" => false,
                    'auto_initialize' => false
                )
            ))
            ->remove('allowtoggle')
            ->add($this->factory->createNamed(
                'allowtoggle',
                'checkbox',
                null,
                array(
                    'required' => false,
                    'disabled' => true,
                    'auto_initialize' => false
                )
            ));
        if (count($data->getSourceItem()->getInfoformats())) {
            $form->remove('info');
            $form->add($this->factory->createNamed(
                'info',
                'checkbox',
                null,
                array(
                    'disabled' => false,
                    "required" => false,
                    'auto_initialize' => false
                )
            ));
            $form->remove('allowinfo');
            $form->add($this->factory->createNamed(
                'allowinfo',
                'checkbox',
                null,
                array(
                    'disabled' => false,
                    "required" => false,
                    'auto_initialize' => false
                )
            ));
        }
        $arrStyles = $data->getSourceItem()->getStyles();
        $styleOpt = array("" => "");
        foreach ($arrStyles as $style) {
            $styleOpt[$style->getIdentifier()] = $style->getTitle();
        }

        $form->remove('style');
        $form->add($this->factory->createNamed(
            'style',
            'choice',
            null,
            array(
                'label' => 'style',
                'choices' => $styleOpt,
                "required" => false,
                'auto_initialize' => false
            )
        ));
    }
}
