<?php

namespace Mapbender\WmtsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A TileMatrixSet entity presents an OGC WMTS TileMatrixSet.
 * @ ORM\Entity
 * @ ORM\Table(name="mb_wmts_tilematrixset")
 * O RM\DiscriminatorMap({"mb_wmts_tilematrixset" = "TileMatrixSet"})
 */
class TileMatrixSet
{

    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="WmtsSource",inversedBy="tilematrixsets")
     * @ORM\JoinColumn(name="wmtssource", referencedColumnName="id")
     */
    protected $source;

    /**
     * @ORM\Column(type="string",nullable=false)
     */
    protected $identifier;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $abstract;

    /**
     * @ORM\Column(type="string",nullable=false)
     */
    protected $supportedCrs;

    /**
     * @var ArrayCollections A list of tilematrixsets
     * @ORM\OneToMany(targetEntity="TilematrixSet",mappedBy="source", cascade={"persist","remove"})
     * @ORM\OrderBy({"id" = "asc"})
     */
    protected $tilematrixes;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $wellknowscaleset;

    /**  @var string keyword ??? */
    protected $keyword;

    /**  @var array $tilematrixes */
    protected $boundingbox = array();

    /**
     * Create an instance of TileMatrixSet
     * 
     * @param type $tilematrixset 
     */
    public function __construct()
    {
        $this->tilematrixes = array();
    }
    
    /**
     * Get id
     * 
     * @return integer TileMatrixSet id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get supportedCrs
     * 
     * @return string supportedCrs
     */
    public function getSupportedCrs()
    {
        return $this->supportedCrs;
    }
    
    /**
     * Set supportedCrs
     * 
     * @param string $supportedCrs
     * @return \Mapbender\WmtsBundle\Entity\TileMatrixSet
     */
    public function setSupportedCrs($supportedCrs)
    {
        $this->supportedCrs = $supportedCrs;
        return $this;
    }

        /**
     * Get title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     * @param string $value 
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * Get abstract
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set abstract
     * @param string $value 
     */
    public function setAbstract($value)
    {
        $this->abstract = $value;
    }

    /**
     * Get identifier
     * 
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set identifier
     * 
     * @param string $value 
     */
    public function setIdentifier($value)
    {
        $this->identifier = $value;
    }

    /**
     * Get keyword
     * 
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set keyword
     * 
     * @param string $value 
     */
    public function setKeyword($value)
    {
        $this->keyword = $value;
    }

    /**
     * Get suppertedsrs
     * 
     * @return string
     */
    public function getSupportedSRS()
    {
        return $this->supportedsrs;
    }

    /**
     * Set supportedsrs
     * 
     * @param string $value 
     */
    public function setSupportedSRS($value)
    {
        $this->supportedsrs = $value;
    }
    /**
     * Get wellknowscaleset
     * 
     * @return string
     */
    public function getWellknowscaleset()
    {
        return $this->wellknowscaleset;
    }

    /**
     * Set wellknowscaleset
     * 
     * @param string $value 
     */
    public function setWellknowscaleset($value)
    {
        $this->wellknowscaleset = $value;
    }

    /**
     * Get Tilematrix as ArrayCollection of Tilematrix
     * 
     * @return array
     */
    public function getTilematrixes()
    {
        return $this->tilematrixes;
    }

    /**
     * Set tilematrix: ArrayCollection of Tilematrix
     * 
     * @param ArrayCollection $tilematrixes 
     */
    public function setTilematrixes($tilematrixes)
    {
        $this->tilematrixes = $tilematrixes;
    }

    /**
     * Add to tilematrix TileMatrix or Tilematrix as array
     * 
     * @param $tilematrix 
     */
    public function addTilematrix($tilematrix)
    {
        if ($tilematrix instanceof TileMatrix) {
            $this->tilematrixes->add($tilematrix);
        } else if (is_array($tilematrix)) {
            $this->tilematrixes->add(new TileMatrix($tilematrix));
        }
    }

    /**
     * Get boundingbox
     * 
     * @return array
     */
    public function getBoundingbox()
    {
        return $this->boundingbox;
    }

    /**
     * Set boundingbox:
     * 
     * @param array $boundingbox
     */
    public function setBoundingbox($boundingbox)
    {
        $this->boundingbox = $boundingbox;
    }

    /**
     * Get TilematrixSet as array of string inc. TileMatrixes
     * 
     * @return array
     */
    public function getAsArray()
    {
        $tilematrixset = array();
        $tilematrixset["title"] = $this->getTitle();
        $tilematrixset["abstract"] = $this->getAbstract();
        $tilematrixset["identifier"] = $this->getIdentifier();
        $tilematrixset["keyword"] = $this->getKeyword();
        $tilematrixset["supportedsrs"] = $this->getSupportedSRS();
        $tilematrixset["wellknowscaleset"] = $this->getBoundingbox();
        $tilematrix = array();
        foreach ($this->getTilematrix() as $tilematrixObj) {
            $tilematrix[] = $tilematrixObj->toArray();
        }
        $tilematrixset["tilematrixes"] = $tilematrix;
        return $tilematrixset;
    }

}
