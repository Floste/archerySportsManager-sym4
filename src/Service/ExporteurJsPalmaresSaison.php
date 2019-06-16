<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 04/07/2018
 * Time: 21:53
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Depart;
use App\Entity\Resultat;
use App\Entity\Saison;
use App\Repository\DepartRepository;
use App\Repository\ResultatRepository;
use App\Repository\SaisonRepository;

class ExporteurJsPalmaresSaison
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var ResultatRepository */
    private $resultatRepo;
    /** @var SaisonRepository */
    private $saisonRepo;
    /** @var DepartRepository */
    private $departRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->resultatRepo = $this->em->getRepository(Resultat::class);
        $this->saisonRepo = $this->em->getRepository(Saison::class);
        $this->departRepo = $this->em->getRepository(Depart::class);
    }

    /**
     * @param $saison
     * @param $outputFolder
     */
    public function exportResultat($saison, $outputFolder)
    {
        // Controles des paramètres
        if($saison == "" || !is_numeric($saison)){
            throw new \Exception("saison doit etre une année");
        }

        if(substr($outputFolder,-1)!=DIRECTORY_SEPARATOR){
            $outputFolder .= DIRECTORY_SEPARATOR;
        }
        if(!is_dir($outputFolder)){
            throw new \Exception("output folder non trouvé");
        }

        $objSaison = $this->saisonRepo->findOneBy([
            'name'=>$saison
        ]);
        if(is_null($objSaison)){
            throw new \Exception("Saison non trouvé");
        }
        // Fin de controles des paramètres

        $fileNameExport = "palmares-data-";
        $fileNameExport .= $objSaison->getName();
        $fileNameExport .= ".js";
        $outFilePath = $outputFolder . DIRECTORY_SEPARATOR . $fileNameExport;
        file_put_contents($outFilePath, $this->getStrInfosClub($objSaison));
        file_put_contents($outFilePath, $this->getStrPalmaresIndividuel($objSaison),FILE_APPEND);
        file_put_contents($outFilePath, $this->getStrPalmaresEquipe($objSaison),FILE_APPEND);
    }

    private function getStrInfosClub(Saison $objSaison){
        return "
            var infosClub = [
                '" . $this->departRepo->getNbCompetiteursForSaison($objSaison) . " compétiteurs',
                '" . $this->departRepo->getNbDepartsForSaison($objSaison) . " départs en compétitions'
            ];
        ";
    }

    private function getStrPalmaresIndividuel(Saison $objSaison){
        $resultats = $this->resultatRepo->getPodiumsIndividuelsForSaison($objSaison);

        $ret = [];
        foreach ($resultats as $resultat) {
            $ret[] = $this->getArrayResultForPalmares($resultat);
        }
        $str = json_encode($ret);
        $str = str_replace("Pr\u00e9nom","Prénom",$str);
        $str = str_replace("\/","/",$str);
        $str = str_replace("'","&quot;",$str);
        return "var palmaresIndividuel = " . $str . "; ";
    }

    private function getArrayResultForPalmares(Resultat $result){
        return [
            "Nom" => $result->getDepart()->getArcher()->getNom(),
            "Prénom" => $result->getDepart()->getArcher()->getPrenom(),
            "Categorie" => $result->getDepart()->getCategorie(),
            "Sexe" => $result->getDepart()->getArcher()->getSexe(),
            "Arme" => $result->getDepart()->getArme(),
            "Discipline" => Depart::getDisciplineName(($result->getDepart()->getDiscipline())),
            "Blason" => $result->getDepart()->getResultat()->getBlason(),
            "Date" => $result->getDepart()->getConcours()->getStartDate()->format("d/m/Y"),
            "Organisateur" => $result->getDepart()->getConcours()->getNomStructureOrganisatrice(),
            "Series" => $result->getDepart()->getConcours()->getFormuleTir(),
            "Score" => $result->getScore(),
            "Place" => $result->getPlaceDefinitive()
        ];
    }

    private function getStrPalmaresEquipe(Saison $objSaison){
        return "var palmaresEquipe = []; ";
    }

}