<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Mapbender\WmtsBundle\Entity;

use Mapbender\CoreBundle\Component\ContainingKeyword;
use Mapbender\CoreBundle\Component\SourceItem;
use Mapbender\CoreBundle\Entity\Source;

/**
 * Description of WmtsLayerSource
 * @author Pau Schmidt
 * @ORM\Entity
 * @ORM\Table(name="mb_wms_wmslayersource")
 */
class WmtsLayerSource extends SourceItem # implements ContainingKeyword
{
    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title = "";

    /**
     * @ORM\Column(name="name", type="string", nullable="true")
     */
    protected $identifier = "";

    /**
     * @ORM\Column(type="text",nullable="true")
     */
    protected $abstract = "";

    /**
     * @ORM\ManyToOne(targetEntity="WmtsSource",inversedBy="layers")
     * @ORM\JoinColumn(name="wmssource", referencedColumnName="id")
     */
    protected $source; # change this variable name together with "get" "set" functions (s. SourceItem too)

//    /**
//     * @ORM\Column(type="object", nullable=true)
//     */
//    public $latlonBounds;
//
//    /**
//     * @ORM\Column(type="array", nullable=true)
//     */
//    public $boundingBoxes;
//
//    public $crs;
//
//    public $crsBounds;
//
//    public $metadataUrl;
//
//    public $styles;


    /**
     * @ORM\Column(type="array", nullable="true")
     */
    public $formats;

    /**
     * @ORM\Column(type="array", nullable="true")
     */
    protected $resourceUrl;



    public function __construct()
    {
        $this->layer = new ArrayCollection();
        $this->keywords = new ArrayCollection();
//        $this->boundingBoxes = array();
//        $this->metadataUrl = array();
//        $this->dimension = array();
//        $this->dataUrl = array();
//        $this->featureListUrl = array();
//        $this->styles = array();
//        $this->srs = array();
//        $this->identifier = array();
        $this->resourceUrl = array();
    }
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get identifier
     *
     * @return string $identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set abstract
     *
     * @param string $abstract
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * Get abstract
     *
     * @return string $abstract
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @inheritdoc
     */
    public function setSource(Source $wmssource)
    {
        $this->source = $wmssource;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSource()
    {
        return $this->source;
    }





    /**
     * Set resourceUrl
     * @param array $resourceUrls
     * @return \Mapbender\WmtsBundle\Entity\WmtsLayerSource
     */
    public function setResourceUrl(array $resourceUrls = array())
    {
        $this->resourceUrl = $resourceUrls;
        return $this;
    }

    /**
     * Add resourceUrl
     * @return string resourceUrl
     * @return \Mapbender\WmtsBundle\Entity\WmtsLayerSource
     */
    public function addResourceUrl($resourceUrl)
    {
        $this->resourceUrl[] = $resourceUrl;
        return $this;
    }

    /**
     * Get resourceUrl
     *
     * @return array resourceUrl
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }
}
