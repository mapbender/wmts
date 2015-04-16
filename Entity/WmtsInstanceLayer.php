<?php

namespace Mapbender\WmtsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Mapbender\CoreBundle\Component\SourceInstanceItem;
use Mapbender\CoreBundle\Component\SourceItem;
use Mapbender\CoreBundle\Entity\SourceInstance;
use Mapbender\WmtsBundle\Entity\WmtsInstance;
use Mapbender\WmtsBundle\Entity\WmtsLayerSource;
use Mapbender\CoreBundle\Component\Utils;

/**
 * WmtsInstanceLayer class
 *
 * @author Paul Schmidt
 *
 * @ORM\Entity
 * @ORM\Table(name="mb_wmts_wmtsinstancelayer")
 */
class WmtsInstanceLayer extends SourceInstanceItem
{

    /**
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="WmtsInstance", inversedBy="layers", cascade={"refresh"})
//     * @ORM\JoinColumn(name="wmtsinstance", referencedColumnName="id")
//     */
//    protected $sourceInstance;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="WmtsLayerSource", inversedBy="id", cascade={"refresh"})
//     * @ORM\JoinColumn(name="wmtslayersource", referencedColumnName="id")
//     */
//    protected $sourceItem;
//
//    /**
//     * @ORM\ManyToOne(targetEntity="WmtsInstanceLayer",inversedBy="sublayer")
//     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
//     */
//    protected $parent = null;
//
//    /**
//     * @ORM\OneToMany(targetEntity="WmtsInstanceLayer",mappedBy="parent", cascade={"remove"})
//     * @ORM\OrderBy({"priority" = "asc"})
//     */
//    protected $sublayer;
//
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $title;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $active = true;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $allowselected = true;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $selected = true;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $info;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $allowinfo;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $toggle;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $allowtoggle;
//
//    /**
//     * @ORM\Column(type="boolean", nullable=true)
//     */
//    protected $allowreorder = true;
//
//    /**
//     * @ORM\Column(type="float", nullable=true)
//     */
//    protected $minScale;
//
//    /**
//     * @ORM\Column(type="float", nullable=true)
//     */
//    protected $maxScale;
//
//    /**
//     * @ORM\Column(type="string", nullable=true)
//     */
//    protected $style = "";
//
//    /**
//     * @ORM\Column(type="integer", nullable=true)
//     */
//    protected $priority;

    public function __construct()
    {
        $this->sublayer = new ArrayCollection();
        $this->style = "";
    }

    /**
     * Set id
     * @param integer $id
     * @return WmtsInstanceLayer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return WmtsInstanceLayer
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set sublayer as array of string
     *
     * @param array $sublayer
     * @return WmtsInstanceLayer
     */
    public function setSublayer($sublayer)
    {
        $this->sublayer = $sublayer;
        return $this;
    }

    /**
     * Set sublayer as array of string
     *
     * @param WmtsInstanceLayer $sublayer
     * @return WmtsInstanceLayer
     */
    public function addSublayer(WmtsInstanceLayer $sublayer)
    {
        $this->sublayer->add($sublayer);
        return $this;
    }

    /**
     * Get sublayer
     *
     * @return array
     */
    public function getSublayer()
    {
        return $this->sublayer;
    }

    /**
     * Set parent
     *
     * @param WmtsInstanceLayer $parent
     * @return WmtsInstanceLayer
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return WmtsInstanceLayer
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return WmtsInstanceLayer
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set allowselected
     *
     * @param boolean $allowselected
     * @return WmtsInstanceLayer
     */
    public function setAllowselected($allowselected)
    {
        $this->allowselected = $allowselected;
        return $this;
    }

    /**
     * Get allowselected
     *
     * @return boolean
     */
    public function getAllowselected()
    {
        return $this->allowselected;
    }

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return WmtsInstanceLayer
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set info
     *
     * @param boolean $info
     * @return WmtsInstanceLayer
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Get info
     *
     * @return boolean
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get toggle
     *
     * @return boolean $toggle
     */
    public function getToggle()
    {
        return $this->toggle;
    }

    /**
     * Set toggle
     *
     * @param string $toggle
     */
    public function setToggle($toggle)
    {
        $this->toggle = $toggle;
        return $this;
    }

    /**
     * Set allowinfo
     *
     * @param boolean $allowinfo
     * @return WmtsInstanceLayer
     */
    public function setAllowinfo($allowinfo)
    {
        $this->allowinfo = $allowinfo;
        return $this;
    }

    /**
     * Get allowinfo
     *
     * @return boolean
     */
    public function getAllowinfo()
    {
        return $this->allowinfo;
    }

    /**
     * Get allowtoggle
     *
     * @return boolean $allowtoggle
     */
    public function getAllowtoggle()
    {
        return $this->allowtoggle;
    }

    /**
     * Set allowtoggle
     *
     * @param boolean $allowtoggle
     */
    public function setAllowtoggle($allowtoggle)
    {
        $this->allowtoggle = $allowtoggle;
        return $this;
    }

    /**
     * Get allowreorder
     *
     * @return boolean $allowreorder
     */
    public function getAllowreorder()
    {
        return $this->allowreorder;
    }

    /**
     * Set allowreorder
     *
     * @param boolean $allowreorder
     */
    public function setAllowreorder($allowreorder)
    {
        $this->allowreorder = $allowreorder;
        return $this;
    }

    /**
     * Set minScale
     *
     * @param float $minScale
     * @return WmtsInstanceLayer
     */
    public function setMinScale($minScale)
    {
        $this->minScale = $minScale;
        return $this;
    }

    /**
     * Get minScale
     *
     * @return float
     */
    public function getMinScale()
    {
        return $this->minScale;
    }

    /**
     * Set maxScale
     *
     * @param float $maxScale
     * @return WmtsInstanceLayer
     */
    public function setMaxScale($maxScale)
    {
        $this->maxScale = $maxScale;
        return $this;
    }

    /**
     * Get maxScale
     *
     * @return float
     */
    public function getMaxScale()
    {
        return $this->maxScale;
    }

    /**
     * Set style
     *
     * @param string $style
     * @return WmtsInstanceLayer
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return WmtsInstanceLayer
     */
    public function setPriority($priority)
    {
        if ($priority !== null)
            $this->priority = intval($priority);
        else
            $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setSourceInstance(SourceInstance $sourceInstance = NULL)
    {
        $this->sourceInstance = $sourceInstance;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSourceInstance()
    {
        return $this->sourceInstance;
    }


    /**
     * @inheritdoc
     */
    public function getSourceItem()
    {
        return $this->sourceItem;
    }

    /**
     * @inheritdoc
     */
    public function setSourceItem(SourceItem $sourceItem)
    {
        $this->sourceItem = $sourceItem;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     */
    public function copy(EntityManager $em)
    {
        $inlay = new WmtsInstanceLayer();
        $inlay->title = $this->title;
        $inlay->active = $this->active;
        $inlay->allowselected = $this->allowselected;
        $inlay->selected = $this->selected;
        $inlay->info = $this->info;
        $inlay->allowinfo = $this->allowinfo;
        $inlay->toggle = $this->toggle;
        $inlay->allowtoggle = $this->allowtoggle;
        $inlay->allowreorder = $this->allowreorder;
        $inlay->minScale = $this->minScale;
        $inlay->maxScale = $this->maxScale;
        $inlay->style = $this->style;
        $inlay->priority = $this->priority;
        return $inlay;
    }

}
