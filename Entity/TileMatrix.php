<?php

namespace Mapbender\WmtsBundle\Entity;

/**
 * TileMatrix class
 * @author Paul Schmidt
 */
class TileMatrix
{
    /**  @var string identifier */
    public $identifier;

    /**  @var string scaledenominator */
    public $scaledenominator;

    /**  @var string topleftcorner */
    public $topleftcorner;

    /**  @var string tilewidth */
    public $tilewidth;

    /**  @var string tileheight */
    public $tileheight;

    /**  @var string matrixwidth */
    public $matrixwidth;

    /**  @var string matrixheight */
    public $matrixheight;

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set identifier
     * @param string $value
     */
    public function setIdentifier($value)
    {
        $this->identifier = $value;
    }

    /**
     * Get scaledenominator
     * @return string
     */
    public function getScaledenominator()
    {
        return $this->scaledenominator;
    }

    /**
     * Set scaledenominator
     * @param string $value
     */
    public function setScaledenominator($value)
    {
        $this->scaledenominator = floatval($value);
    }

    /**
     * Get topleftcorner
     * @return string
     */
    public function getTopleftcorner()
    {
        return $this->topleftcorner;
    }

    /**
     * Set topleftcorner
     * @param string $value
     */
    public function setTopleftcorner($value)
    {
        $this->topleftcorner = $value;
    }

    /**
     * Get tilewidth
     * @return string
     */
    public function getTilewidth()
    {
        return $this->tilewidth;
    }

    /**
     * Set tilewidth
     * @param string $value
     */
    public function setTilewidth($value)
    {
        $this->tilewidth = intval($value);
    }

    /**
     * Get tileheight
     * @return string
     */
    public function getTileheight()
    {
        return $this->tileheight;
    }

    /**
     * Set tileheight
     * @param string $value
     */
    public function setTileheight($value)
    {
        $this->tileheight = intval($value);
    }

    /**
     * Get matrixwidth
     * @return string
     */
    public function getMatrixwidth()
    {
        return $this->matrixwidth;
    }

    /**
     * Set matrixwidth
     * @param string $value
     */
    public function setMatrixwidth($value)
    {
        $this->matrixwidth = intval($value);
    }

    /**
     * Get matrixheight
     * @return string
     */
    public function getMatrixheight()
    {
        return $this->matrixheight;
    }

    /**
     * Set matrixheight
     * @param string $value
     */
    public function setMatrixheight($value)
    {
        $this->matrixheight = intval($value);
    }
}
