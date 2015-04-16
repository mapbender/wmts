<?php

namespace Mapbender\WmtsBundle\Component;

use Doctrine\Common\Collections\ArrayCollection;
use Mapbender\CoreBundle\Component\BoundingBox;
use Mapbender\CoreBundle\Entity\Contact;
use Mapbender\WmtsBundle\Entity\LegendUrl;
use Mapbender\WmtsBundle\Entity\Style;
use Mapbender\WmtsBundle\Entity\WmtsSource;
use Mapbender\WmtsBundle\Entity\WmtsSourceKeyword;
use Mapbender\WmtsBundle\Entity\WmtsLayerSource;
use Mapbender\WmtsBundle\Entity\RequestInformation;

/**
 * Class that Parses WMTS 1.1.0 GetCapabilies Document
 * @package Mapbender
 * @author Paul Schmidt
 */
class WmtsCapabilitiesParser100 extends WmtsCapabilitiesParser
{

    /**
     * Creates an instance
     * @param \DOMDocument $doc
     */
    public function __construct(\DOMDocument $doc)
    {
        parent::__construct($doc);

        foreach ($this->xpath->query('namespace::*', $this->doc->documentElement) as $node) {
            $nsPrefix = $node->prefix;
            $nsUri    = $node->nodeValue;
            if ($nsPrefix == "" && $nsUri == "http://www.opengis.net/wmts/1.0") {
                $nsPrefix = "wmts";
            }
            $this->xpath->registerNamespace($nsPrefix, $nsUri);
        }
    }

    /**
     * Parses the GetCapabilities document
     * @return \Mapbender\WmtsBundle\Entity\WmtsSource
     */
    public function parse()
    {
        $wmtssource = new WmtsSource();
        $root       = $this->doc->documentElement;

        $wmtssource->setVersion($this->getValue("./@version", $root));
        $this->parseServiceIdentification($wmtssource, $this->getValue("./ows:ServiceIdentification", $root));
        $this->parseServiceProvider($wmtssource, $this->getValue("./ows:ServiceProvider", $root));
        $this->parseCapabilityRequest($wmtssource, $this->getValue("./ows:OperationsMetadata", $root));

        $serviceMetadataUrl = $this->getValue("./wmts:ServiceMetadataURL/@xlink:href", $root);
        $wmtssource->setServiceMetadataURL($serviceMetadataUrl);

        $layerElms = $this->xpath->query("./wmts:Contents/wmts:Layer", $root);
        foreach ($layerElms as $layerEl) {
            $layer = new WmtsLayerSource();
            $layer->setSource($wmtssource);
            $wmtssource->addLayer($layer);
            $this->parseLayer($layer, $layerEl);
        }

//        foreach ($capabilities as $capabilityEl) {
//            if ($capabilityEl->localName === "Request") {
//                $this->parseCapabilityRequest($wmts, $capabilityEl);
//            } elseif ($capabilityEl->localName === "Exception") {
//                $this->parseCapabilityException($wmts, $capabilityEl);
//            } elseif ($capabilityEl->localName === "Layer") {
//                $rootlayer = new WmtsLayerSource();
//                $wmts->addLayer($rootlayer);
//                $layer = $this->parseLayer($wmts, $rootlayer, $capabilityEl);
//            }
//            /* parse wmts:_ExtendedOperation  */ elseif ($capabilityEl->localName ===
//                "UserDefinedSymbolization") {
//                $this->parseUserDefinedSymbolization($wmts, $capabilityEl);
//            }
//            /* @TODO add other wmts:_ExtendedOperation ?? */
//        }
        return $wmtssource;
    }

