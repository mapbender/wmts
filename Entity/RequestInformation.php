<?php

namespace Mapbender\WmtsBundle\Entity;

/**
 * RequestInformation class.
 *
 * @author Paul Schmidt
 */
class RequestInformation
{

    /**
     * ORM\Column(type="string", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $httpGetRestful;

    /**
     * ORM\Column(type="string", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $httpGetKvp;

    /**
     * ORM\Column(type="string", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $httpPost;

    /**
     * ORM\Column(type="array", nullable=true)
     */
    //@TODO Doctrine bug: "protected" replaced with "public"
    public $formats = array();

    /**
     * @var TODO: describe and set access modifier of variable
     */
    public $httpGetKvpl;

    /**
     * Get httpGet
     *
     * @return string
     */
    public function getHttpGetRestful()
    {
        return $this->httpGetRestful;
    }

    /**
     * Set httpGetRestful
     *
     * @param string $value
     * @return $this
     */
    public function setHttpGetRestful($value)
    {
        $this->httpGetRestful = $value;
        return $this;
    }

    /**
     * Get httpGetKvp
     *
     * @return string
     */
    public function getHttpGetKvp()
    {
        return $this->httpGetKvp;
    }

    /**
     * Set httpGetKvp
     *
     * @param string $value
     * @return $this
     */
    public function setHttpGetKvp($value)
    {
        $this->httpGetKvpl = $value;
        return $this;
    }

    /**
     * Get httpPost
     * @return string
     */
    public function getHttpPost()
    {
        return $this->httpPost;
    }

    /**
     * Set httpPost
     *
     * @param string $value
     * @return $this
     */
    public function setHttpPost($value)
    {
        $this->httpPost = $value;
        return $this;
    }

    /**
     * Get formats
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * Set formats
     *
     * @param array $value
     * @return $this
     */
    public function setFormats($value)
    {
        $this->formats = $value;
        return $this;
    }

    /**
     * Add format
     *
     * @param string $value
     * @return $this
     */
    public function addFormat($value)
    {
        $this->formats[] = $value;
        return $this;
    }
}
