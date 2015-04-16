<?php

namespace Mapbender\WmtsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Mapbender\CoreBundle\Component\Utils;
use Mapbender\CoreBundle\Entity\Contact;
use Mapbender\CoreBundle\Entity\Keyword;
use Mapbender\CoreBundle\Entity\Source;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A WmtsSource entity presents an OGC WMTS.
 * @ORM\Entity
 * @ORM\Table(name="mb_wmts_wmtssource")
 * ORM\DiscriminatorMap({"mb_wmts_wmtssource" = "WmtsSource"})
 */
class WmtsSource extends Source
{

    /**
     * @var string An origin WMTS URL
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    protected $originUrl = "";

    /**
     * @var string A WMTS version
     * @ORM\Column(type="string", nullable=true)
     */
    protected $version = "";

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fees = "";

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $accessConstraints = "";

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $serviceType = "";

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $serviceProviderSite = "";

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $serviceProviderName = "";

    /**
     * @var Contact A contact.
     * @ORM\OneToOne(targetEntity="Mapbender\CoreBundle\Entity\Contact", cascade={"persist","remove"})
     */
    protected $contact;

    /**
     * @var ArrayCollections A list of WMTS keywords
     * @ORM\OneToMany(targetEntity="Mapbender\CoreBundle\Entity\Keyword",mappedBy="id", cascade={"persist"})
     */
    protected $keywords;

    /**
     * @var RequestInformation A request information for the GetCapabilities operation
     * @ORM\Column(type="object", nullable=true)
     */
    public $getCapabilities = null;

    /**
     * @var RequestInformation A request information for the GetTile operation
     * @ORM\Column(type="object", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $getTile = null;

    /**
     * @var RequestInformation A request information for the GetFeatureInfo operation
     * @ORM\Column(type="object", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $getFeatureInfo = null;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $serviceMetadataURL = "";

    /**
     * @ORM\Column(type="array",nullable=true);
     */
    protected $theme = null;

    /**
     * @ var ArrayCollections A list of WMTS layers
     * @ ORM\OneToMany(targetEntity="TileMatrixSet",mappedBy="source", cascade={"persist","remove"})
     * @ ORM\OrderBy({"id" = "asc"})
     */
    protected $tilematrixsets;

    /**
     * @ORM\Column(type="text",nullable=true);
     */
    protected $username = null;

    /**
     * @ORM\Column(type="text",nullable=true);
     */
    protected $password = null;

    /**
     * @var ArrayCollections A list of WMTS layers
     * @ORM\OneToMany(targetEntity="WmtsLayerSource",mappedBy="source", cascade={"persist","remove"})
     * @ORM\OrderBy({"priority" = "asc","id" = "asc"})
     */
    protected $layers;

    /**
     * @var ArrayCollections A list of WMTS instances
     * @ORM\OneToMany(targetEntity="WmtsInstance",mappedBy="source", cascade={"persist","remove"})
     */
    protected $instances;

    /**
     * Create an instance of WMTSService
     */
    public function __construct()
    {
        parent::__construct();
//        $this->keywords = new ArrayCollection();
        $this->layers = new ArrayCollection();
        
//        $this->exceptionFormats = array();
//        $this->tilematrixsets = new ArrayCollection();
        $this->theme = array();
    }

    public function getType()
    {
        return "WMTS";
    }

    public function getManagertype()
    {
        return "wmts";
    }

    /**
     * Set originUrl
     *
     * @param string $originUrl
     * @return WmTsSource
     */
    public function setOriginUrl($originUrl)
    {
        $this->originUrl = $originUrl;
        return $this;
    }

    /**
     * Get originUrl
     * @return strin
     */
    public function getOriginUrl()
    {
        return $this->originUrl;
    }

    /**
     * Set version
     * @param type $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set alias
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * Get alias
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set fees
     * @param string $fees
     */
    public function setFees($fees)
    {
        $this->fees = $fees;
    }

    /**
     * Get fees
     * @return string
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Get accessConstraints
     * @param string $accessConstraints
     */
    public function setAccessConstraints($accessConstraints)
    {
        $this->accessConstraints = $accessConstraints;
    }

    /**
     * Get accessConstraints
     * @return string
     */
    public function getAccessConstraints()
    {
        return $this->accessConstraints;
    }

