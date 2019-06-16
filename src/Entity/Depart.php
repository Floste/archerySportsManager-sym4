<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 18/06/2018
 * Time: 22:10
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Depart
 *
 * @ORM\Table(name="depart")
 * @ORM\Entity(repositoryClass="App\Repository\DepartRepository")
 */
class Depart
{
    const DISCIPLINE_SALLE = 'S';
    const DISCIPLINE_FEDERAL = 'E';
    const DISCIPLINE_FITA = 'F';
    const DISCIPLINE_TAE_LONGUE_DISTANCE = 'TL';
    const DISCIPLINE_TAE_COURTE_DISTANCE = 'TC';
    const DISCIPLINE_NATURE = 'N';
    const DISCIPLINE_3D = '3';
    const DISCIPLINE_CAMPAGNE = 'C';

    public static function getDisciplineName($discipline){
        switch ($discipline){
            case self::DISCIPLINE_SALLE : return "Salle";
            case self::DISCIPLINE_FEDERAL : return "Fédéral";
            case self::DISCIPLINE_FITA : return "FITA";
            case self::DISCIPLINE_TAE_LONGUE_DISTANCE : return "TAE Longue Dist.";
            case self::DISCIPLINE_TAE_COURTE_DISTANCE : return "TAE Courte Dist.";
            case self::DISCIPLINE_NATURE : return "Nature";
            case self::DISCIPLINE_3D : return "3D";
            case self::DISCIPLINE_CAMPAGNE : return "Campagne";
        }
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=5)
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="arme", type="string", length=5)
     */
    private $arme;

    /**
     * @var string
     *
     * @ORM\Column(name="discipline", type="string", length=5)
     */
    private $discipline;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numDepart", type="integer", nullable=true)
     */
    private $numDepart;

    /**
     * @var Archer
     * @ORM\ManyToOne(targetEntity="Archer", inversedBy="departs")
     * @ORM\JoinColumn(name="archer_id", referencedColumnName="id")
     */
    private $archer;

    /**
     * @var Concours
     * @ORM\ManyToOne(targetEntity="Concours", inversedBy="departs")
     * @ORM\JoinColumn(name="concours_id", referencedColumnName="id")
     */
    private $concours;

    /**
     * @var Resultat
     * @ORM\OneToOne(targetEntity="Resultat", inversedBy="depart", cascade={"remove"},orphanRemoval=true)
     */
    private $resultat;

    /**
     * Depart constructor.
     */
    public function __construct()
    {
        if($this->getDiscipline() == "S"){
        }
    }


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
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param string $categorie
     * @return Depart
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * @return string
     */
    public function getArme()
    {
        return $this->arme;
    }

    /**
     * @param string $arme
     * @return Depart
     */
    public function setArme($arme)
    {
        $this->arme = $arme;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * @param string $discipline
     * @return Depart
     */
    public function setDiscipline($discipline)
    {
        $this->discipline = $discipline;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumDepart()
    {
        return $this->numDepart;
    }

    /**
     * @param int|null $numDepart
     * @return Depart
     */
    public function setNumDepart($numDepart)
    {
        $this->numDepart = $numDepart;
        return $this;
    }

    /**
     * @return Archer
     */
    public function getArcher()
    {
        return $this->archer;
    }

    /**
     * @param Archer $archer
     * @return Depart
     */
    public function setArcher($archer)
    {
        $this->archer = $archer;
        return $this;
    }

    /**
     * @return Concours
     */
    public function getConcours()
    {
        return $this->concours;
    }

    /**
     * @param Concours $concours
     * @return Depart
     */
    public function setConcours($concours)
    {
        $this->concours = $concours;
        return $this;
    }

    /**
     * @return Resultat
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * @param Resultat $resultat
     * @return Depart
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;
        return $this;
    }

    public function getNbFleches(){
        switch ($this->getDiscipline()){
            case self::DISCIPLINE_SALLE:
                if($this->isTarget()){
                    return 120;
                }else{
                    return 60;
                }
            case self::DISCIPLINE_FEDERAL:
            case self::DISCIPLINE_FITA:
                return 72;
            case self::DISCIPLINE_TAE_COURTE_DISTANCE:
                return 72;
            case self::DISCIPLINE_TAE_LONGUE_DISTANCE:
                return 72;
            case self::DISCIPLINE_3D:
            case self::DISCIPLINE_NATURE:
            case self::DISCIPLINE_CAMPAGNE:
                return null;
        }
    }

    //Indique si ce départ de discipline salle
    // est un cas pariculier de Target
    //Un tagret, c'est 2 séries à 25m et 2 séries à 18m
    public function isTarget(){
        if(self::DISCIPLINE_SALLE == $this->getDiscipline()){
            $formuleTir = $this->getConcours()->getFormuleTir();
            if(strpos($formuleTir,"25")>0 && strpos($formuleTir,"18")>0){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }
}