    /**
     * Parses the ServiceIdentification section of the GetCapabilities document
     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
     * @param \DOMElement $contextElm the element to use as context for the ServiceIdentification section
     */
    private function parseServiceIdentification(WmtsSource $wmts, \DOMElement $contextElm)
    {
        $wmts->setTitle($this->getValue("./ows:Title/text()", $contextElm));
        $wmts->setDescription($this->getValue("./ows:Abstract/text()", $contextElm));

        $keywordElList = $this->xpath->query("./ows:KeywordList/ows:Keyword", $contextElm);
        $keywords      = new ArrayCollection();
        foreach ($keywordElList as $keywordEl) {
            $keyword = new WmtsSourceKeyword();
            $keyword->setValue(trim($this->getValue("./text()", $keywordEl)));
            $keyword->setReferenceObject($wmts);
            $keywords->add($keyword);
        }
        $wmts->setServiceType($this->getValue("./ows:ServiceType/text()", $contextElm));
        $wmts->setFees($this->getValue("./ows:Fees/text()", $contextElm));
        $wmts->setAccessConstraints($this->getValue("./ows:AccessConstraints/text()", $contextElm));
    }

    /**
     * Parses the ServiceProvider section of the GetCapabilities document
     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
     * @param \DOMElement $contextElm the element to use as context for the ServiceProvider section.
     */
    private function parseServiceProvider(WmtsSource $wmts, \DOMElement $contextElm)
    {
        $wmts->setServiceProviderSite($this->getValue("./wmts:OnlineResource/@xlink:href", $contextElm));
        $contact = new Contact();
        $contact->setOrganization($this->getValue("./ows:ProviderName/text()", $contextElm));
        $contact->setPerson($this->getValue("./ows:ServiceContact/ows:IndividualName/text()", $contextElm));
        $contact->setPosition($this->getValue("./ows:ServiceContact/ows:PositionName/text()", $contextElm));
        $contact->setVoiceTelephone(
            $this->getValue("./ows:ServiceContact/ows:ContactInfo/ows:Phone/ows:Voice/text()", $contextElm)
        );
        $contact->setFacsimileTelephone(
            $this->getValue("./ows:ServiceContact/ows:ContactInfo/ows:Phone/ows:Facsimile/text()", $contextElm)
        );
        $contact->setAddress(
            $this->getValue("./wmts:ContactInformation/wmts:ContactAddress/wmts:Address/text()", $contextElm)
        );
        $contact->setAddressCity(
            $this->getValue("./ows:ServiceContact/ows:ContactInfo/ows:Address/ows:DeliveryPoint/text()", $contextElm)
        );
        $contact->setAddressStateOrProvince(
            $this->getValue(
                "./ows:ServiceContact/ows:ContactInfo/ows:Address/ows:AdministrativeArea/text()", $contextElm
            )
        );
        $contact->setAddressPostCode(
            $this->getValue("./ows:ServiceContact/ows:ContactInfo/ows:Address/ows:PostalCode/text()", $contextElm)
        );
        $contact->setAddressCountry(
            $this->getValue("./ows:ServiceContact/ows:ContactInfo/ows:Address/ows:Country/text()", $contextElm)
        );
        $contact->setElectronicMailAddress(
            $this->getValue(
                "./ows:ServiceContact/ows:ContactInfo/ows:Address/ows:ElectronicMailAddress/text()", $contextElm
            )
        );
        $wmts->setContact($contact);
    }

    /**
     * Parses the Capabilities Request section of the GetCapabilities document
     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
     * @param \DOMElement $contextElm the element to use as context for the
     * Capabilities Request section
     */
    private function parseCapabilityRequest(WmtsSource $wmts, \DOMElement $contextElm)
    {
        $operations = $this->xpath->query("./*", $contextElm);
        foreach ($operations as $operation) {
            $name = $this->getValue("./@name", $operation);
            if ($name === "GetCapabilities") {
                $getCapabilities = $this->parseOperationRequestInformation($operation);
                $wmts->setGetCapabilities($getCapabilities);
            } elseif ($name === "GetTile") {
                $getTile = $this->parseOperationRequestInformation($operation);
                $wmts->setGetTile($getTile);
            } elseif ($name === "GetFeatureInfo") {
                $getFeatureInfo = $this->parseOperationRequestInformation($operation);
                $wmts->setGetFeatureInfo($getFeatureInfo);
            }
        }
    }