    /**
     * Set serviceType
     * @param string $serviceType
     */
    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;
    }

    /**
     * Get serviceType
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }
//
//    /**
//     * Get root layer
//     * @return WMTSLayer
//     */
//    public function getRootLayer()
//    {
//        return $this->getLayer()->get(0);
//    }
//
//    /**
//     * returns all Layers of the WMTS as comma-seperated string so that they can be used in a WMTS Request's LAYER parameter
//     */
//    public function getAllLayerNames($grouplayers = null)
//    {
//        $grouplayers = $grouplayers == null ? $this->getLayer() : $grouplayers;
//        $names = "";
//        foreach ($grouplayers as $layer) {
//            $name = $layer->getName();
//            if ($name != "") {
//                $names .= $name;
//            }
//            $names .= "," . $this->getAllLayerNames($layer->getLayer());
//        }
//        return trim($names, ",");
//    }
//
//    /**
//     * returns all Layers of the WMTS as comma-seperated string so that they can be used in a WMTS Request's LAYER parameter
//     */
//    public function getAllLayer($grouplayers = null, &$layers = array())
//    {
//        $grouplayers = $grouplayers == null ? $this->getLayer() : $grouplayers;
//        foreach ($grouplayers as $layer) {
//            $layers[] = $layer;
//            $this->getAllLayer($layer->getLayer(), $layers);
//        }
//        return $layers;
//    }

    /**
     * Set serviceProviderSite
     *
     * @param string $serviceProviderSite
     */
    public function setServiceProviderSite($serviceProviderSite)
    {
        $this->serviceProviderSite = $serviceProviderSite;
    }

    /**
     * Get serviceProviderSite
     *
     * @return string 
     */
    public function getServiceProviderSite()
    {
        return $this->serviceProviderSite;
    }

    /**
     * Set Contact
     *
     * @param Contact $contact
     * @return WmtsSource 
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    /**
     * Get Contact
     *
     * @return Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set GetCapabilities
     * @param type $getCapabilites
     */
    public function setGetCapabilities($getCapabilites)
    {
        $this->getCapabilites = $getCapabilites;
    }

    /**
     * Get requestGetCapabilitiesGETREST
     *
     * @return string
     */
    public function getGetCapabilities()
    {
        return $this->getCapabilites;
    }

    /**
     * Set GetTile
     * @param string $getTile
     */
    public function setGetTile($getTile)
    {
        $this->getTile = $getTile;
    }

    /**
     * Get GetTile
     * @return string
     */
    public function getGetTile()
    {
        return $this->getTile;
    }

    /**
     * Set GetFeatureInfo
     * @param string $getFeatureInfo
     */
    public function setGetFeatureInfo($getFeatureInfo)
    {
        $this->getFeatureInfo = $getFeatureInfo;
    }

    /**
     * Get requestGetFeatureInfo
     * @return string
     */
    public function getGetFeatureInfo()
    {
        return $this->getFeatureInfo;
    }

    /**
     * Set serviceMetadataURL
     * 
     * @param string $serviceMetadataURL
     */
    public function setServiceMetadataURL($serviceMetadataURL)
    {
        $this->serviceMetadataURL = $serviceMetadataURL;
    }

    /**
     * Get serviceMetadataURL
     * 
     * @return string
     */
    public function getServiceMetadataURL()
    {
        return $this->serviceMetadataURL;
    }

    /**
     * Get theme
     * @return array
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get theme as ArrayCollection of Theme
     * @return ArrayCollection
     */
    public function getThemeAsObjects()
    {
        $array = new ArrayCollection();
        foreach ($this->theme as $theme) {
            $array->add(new Theme($theme));
        }
        return $array;
    }

    /**
     * Set theme
     * @param array of Theme or Theme->getAsArray $themes
     */
    public function setTheme($themes)
    {
        if ($themes === null) {
            $this->theme = $themes;
        } else if (count($themes) > 0) {
            if (is_array($themes[0])) {
                $this->theme = $themes;
            } else if ($themes[0] instanceof Theme) {
                foreach ($themes as $theme) {
                    $this->theme[] = $theme->getAsArray();
                }
            }
        } else {
            $this->theme = $themes;
        }
    }

    /**
     * Add $theme to theme
     * 
     * @param Theme or array $theme 
     */
    public function addTheme($theme)
    {
        if (is_array($theme)) {
            $this->theme[] = $theme;
        } else if ($theme instanceof Theme) {
            $this->theme[] = $theme->getAsArray();
        }
    }

    /**
     * Get tilematrixset
     * 
     * @return array 
     */
    public function getTileMatrixSet()
    {
        return $this->tilematrixset;
    }

    /**
     * Get tilematrixset
     * 
     * @return array 
     */
    public function getTileMatrixSetAsObjects()
    {
        $array = new ArrayCollection();
        foreach ($this->tilematrixset as $tilematrixset) {
            $array->add(new TileMatrixSet($tilematrixset));
        }
        return $array;
    }

    /**
     * Get tilematrixset
     * @param string or array $name 
     * @return array 
     */
    public function getTileMatrixSetByName($name)
    {
        $array = array();
        foreach ($this->tilematrixset as $tilematrixset) {
            if (is_string($name)) {
                if ($tilematrixset["title"] == $name) {
                    $array[$name] = $tilematrixset;
                }
            } else if (is_array($name)) {
                foreach ($name as $name_) {
                    if ($tilematrixset["title"] == $name_) {
                        $array[$name_] = $tilematrixset;
                    }
                }
            }
        }
        return $array;
    }

    /**
     * Set tilematrixset
     *
     * @param array $tilematrixset 
     */
    public function setTtilematrixset($tilematrixset)
    {
        $this->tilematrixset = $tilematrixset;
        if ($tilematrixset === null) {
            $this->tilematrixset = $tilematrixset;
        } else if (count($tilematrixset) > 0) {
            if (is_array($tilematrixset[0])) {
                $this->tilematrixset = $tilematrixset;
            } else if ($tilematrixset[0] instanceof TileMatrixSet) {
                foreach ($tilematrixset as $tilematrixset_) {
                    $this->tilematrixset = array();
                    $this->tilematrixset[] = $tilematrixset_->getAsArray();
                }
            }
        } else {
            $this->tilematrixset = $tilematrixset;
        }
    }

    /**
     * Add tilematrixset
     * @param TilematrixSet or array $tilematrixset 
     */
    public function addTtilematrixset($tilematrixset)
    {
        if (is_array($tilematrixset)) {
            $this->tilematrixset[] = $tilematrixset;
        } else if ($tilematrixset instanceof TileMatrixSet) {
            $this->tilematrixset[] = $tilematrixset->getAsArray();
        }
    }

    /**
     * Get username
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     * 
     * @param string $username 
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get password
     * 
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     * 
     * @param string $password 
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return $this->identifier ? $this->identifier : $this->originUrl;
    }

    /**
     * @inheritdoc
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }
}
