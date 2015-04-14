<?php
namespace Mapbender\WmtsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * TileMatrix class
 *
 * @author Paul Schmidt <paul.schmidt@wheregroup.com>
 */
class TileMatrix {
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
     * Create an instance of TileMatrix
     * 
     * @param array $tilematrix
     */
    public function __construct($tilematrix=null){
        if($tilematrix !== null && is_array($tilematrix)){
            $this->setIdentifier($tilematrix["identifier"]);
            $this->setScaledenominator($tilematrix["scaledenominator"]);
            $this->setTopleftcorner($tilematrix["topleftcorner"]);
            $this->setTilewidth($tilematrix["tilewidth"]);
            $this->setTileheight($tilematrix["tileheight"]);
            $this->setMatrixwidth($tilematrix["matrixwidth"]);
            $this->setMatrixheight($tilematrix["matrixheight"]);
        }
    }
    /**
     * Get identifier
     * 
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }
    /**
     * Set identifier
     * 
     * @param string $value 
     */
    public function setIdentifier($value) {
        $this->identifier = $value;
    }
    /**
     * Get scaledenominator
     * 
     * @return string 
     */
    public function getScaledenominator() {
        return $this->scaledenominator;
    }
    /**
     * Set scaledenominator
     * @param string $value 
     */
    public function setScaledenominator($value) {
        $this->scaledenominator = floatval($value);
    }
    /**
     * Get topleftcorner
     * 
     * @return string 
     */
    public function getTopleftcorner() {
        return $this->topleftcorner;
    }
    /**
     * Set topleftcorner
     * 
     * @param string $value 
     */
    public function setTopleftcorner($value) {
        $this->topleftcorner = $value;
    }
    /**
     * Get tilewidth
     * 
     * @return string
     */
    public function getTilewidth() {
        return $this->tilewidth;
    }
    /**
     * Set tilewidth
     * 
     * @param string $value 
     */
    public function setTilewidth($value) {
        $this->tilewidth = intval($value);
    }
    /**
     * Get tileheight
     * 
     * @return string
     */
    public function getTileheight() {
        return $this->tileheight;
    }
    /**
     * Set tileheight
     * 
     * @param string $value 
     */
    public function setTileheight($value) {
        $this->tileheight = intval($value);
    }
    /**
     * Get matrixwidth
     * 
     * @return string
     */
    public function getMatrixwidth() {
        return $this->matrixwidth;
    }
    /**
     * Set matrixwidth
     * 
     * @param string $value 
     */
    public function setMatrixwidth($value) {
        $this->matrixwidth = intval($value);
    }
    /**
     * Get matrixheight
     * @return string
     */
    public function getMatrixheight() {
        return $this->matrixheight;
    }
    /**
     * Set matrixheight
     * 
     * @param string $value 
     */
    public function setMatrixheight($value) {
        $this->matrixheight = intval($value);
    }
    /**
     * Get Tilematrix as array of string
     * 
     * @return array
     */
    public function toArray() {
        $tilematrix = array();
        $tilematrix["identifier"] = $this->identifier;
        $tilematrix["scaledenominator"] = floatval($this->scaledenominator);
        $tilematrix["topleftcorner"] = $this->topleftcorner;
        $tilematrix["tilewidth"] = intval($this->tilewidth);
        $tilematrix["tileheight"] = intval($this->tileheight);
        $tilematrix["matrixwidth"] = intval($this->matrixwidth);
        $tilematrix["matrixheight"] = intval($this->matrixheight);
        return $tilematrix;
    }
}