    /**
     * Parses the Operation Request Information section of the GetCapabilities
     * document.
     * @param \DOMElement $contextElm the element to use as context for the
     * Operation Request Information section
     * @return RequestInformation
     */
    private function parseOperationRequestInformation(\DOMElement $contextElm)
    {
        $ri       = new RequestInformation();
        $tempList = $this->xpath->query("./wmts:Format", $contextElm);
        if ($tempList !== null) {
            foreach ($tempList as $item) {
                $ri->addFormat($this->getValue("./text()", $item));
            }
        }
        $ri->setHttpGetRestful(
            $this->getValue(
                "./ows:DCP/ows:HTTP/ows:Get[./ows:Constraint/ows:AllowedValues/ows:Value/text()='RESTful']/@xlink:href",
                $contextElm
            )
        );
        $ri->setHttpGetKvp(
            $this->getValue(
                "./ows:DCP/ows:HTTP/ows:Get[./ows:Constraint/ows:AllowedValues/ows:Value/text()='KVP']/@xlink:href",
                $contextElm
            )
        );
        // TODO $ri->setHttpPost ?
        return $ri;
    }

//
//    /**
//     * Parses the Capability Exception section of the GetCapabilities
//     * document.
//     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
//     * @param \DOMElement $contextElm the element to use as context for the
//     * Capability Exception section
//     */
//    private function parseCapabilityException(WmtsSource $wmts, \DOMElement $contextElm)
//    {
//        $tempList = $this->xpath->query("./wmts:Format", $contextElm);
//        if ($tempList !== null) {
//            foreach ($tempList as $item) {
//                $wmts->addExceptionFormat($this->getValue("./text()", $item));
//            }
//        }
//    }
//
//    /**
//     * Parses the UserDefinedSymbolization section of the GetCapabilities
//     * document
//     *
//     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
//     * @param \DOMElement $contextElm the element to use as context for the
//     * UserDefinedSymbolization section
//     */
//    private function parseUserDefinedSymbolization(WmtsSource $wmts, \DOMElement $contextElm)
//    {
//        if ($contextElm !== null) {
//            $wmts->setSupportSld($this->getValue("./@SupportSLD", $contextElm));
//            $wmts->setUserLayer($this->getValue("./@UserLayer", $contextElm));
//            $wmts->setUserStyle($this->getValue("./@UserStyle", $contextElm));
//            $wmts->setRemoteWfs($this->getValue("./@RemoteWFS", $contextElm));
//            $wmts->setInlineFeature($this->getValue("./@InlineFeature", $contextElm));
//            $wmts->setRemoteWcs($this->getValue("./@RemoteWCS", $contextElm));
//        }
//    }

