<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Concours
 *
 * @ORM\Table(name="concours")
 * @ORM\Entity(repositoryClass="App\Repository\ConcoursRepository")
 */
class Concours
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
     * @ORM\ManyToOne(targetEntity="Saison", inversedBy="Concours")
     * @ORM\JoinColumn(name="saison_id", referencedColumnName="id")
     */
    private $saison;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="lieu", type="string", length=255)
     */
    private $lieu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codeStructureOrganisatrice", type="string", length=255, nullable=true)
     */
    private $codeStructureOrganisatrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomStructureOrganisatrice", type="string", length=255, nullable=true)
     */
    private $nomStructureOrganisatrice;

    /**
     * @var string
     *
     * @ORM\Column(name="formuleTir", type="string", length=255, nullable=true)
     */
    private $formuleTir;

    /**
     * @var string
     *
     * @ORM\Column(name="niveauChampionnat", type="string", length=5, nullable=true)
     */
    private $niveauChampionnat;

    /**
     * @var string
     *
     * @ORM\Column(name="detailNiveauChampionnat", type="string", length=255, nullable=true)
     */
    private $detailNiveauChampionnat;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Depart", mappedBy="archer")
     *
     */
    private $departs;

    /**
     * Archer constructor.
     */
    public function __construct()
    {
        $this->departs = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getDeparts()
    {
        return $this->departs;
    }

    /**
     * @param ArrayCollection $departs
     * @return Archer
     */
    public function setDeparts($departs)
    {
        $this->departs = $departs;
        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Concours
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return Concours
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set lieu.
     *
     * @param string $lieu
     *
     * @return Concours
     */
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * Get lieu.
     *
     * @return string
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * Set codeStructureOrganisatrice.
     *
     * @param string|null $codeStructureOrganisatrice
     *
     * @return Concours
     */
    public function setCodeStructureOrganisatrice($codeStructureOrganisatrice = null)
    {
        $this->codeStructureOrganisatrice = $codeStructureOrganisatrice;

        return $this;
    }

    /**
     * Get codeStructureOrganisatrice.
     *
     * @return string|null
     */
    public function getCodeStructureOrganisatrice()
    {
        return $this->codeStructureOrganisatrice;
    }

    /**
     * Set nomStructureOrganisatrice.
     *
     * @param string|null $nomStructureOrganisatrice
     *
     * @return Concours
     */
    public function setNomStructureOrganisatrice($nomStructureOrganisatrice = null)
    {
        $this->nomStructureOrganisatrice = $nomStructureOrganisatrice;

        return $this;
    }

    /**
     * Get nomStructureOrganisatrice.
     *
     * @return string|null
     */
    public function getNomStructureOrganisatrice()
    {
        return $this->nomStructureOrganisatrice;
    }

    /**
     * Set formuleTir.
     *
     * @param string $formuleTir
     *
     * @return Concours
     */
    public function setFormuleTir($formuleTir)
    {
        $this->formuleTir = $formuleTir;

        return $this;
    }

    /**
     * Get formuleTir.
     *
     * @return string
     */
    public function getFormuleTir()
    {
        return $this->formuleTir;
    }

    /**
     * Set niveauChampionnat.
     *
     * @param string $niveauChampionnat
     *
     * @return Concours
     */
    public function setNiveauChampionnat($niveauChampionnat)
    {
        $this->niveauChampionnat = $niveauChampionnat;

        return $this;
    }

    /**
     * Get niveauChampionnat.
     *
     * @return string
     */
    public function getNiveauChampionnat()
    {
        return $this->niveauChampionnat;
    }

    /**
     * Set detailNiveauChampionnat.
     *
     * @param string $detailNiveauChampionnat
     *
     * @return Concours
     */
    public function setDetailNiveauChampionnat($detailNiveauChampionnat)
    {
        $this->detailNiveauChampionnat = $detailNiveauChampionnat;

        return $this;
    }

    /**
     * Get detailNiveauChampionnat.
     *
     * @return string
     */
    public function getDetailNiveauChampionnat()
    {
        return $this->detailNiveauChampionnat;
    }

    /**
     * @return Saison
     */
    public function getSaison()
    {
        return $this->saison;
    }

    /**
     * @param mixed $saison
     * @return Concours
     */
    public function setSaison($saison)
    {
        $this->saison = $saison;
        return $this;
    }
}
