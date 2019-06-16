<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 18/06/2018
 * Time: 21:55
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Depart
 *
 * @ORM\Entity(repositoryClass="App\Repository\ResultatRepository")
 *
*/
class Resultat
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Depart", mappedBy="resultat")
     */
    private $depart;

    /**
     * @var int|null
     *
     * @ORM\Column(name="placeDefinitive", type="integer", nullable=true)
     */
    private $placeDefinitive;

    /**
     * @var string
     *
     * @ORM\Column(name="distance", type="string", length=255)
     */
    private $distance;

    /**
     * @var string
     *
     * @ORM\Column(name="blason", type="string", length=5)
     */
    private $blason;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @var int|null
     *
     * @ORM\Column(name="paille", type="integer", nullable=true)
     */
    private $paille;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dix", type="integer", nullable=true)
     */
    private $dix;

    /**
     * @var int|null
     *
     * @ORM\Column(name="neuf", type="integer", nullable=true)
     */
    private $neuf;

    /**
     * @var int|null
     *
     * @ORM\Column(name="placeQualif", type="integer", nullable=true)
     */
    private $placeQualif;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scoreDistance1", type="integer", nullable=true)
     */
    private $scoreDistance1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scoreDistance2", type="integer", nullable=true)
     */
    private $scoreDistance2;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scoreDistance3", type="integer", nullable=true)
     */
    private $scoreDistance3;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scoreDistance4", type="integer", nullable=true)
     */
    private $scoreDistance4;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score32", type="integer", nullable=true)
     */
    private $score32;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score16", type="integer", nullable=true)
     */
    private $score16;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score8", type="integer", nullable=true)
     */
    private $score8;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score4", type="integer", nullable=true)
     */
    private $score4;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score2", type="integer", nullable=true)
     */
    private $score2;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scorePetiteFinal", type="integer", nullable=true)
     */
    private $scorePetiteFinal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="scoreFinal", type="integer", nullable=true)
     */
    private $scoreFinal;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Depart
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlaceDefinitive()
    {
        return $this->placeDefinitive;
    }

    /**
     * @param int|null $placeDefinitive
     * @return Resultat
     */
    public function setPlaceDefinitive($placeDefinitive)
    {
        $this->placeDefinitive = $placeDefinitive;
        return $this;
    }

    /**
     * @param Depart $depart
     * @return Resultat
     */
    public function setDepart($depart)
    {
        $this->depart = $depart;
        return $this;
    }

    /**
     * @return Depart
     */
    public function getDepart()
    {
        return $this->depart;
    }

    /**
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param string $distance
     * @return Resultat
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
        return $this;
    }

    /**
     * @return string
     */
    public function getBlason()
    {
        return $this->blason;
    }

    /**
     * @param string $blason
     * @return Resultat
     */
    public function setBlason($blason)
    {
        $this->blason = $blason;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int|null $score
     * @return Resultat
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaille()
    {
        return $this->paille;
    }

    /**
     * @param int|null $paille
     * @return Resultat
     */
    public function setPaille($paille)
    {
        $this->paille = $paille;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDix()
    {
        return $this->dix;
    }

    /**
     * @param int|null $dix
     * @return Resultat
     */
    public function setDix($dix)
    {
        $this->dix = $dix;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNeuf()
    {
        return $this->neuf;
    }

    /**
     * @param int|null $neuf
     * @return Resultat
     */
    public function setNeuf($neuf)
    {
        $this->neuf = $neuf;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlaceQualif()
    {
        return $this->placeQualif;
    }

    /**
     * @param int|null $placeQualif
     * @return Resultat
     */
    public function setPlaceQualif($placeQualif)
    {
        $this->placeQualif = $placeQualif;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScoreDistance1()
    {
        return $this->scoreDistance1;
    }

    /**
     * @param int|null $scoreDistance1
     * @return Resultat
     */
    public function setScoreDistance1($scoreDistance1)
    {
        $this->scoreDistance1 = $scoreDistance1;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScoreDistance2()
    {
        return $this->scoreDistance2;
    }

    /**
     * @param int|null $scoreDistance2
     * @return Resultat
     */
    public function setScoreDistance2($scoreDistance2)
    {
        $this->scoreDistance2 = $scoreDistance2;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScoreDistance3()
    {
        return $this->scoreDistance3;
    }

    /**
     * @param int|null $scoreDistance3
     * @return Resultat
     */
    public function setScoreDistance3($scoreDistance3)
    {
        $this->scoreDistance3 = $scoreDistance3;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScoreDistance4()
    {
        return $this->scoreDistance4;
    }

    /**
     * @param int|null $scoreDistance4
     * @return Resultat
     */
    public function setScoreDistance4($scoreDistance4)
    {
        $this->scoreDistance4 = $scoreDistance4;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore32()
    {
        return $this->score32;
    }

    /**
     * @param int|null $score32
     * @return Resultat
     */
    public function setScore32($score32)
    {
        $this->score32 = $score32;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore16()
    {
        return $this->score16;
    }

    /**
     * @param int|null $score16
     * @return Resultat
     */
    public function setScore16($score16)
    {
        $this->score16 = $score16;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore8()
    {
        return $this->score8;
    }

    /**
     * @param int|null $score8
     * @return Resultat
     */
    public function setScore8($score8)
    {
        $this->score8 = $score8;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore4()
    {
        return $this->score4;
    }

    /**
     * @param int|null $score4
     * @return Resultat
     */
    public function setScore4($score4)
    {
        $this->score4 = $score4;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore2()
    {
        return $this->score2;
    }

    /**
     * @param int|null $score2
     * @return Resultat
     */
    public function setScore2($score2)
    {
        $this->score2 = $score2;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScorePetiteFinal()
    {
        return $this->scorePetiteFinal;
    }

    /**
     * @param int|null $scorePetiteFinal
     * @return Resultat
     */
    public function setScorePetiteFinal($scorePetiteFinal)
    {
        $this->scorePetiteFinal = $scorePetiteFinal;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScoreFinal()
    {
        return $this->scoreFinal;
    }

    /**
     * @param int|null $scoreFinal
     * @return Resultat
     */
    public function setScoreFinal($scoreFinal)
    {
        $this->scoreFinal = $scoreFinal;
        return $this;
    }

    public function getMoyenne(){
        if(!is_null($this->getDepart()->getNbFleches())){
            return
                round($this->getScore() / $this->getDepart()->getNbFleches(),2)
                ;
        }
        return null;
    }
}