    private function parseLayer(WmtsLayerSource $wmtslayer, \DOMElement $contextElm)
    {
//        foreach($layerlist as $layerEl) {
//                $layer = new WmtsLayerDetail();
//                $layer->setName($node->nodeValue); ???
        $wmtslayer->setTitle($this->getValue("./ows:Title/text()", $contextElm));
        $wmtslayer->setAbstract($this->getValue("./ows:Abstract/text()", $contextElm));

        $latlonbboxEl = $this->getValue("./ows:WGS84BoundingBox", $contextElm);
        if ($latlonbboxEl !== null) {
            $latlonBounds = new BoundingBox();
            $bounds       = explode(
                " ",
                $this->getValue("./ows:LowerCorner/text()", $latlonbboxEl)
                . " " . $this->getValue("./ows:UpperCorner/text()", $latlonbboxEl)
            );
            $latlonBounds->setSrs("EPSG:4326");
            $latlonBounds
                ->setMinx($bounds[0])
                ->setMiny($bounds[1])
                ->setMaxx($bounds[2])
                ->setMaxy($bounds[3]);
            $wmtslayer->setLatlonBounds($latlonBounds);
        }

//
//
//                $crs = array();
//                $bounds = array();
////                <ows:BoundingBox crs="urn:ogc:def:crs:EPSG::25832">
////                    <ows:LowerCorner>280388.0 5235855.0</ows:LowerCorner>
////                    <ows:UpperCorner>921290.0 6101349.0</ows:UpperCorner>
////                </ows:BoundingBox>
//                $bboxesEl = $this->xpath->query("./ows:BoundingBox", $contextElm);
//                foreach($bboxesEl as $bboxEl) {
//                    $crsStr = $this->getValue("./@crs", $bboxEl);
//                    $crs[] = $crsStr;
//                    $bounds[$crsStr] = $this->getValue("./ows:BoundingBox/ows:LowerCorner/text()", $contextElm)
//                        ." ". $this->getValue("./ows:BoundingBox/ows:UpperCorner/text()", $contextElm);
//                }
//                $wmtslayer->setCrs($crs);
//                $wmtslayer->setCrsBounds($bounds);
//
//                $latlonbounds = $this->getValue("./ows:WGS84BoundingBox/ows:LowerCorner/text()", $contextElm)
//                        ." ". $this->getValue("./ows:WGS84BoundingBox/ows:UpperCorner/text()", $contextElm);
//                $wmtslayer->setLatLonBounds($latlonbounds);
//                $crs84 = $this->getValue("./ows:WGS84BoundingBox/@crs", $contextElm);
//                $wmtslayer->setCrsLatLon($crs84);
//                if(count($crs) == 0) {
//                    $wmtslayer->setDefaultCrs($this->getValue("./ows:WGS84BoundingBox/@crs", $contextElm));
//                }
//                unset($crs);
//                unset($crs84);
        $wmtslayer->setIdentifier($this->getValue("./ows:Identifier/text()", $contextElm));

//                $metadataUrlsEl = $this->xpath->query("./ows:Metadata", $contextElm);
//                $metadata = array();
//                foreach($metadataUrlsEl as $metadataUrlEl) {
//                    $metadata[] = $this->getValue("./xlink:href", $metadataUrlEl);
//                }
//                $wmtslayer->setMetadataURL($metadata);
//                unset($metadata);
//                unset($metadataUrlsEl);

        $stylesEl = $this->xpath->query("./wmts:Style", $contextElm);
        foreach ($stylesEl as $styleEl) {
            $style     = new Style();
            $style
                ->setTitle($this->getValue("./ows:Title/text()", $styleEl))
                ->setAbstract($this->getValue("./ows:Abstract/text()", $styleEl))
                ->setIdentifier($this->getValue("./ows:Identifier/text()", $styleEl))
                ->setIsdefault($this->getValue("./@isDefault", $styleEl));
            $legendurl = new LegendUrl();
            $legendurl->setFormat($this->getValue("./wmts:LegendURL/@format", $styleEl))
                ->setHref($this->getValue("./wmts:LegendURL/@xlink:href", $styleEl));
            if ($legendurl->getHref()) {
                $style->setLegendurl($legendurl);
            }
            $wmtslayer->addStyle($style);
        }
//                unset($stylesEl);

        $formatsEl = $this->xpath->query("./wmts:Format", $contextElm);
        foreach ($formatsEl as $formatEl) {
            $wmtslayer->addFormat($this->getValue("./text()", $formatEl));
        }
        $formatsEl = $this->xpath->query("./wmts:InfoFormat", $contextElm);
        foreach ($formatsEl as $formatEl) {
            $wmtslayer->addInfoformat($this->getValue("./text()", $formatEl));
        }

//                $tileMatrixSetLinks = array();
//                $tileMatrixSetLinksEl = $this->xpath->query("./wmts:TileMatrixSetLink", $contextElm);
//                foreach($tileMatrixSetLinksEl as $tileMatrixSetLinkEl) {
//                   //TODO set formats
//                    $tileMatrixSetLinks[] = $this->getValue("./wmts:TileMatrixSet/text()", $tileMatrixSetLinkEl);
//                }
//                $wmtslayer->setTileMatrixSetLink($tileMatrixSetLinks);
//                $resourceURL = array();
//                $resourceURLsEl = $this->xpath->query("./wmts:ResourceURL", $contextElm);
//                foreach($resourceURLsEl as $resourceURLEl) {
//                    $resourceURL[] = array(
//                        "format" => $this->getValue("./@format", $resourceURLEl),
//                        "resourceType" => $this->getValue("./@resourceType", $resourceURLEl),
//                        "template" => $this->getValue("./@template", $resourceURLEl));
//                }
//                $wmtslayer->setResourceURL($resourceURL);
//                $wmts->getLayer()->add($wmtslayer);
//            }
//            unset($layerlist);
    }
//
//    /**
//     * Parses the Layer section of the GetCapabilities document
//     *
//     * @param \Mapbender\WmtsBundle\Entity\WmtsSource $wmts the WmtsSource
//     * @param \Mapbender\WmtsBundle\Entity\WmtsLayerSource $wmtslayer the WmtsLayerSource
//     * @param \DOMElement $contextElm the element to use as context for the
//     * Layer section
//     * @return \Mapbender\WmtsBundle\Entity\WmtsLayerSource the created layer
//     */
//    private function parseLayerOld(WmtsSource $wmts, WmtsLayerSource $wmtslayer, \DOMElement $contextElm)
//    {
//        $wmtslayer->setQueryable($this->getValue("./@queryable", $contextElm));
//        $wmtslayer->setCascaded($this->getValue("./@cascaded", $contextElm));
//        $wmtslayer->setOpaque($this->getValue("./@opaque", $contextElm));
//        $wmtslayer->setNoSubset($this->getValue("./@noSubsets", $contextElm));
//        $wmtslayer->setFixedWidth($this->getValue("./@fixedWidth", $contextElm));
//        $wmtslayer->setFixedHeight($this->getValue("./@fixedHeight", $contextElm));
//
//        $wmtslayer->setName($this->getValue("./wmts:Name/text()", $contextElm));
//        $wmtslayer->setTitle($this->getValue("./wmts:Title/text()", $contextElm));
//        $wmtslayer->setAbstract($this->getValue("./wmts:Abstract/text()", $contextElm));
//
//        $keywordElList = $this->xpath->query("./wmts:KeywordList/wmts:Keyword", $contextElm);
////        foreach ($keywordElList as $keywordEl) {
////            $keyword = new Keyword();
////            $keyword->setValue(trim($this->getValue("./text()", $keywordEl)));
////            $keyword->setSourceclass($wmtslayer->getClassname());
////            $keyword->setSourceid($wmtslayer);
////
////            // FIXME: breaks sqlite
////            //$wmtslayer->addKeyword($keyword);
////        }
//
//        $tempList = $this->xpath->query("./wmts:CRS", $contextElm);
//        if ($tempList !== null) {
//            foreach ($tempList as $item) {
//                $wmtslayer->addSrs($this->getValue("./text()", $item));
//            }
//        }
//        $latlonbboxEl = $this->getValue("./wmts:EX_GeographicBoundingBox", $contextElm);
//        if ($latlonbboxEl !== null) {
//            $latlonBounds = new BoundingBox();
//            $latlonBounds->setSrs("EPSG:4326");
//            $latlonBounds->setMinx($this->getValue("./wmts:westBoundLongitude/text()", $latlonbboxEl));
//            $latlonBounds->setMiny($this->getValue("./wmts:southBoundLatitude/text()", $latlonbboxEl));
//            $latlonBounds->setMaxx($this->getValue("./wmts:eastBoundLongitude/text()", $latlonbboxEl));
//            $latlonBounds->setMaxy($this->getValue("./wmts:northBoundLatitude/text()", $latlonbboxEl));
//            $wmtslayer->setLatlonBounds($latlonBounds);
//        }
//
//        $tempList = $this->xpath->query("./wmts:BoundingBox", $contextElm);
//        if ($tempList !== null) {
//            foreach ($tempList as $item) {
//                $bbox = new BoundingBox();
//                $bbox->setSrs($this->getValue("./@CRS", $item));
//                $bbox->setMinx($this->getValue("./@minx", $item));
//                $bbox->setMiny($this->getValue("./@miny", $item));
//                $bbox->setMaxx($this->getValue("./@maxx", $item));
//                $bbox->setMaxy($this->getValue("./@maxy", $item));
//                $wmtslayer->addBoundingBox($bbox);
//            }
//        }
//
//        $attributionEl = $this->getValue("./wmts:Attribution", $contextElm);
//        if ($attributionEl !== null) {
//            $attribution = new Attribution();
//            $attribution->setTitle($this->getValue("./wmts:Title/text()", $attributionEl));
//            $attribution->setOnlineResource($this->getValue("./wmts:OnlineResource/text()", $attributionEl));
//
//            $logoUrl        = new LegendUrl();
//            $logoUrl->setHeight($this->getValue("./wmts:LogoURL/@height", $attributionEl));
//            $logoUrl->setWidth($this->getValue("./wmts:LogoURL/@width", $attributionEl));
//            $onlineResource = new OnlineResource();
//            $onlineResource->setHref($this->getValue("./wmts:LogoURL/wmts:OnlineResource/text()", $attributionEl));
//            $onlineResource->setFormat($this->getValue("./wmts:LogoURL/wmts:Format/text()", $attributionEl));
//            $logoUrl->setOnlineResource($onlineResource);
//            $attribution->setLogoUrl($logoUrl);
//            $wmtslayer->setAttribution($attribution);
//        }
//
//        $authorityList  = $this->xpath->query("./wmts:AuthorityURL", $contextElm);
//        $identifierList = $this->xpath->query("./wmts:Identifier", $contextElm);
//
//        if ($authorityList !== null) {
//            foreach ($authorityList as $authorityEl) {
//                $authority = new Authority();
//                $authority->setName($this->getValue("./@name", $authorityEl));
//                $authority->setUrl($this->getValue("./wmts:OnlineResource/text()", $authorityEl));
//                $wmtslayer->addAuthority($authority);
//            }
//        }
//        if ($identifierList !== null) {
//            foreach ($identifierList as $identifierEl) {
//                $identifier = new Identifier();
//                $identifier->setAuthority($this->getValue("./@authority", $identifierEl));
//                $identifier->setValue($this->getValue("./text()", $identifierEl));
//                $wmtslayer->setIdentifier($identifier);
//            }
//        }
//
//        $metadataUrlList = $this->xpath->query("./wmts:MetadataURL", $contextElm);
//        if ($metadataUrlList !== null) {
//            foreach ($metadataUrlList as $metadataUrlEl) {
//                $metadataUrl    = new MetadataUrl();
//                $onlineResource = new OnlineResource();
//                $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $metadataUrlEl));
//                $onlineResource->setHref($this->getValue("./wmts:OnlineResource/text()", $metadataUrlEl));
//                $metadataUrl->setOnlineResource($onlineResource);
//                $metadataUrl->setType($this->getValue("./@type", $metadataUrlEl));
//                $wmtslayer->addMetadataUrl($metadataUrl);
//            }
//        }
//
//        $dimentionList = $this->xpath->query("./wmts:Dimension", $contextElm);
//        if ($dimentionList !== null) {
//            foreach ($dimentionList as $dimensionEl) {
//                $dimension = new Dimension();
//                $dimension->setName($this->getValue("./@name", $dimensionEl)); //($this->getValue("./@CRS", $item));
//                $dimension->setUnits($this->getValue("./@units", $dimensionEl));
//                $dimension->setUnitSymbol($this->getValue("./@unitSymbol", $dimensionEl));
//                $dimension->setDefault($this->getValue("./@default", $dimensionEl));
//                $dimension->setMultipleValues($this->getValue("./@multipleValues", $dimensionEl) !== null ? (bool) $this->getValue("./@name",
//                            $dimensionEl) : null);
//                $dimension->setNearestValue($this->getValue("./@nearestValue", $dimensionEl) !== null ? (bool) $this->getValue("./@name",
//                            $dimensionEl) : null);
//                $dimension->setCurrent($this->getValue("./@current", $dimensionEl) !== null ? (bool) $this->getValue("./@name",
//                            $dimensionEl) : null);
//                $dimension->setExtentValue($this->getValue("./text()", $dimensionEl));
//                $wmtslayer->addDimensionl($dimension);
//            }
//        }
//
//        $dataUrlList = $this->xpath->query("./wmts:DataURL", $contextElm);
//        if ($dataUrlList !== null) {
//            foreach ($dataUrlList as $dataUrlEl) {
//                $onlineResource = new OnlineResource();
//                $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $dataUrlEl));
//                $onlineResource->setHref($this->getValue("./wmts:OnlineResource/text()", $dataUrlEl));
//
//                $wmtslayer->addDataUrl($onlineResource);
//            }
//        }
//
//        $featureListUrlList = $this->xpath->query("./wmts:FeatureListURL", $contextElm);
//        if ($featureListUrlList !== null) {
//            foreach ($featureListUrlList as $featureListUrlEl) {
//                $onlineResource = new OnlineResource();
//                $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $featureListUrlEl));
//                $onlineResource->setHref($this->getValue("./wmts:OnlineResource/text()", $featureListUrlEl));
//
//                $wmtslayer->addFeatureListUrl($onlineResource);
//            }
//        }
//
//        $tempList = $this->xpath->query("./wmts:Style", $contextElm);
//        if ($tempList !== null) {
//            foreach ($tempList as $item) {
//                $style = new Style();
//                $style->setName($this->getValue("./wmts:Name/text()", $item));
//                $style->setTitle($this->getValue("./wmts:Title/text()", $item));
//                $style->setAbstract($this->getValue("./wmts:Abstract/text()", $item));
//
//                $legendUrlEl = $this->getValue("./wmts:LegendURL", $item);
//                if ($legendUrlEl !== null) {
//                    $legendUrl      = new LegendUrl();
//                    $legendUrl->setWidth($this->getValue("./@width", $legendUrlEl));
//                    $legendUrl->setHeight($this->getValue("./@height", $legendUrlEl));
//                    $onlineResource = new OnlineResource();
//                    $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $legendUrlEl));
//                    $onlineResource->setHref($this->getValue("./wmts:OnlineResource/@xlink:href", $legendUrlEl));
//                    $legendUrl->setOnlineResource($onlineResource);
//                    $style->setLegendUrl($legendUrl);
//                }
//
//                $styleUrlEl = $this->getValue("./wmts:StyleSheetURL", $item);
//                if ($styleUrlEl !== null) {
//                    $onlineResource = new OnlineResource();
//                    $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $styleUrlEl));
//                    $onlineResource->setHref($this->getValue("./wmts:OnlineResource/@xlink:href", $styleUrlEl));
//                    $style->setStyleUlr($onlineResource);
//                }
//                $stylesheetUrlEl = $this->getValue("./wmts:StyleSheetURL", $item);
//                if ($stylesheetUrlEl !== null) {
//                    $onlineResource = new OnlineResource();
//                    $onlineResource->setFormat($this->getValue("./wmts:Format/text()", $stylesheetUrlEl));
//                    $onlineResource->setHref($this->getValue("./wmts:OnlineResource/@xlink:href", $stylesheetUrlEl));
//                    $style->setStyleSheetUrl($onlineResource);
//                }
//
//                $wmtslayer->addStyle($style);
//            }
//        }
//
//        $minScaleEl = $this->getValue("./wmts:MinScaleDenominator", $contextElm);
//        $maxScaleEl = $this->getValue("./wmts:MaxScaleDenominator", $contextElm);
//        if ($minScaleEl !== null || $maxScaleEl !== null) {
//            $scale = new MinMax();
//            $min   = $this->getValue("./text()", $minScaleEl);
//            $scale->setMin($min !== null ? floatval($min) : null);
//            $max   = $this->getValue("./text()", $maxScaleEl);
//            $scale->setMax($max !== null ? floatval($max) : null);
//            $wmtslayer->setScale($scale);
//
//            $scaleHint    = new MinMax();
//            $minScaleHint = sqrt(2.0) * $scale->getMin() / ($this->resolution / 2.54 *
//                100);
//            $maxScaleHint = sqrt(2.0) * $scale->getMax() / ($this->resolution / 2.54 *
//                100);
//
//            $scaleHint->setMax($maxScaleHint);
//            $scaleHint->setMin($minScaleHint);
//            $wmtslayer->setScaleHint($scaleHint);
//        }
//
//        $tempList = $this->xpath->query("./wmts:Layer", $contextElm);
//        if ($tempList !== null) {
//            foreach ($tempList as $item) {
//                $subwmtslayer = $this->parseLayer($wmts, new WmtsLayerSource(), $item);
//                $subwmtslayer->setParent($wmtslayer);
//                $subwmtslayer->setSource($wmts);
//                $wmtslayer->addSublayer($subwmtslayer);
//                $wmts->addLayer($subwmtslayer);
//            }
//        }
//        $wmtslayer->setSource($wmts);
//        return $wmtslayer;
//    }
}
