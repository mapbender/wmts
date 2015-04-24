<?php
namespace Mapbender\WmtsBundle\Component;

use Mapbender\CoreBundle\Component\InstanceConfiguration;
use Mapbender\CoreBundle\Component\InstanceConfigurationOptions;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WmtsInstanceConfiguration
 *
 * @author Paul Schmidt
 */
class WmtsInstanceConfiguration extends InstanceConfiguration
{

    /**
     * ORM\Column(type="array", nullable=true)
     */

    public $layers;

    /**
     * ORM\Column(type="array", nullable=true)
     */
    public $tilematrixsets;

    public function getLayers()
    {
        return $this->layers;
    }

    public function getTilematrixsets()
    {
        return $this->tilematrixsets;
    }

    public function setLayers($layers)
    {
        $this->layers = $layers;
        return $this;
    }

    public function setTilematrixsets($tilematrixsets)
    {
        $this->tilematrixsets = $tilematrixsets;
        return $this;
    }



    public function addTilematrixset($tilematrixset)
    {
        $this->tilematrixsets[] = $tilematrixset;
        return $this;
    }

    /**
     * Sets options
     * @param ServiceConfigurationOptions $options ServiceConfigurationOptions
     * @return InstanceConfiguration
     */
    public function setOptions(InstanceConfigurationOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Returns options
     * @return ServiceConfigurationOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets a children
     * @param array $children children
     * @return InstanceConfiguration
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Returns a title
     * @return integer children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns a title
     * @return integer children
     */
    public function addChild($child)
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return array(
            "type" => $this->type,
            "title" => $this->title,
            "isBaseSource" => $this->isBaseSource,
            "options" => $this->options->toArray(),
            "children" => $this->children,
            "layers" => $this->layers,
            "tilematrixsets" => $this->tilematrixsets
        );
    }

    /**
     * @inheritdoc
     */
    public static function fromArray($options)
    {
        throw new \Exception('not implemented yet.');
        $ic = null;
        if ($options && is_array($options)) {
            $ic = new WmtsInstanceConfiguration();
            if (isset($options['type'])) {
                $ic->type = $options['type'];
            }
            if (isset($options['title'])) {
                $ic->title = $options['title'];
            }
            if (isset($options['isBaseSource'])) {
                $ic->isBaseSource = $options['isBaseSource'];
            }
            if (isset($options['options'])) {
                $ic->options = WmtsInstanceConfigurationOptions::fromArray($options['options']);
            }
        }
        return $ic;
    }
}
