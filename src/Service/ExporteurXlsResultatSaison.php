<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 29/06/2018
 * Time: 21:56
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Entity\Archer;
use App\Entity\Depart;
use App\Entity\Saison;
use App\Repository\DepartRepository;
use App\Repository\SaisonRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class ExporteurXlsResultatSaison
{
    const CLM_ARCHER_NOM = 'A';
    const CLM_ARCHER_PRENOM = 'B';
    const CLM_ARCHER_CATEGORIE = 'C';
    const CLM_ARCHER_SEXE = 'D';
    const CLM_ARCHER_ARME = 'E';
    const CLM_ARCHER_DISCIPLINE = 'F';
    const CLM_ARCHER_DISTANCE = 'G';
    const CLM_CONCOURS_DATE = 'H';
    const CLM_CONCOURS_LIEU = 'I';
    const CLM_DEPART_NUM = 'J';
    const CLM_RESULTAT_SERIE1 = 'K';
    const CLM_RESULTAT_SERIE2 = 'L';
    const CLM_RESULTAT_SCORE = 'M';
    const CLM_RESULTAT_PLACE = 'N';
    const CLM_RESULTAT_MOYENNE = 'O';
    const CLM_RESULTAT_MOYENNE_SAISON = 'P';
    const CLM_RESULTAT_MOYENNE_SAISON_PREC = 'Q';
    const CLM_RESULTAT_MOYENNE_SAISON_PREC2 = 'R';

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /** @var Color */
    private $goldColor;
    /** @var Color */
    private $silverColor;
    /** @var Color */
    private $bronzeColor;
    /** @var Color */
    private $blueColor;
    /** @var Color */
    private $redColor;
    /** @var Color */
    private $yellowColor;

    private $startLineDepart;
    private $maxLineDepart;

    /** @var DepartRepository */
    private $departRepo;
    /** @var SaisonRepository */
    private $saisonRepo;

    /** @var string */
    private $logoPath;

    /**
     * ImportateurFFTAFileCsv constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        @ini_set("memory_limit",'512M');
        $this->em = $em;

        $this->goldColor = new Color();
        $this->goldColor->setRGB('FFD966');
        $this->silverColor = new Color();
        $this->silverColor->setRGB('BFBFBF');
        $this->bronzeColor = new Color();
        $this->bronzeColor->setRGB('F4B084');

        $this->blueColor = new Color();
        $this->blueColor->setRGB('0070C0');
        $this->redColor = new Color();
        $this->redColor->setRGB('FF0000');
        $this->yellowColor = new Color();
        $this->yellowColor->setRGB('BF8F00');

        $this->departRepo = $this->em->getRepository(Depart::class);
        $this->saisonRepo = $this->em->getRepository(Saison::class);

        $this->logoPath = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "logo-archers-kervignac.png";
    }

    /**
     * @param $saison
     * @param $outputFolder
     */
    public function exportResultat($saison, $outputFolder)
    {
        $this->startLineDepart = 5;
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

        $this->maxLineDepart = $this->startLineDepart + $this->departRepo->getNbDepartsForSaison($objSaison) - 1;

        //Création du fhcihier Excel
        $outFile = new Spreadsheet();
        $this->prepareWorkBook($outFile, $objSaison);

        $sheet = $outFile->getActiveSheet();
        $this->exportResultArchers($sheet,$this->startLineDepart,$objSaison);

        $this->addImageLogo($sheet);

        $exportFileName = "Recap Score - Saison ". $objSaison->getName() . " - " . (new \DateTime())->format("Y-m-d") . ".xlsx";
        $exportFileName = $outputFolder . $exportFileName;
        $writer = new Xlsx($outFile);
        $writer->save($exportFileName);
    }

    /**
     * Preparation général du fichier
     *  - style
     *  - alignement
     *  - taille des colonnes
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function prepareWorkBook(Spreadsheet &$spreadsheet, Saison $saison){
        //Global style
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);
        $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        Cell::setValueBinder(new AdvancedValueBinder());

        $worksheet = $spreadsheet->getActiveSheet();

        //ligne de titre
        $headerTitleColor = new Color();
        $headerTitleColor->setRGB('C00000');
        $worksheet->setCellValue(self::CLM_ARCHER_NOM . '1',"Récapitulatif scores saison " . $saison->getName());
        $worksheet->getStyle(self::CLM_ARCHER_NOM.'1')->getFont()->setColor($headerTitleColor);
        $worksheet->getStyle(self::CLM_ARCHER_NOM.'1')->getFont()->setBold(true);
        $worksheet->getStyle(self::CLM_ARCHER_NOM.'1')->getFont()->setSize(36);
        $worksheet->mergeCells(self::CLM_ARCHER_NOM. "1:" . self::CLM_RESULTAT_MOYENNE_SAISON_PREC2."1");

        //ligne d'entete
        $indiceLigneEntete = $this->startLineDepart - 1;
        $worksheet->getStyle(self::CLM_ARCHER_NOM.$indiceLigneEntete.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$indiceLigneEntete)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        $worksheet->getStyle(self::CLM_ARCHER_NOM.$indiceLigneEntete.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$indiceLigneEntete)->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
        $worksheet->setCellValue(self::CLM_ARCHER_NOM . $indiceLigneEntete,"Nom");
        $worksheet->setCellValue(self::CLM_ARCHER_PRENOM . $indiceLigneEntete,"Prénom");
        $worksheet->setCellValue(self::CLM_ARCHER_CATEGORIE . $indiceLigneEntete,"Catégorie");
        $worksheet->mergeCells(self::CLM_ARCHER_CATEGORIE.$indiceLigneEntete . ":" . self::CLM_ARCHER_ARME.$indiceLigneEntete);
        $worksheet->setCellValue(self::CLM_ARCHER_DISCIPLINE . $indiceLigneEntete,"Discipline");
        $worksheet->mergeCells(self::CLM_ARCHER_DISCIPLINE.$indiceLigneEntete . ":" . self::CLM_ARCHER_DISTANCE.$indiceLigneEntete);
        $worksheet->setCellValue(self::CLM_CONCOURS_DATE . $indiceLigneEntete,"Concours");
        $worksheet->mergeCells(self::CLM_CONCOURS_DATE.$indiceLigneEntete . ":" . self::CLM_CONCOURS_LIEU.$indiceLigneEntete);
        $worksheet->setCellValue(self::CLM_DEPART_NUM . $indiceLigneEntete,"N°\nTir");
        $worksheet->setCellValue(self::CLM_RESULTAT_SERIE1 . $indiceLigneEntete,"Série 1");
        $worksheet->setCellValue(self::CLM_RESULTAT_SERIE2 . $indiceLigneEntete,"Série 2");
        $worksheet->setCellValue(self::CLM_RESULTAT_SCORE . $indiceLigneEntete,"Score");
        $worksheet->setCellValue(self::CLM_RESULTAT_PLACE . $indiceLigneEntete,"Place");
        $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE . $indiceLigneEntete,"Moyenne\npts/flêche");
        $worksheet->mergeCells(self::CLM_RESULTAT_MOYENNE.$indiceLigneEntete . ":" . self::CLM_RESULTAT_MOYENNE_SAISON.$indiceLigneEntete);
        $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE_SAISON_PREC . $indiceLigneEntete,"Moy\nS-1");
        $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2 . $indiceLigneEntete,"Moy\nS-2");
        $worksheet->getRowDimension($indiceLigneEntete.":".$indiceLigneEntete)->setRowHeight(30);

        //Colonne nom et prénom
        $worksheet->getStyle(self::CLM_ARCHER_NOM.$indiceLigneEntete.":".self::CLM_ARCHER_NOM.$this->maxLineDepart)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);
        $worksheet->getColumnDimension(self::CLM_ARCHER_NOM)->setWidth(15);
        $worksheet->getColumnDimension(self::CLM_ARCHER_PRENOM)->setWidth(12);
        $worksheet->getStyle(self::CLM_ARCHER_PRENOM . ":" . self::CLM_ARCHER_PRENOM)->getFont()->setBold(true);

        //Colonne catégories
        $worksheet->getStyle(self::CLM_ARCHER_CATEGORIE.$indiceLigneEntete.":".self::CLM_ARCHER_CATEGORIE.$this->maxLineDepart)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_DASHED);
        $worksheet->getColumnDimension(self::CLM_ARCHER_CATEGORIE)->setWidth(2.57);
        $worksheet->getColumnDimension(self::CLM_ARCHER_SEXE)->setWidth(2.57);
        $worksheet->getColumnDimension(self::CLM_ARCHER_ARME)->setWidth(2.57);
        $worksheet->getStyle(self::CLM_ARCHER_ARME.$indiceLigneEntete.":".self::CLM_ARCHER_ARME.$this->maxLineDepart)->getBorders()->getRight()->setBorderStyle(Border::BORDER_DASHED);

        //Colonne disciplines
        $worksheet->getColumnDimension(self::CLM_ARCHER_DISCIPLINE)->setWidth(9.14);
        $worksheet->getColumnDimension(self::CLM_ARCHER_DISTANCE)->setWidth(7.27);
        $worksheet->getStyle(self::CLM_ARCHER_DISCIPLINE.$indiceLigneEntete.":".self::CLM_ARCHER_DISCIPLINE.$this->maxLineDepart)->getAlignment()->setWrapText(true);

        //Colonne concours
        $worksheet->getColumnDimension(self::CLM_CONCOURS_DATE)->setWidth(10.14);
        $worksheet->getColumnDimension(self::CLM_CONCOURS_LIEU)->setWidth(16);

        //Colonne depart
        $worksheet->getColumnDimension(self::CLM_DEPART_NUM)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_SERIE1)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_SERIE2)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_SCORE)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_PLACE)->setWidth(5);

        //Colonne moyenne
        $worksheet->getColumnDimension(self::CLM_RESULTAT_MOYENNE)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_MOYENNE_SAISON)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_MOYENNE_SAISON_PREC)->setWidth(7);
        $worksheet->getColumnDimension(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2)->setWidth(7);
        $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE.$indiceLigneEntete.":".self::CLM_RESULTAT_MOYENNE.$this->maxLineDepart)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);
        $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE_SAISON_PREC.$indiceLigneEntete.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC.$this->maxLineDepart)->getBorders()->getLeft()->setBorderStyle(Border::BORDER_DASHED);

        //derniere colonne
        $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$indiceLigneEntete.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$this->maxLineDepart)->getBorders()->getRight()->setBorderStyle(Border::BORDER_MEDIUM);

    }

    private function exportResultArchers(Worksheet &$worksheet, $startIndice, Saison $objSaison){
        $archers = $this->em->getRepository(Archer::class)->getArchersFromSaison($objSaison);

        $startLoopIndice = $startIndice;

        /** @var Archer $archer */
        foreach ($archers as $archer) {
            $curIndice = $startLoopIndice;

            $departs = $this->departRepo->getDepartsForArcherSaison($archer, $objSaison);
            $categorieArcherSaison = $departs[0]->getCategorie();

            $worksheet->setCellValue(self::CLM_ARCHER_NOM . $curIndice,$archer->getNom());
            $worksheet->setCellValue(self::CLM_ARCHER_PRENOM . $curIndice,ucwords(strtolower($archer->getPreNom())));
            $worksheet->setCellValue(self::CLM_ARCHER_CATEGORIE . $curIndice,$categorieArcherSaison);
            $worksheet->setCellValue(self::CLM_ARCHER_SEXE . $curIndice,$archer->getSexe());

            //Fusion des cellules globales de chaque archer
            $fromIndice = $curIndice;
            $nbDeparts = count($departs);
            $toIndice = $fromIndice + $nbDeparts - 1;
            $worksheet->mergeCells(self::CLM_ARCHER_NOM.$fromIndice . ":" . self::CLM_ARCHER_NOM.$toIndice);
            $worksheet->mergeCells(self::CLM_ARCHER_PRENOM.$fromIndice . ":" . self::CLM_ARCHER_PRENOM.$toIndice);
            $worksheet->mergeCells(self::CLM_ARCHER_SEXE.$fromIndice . ":" . self::CLM_ARCHER_SEXE.$toIndice);
            $worksheet->mergeCells(self::CLM_ARCHER_CATEGORIE.$fromIndice . ":" . self::CLM_ARCHER_CATEGORIE.$toIndice);

            //Filtrage par arme pratiqué par l'archer
            $tabDepartsArme = [];
            /** @var Depart $depart */
            foreach ($departs as $depart) {
                $tabDepartsArme[$depart->getArme()][] = $depart;
            }

            foreach ($tabDepartsArme as $departsArme) {
                $curIndice = $this->traiteDepartArcherArme($worksheet, $departsArme, $curIndice);
            }

            $worksheet->getStyle(self::CLM_ARCHER_NOM.$toIndice.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$toIndice)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

            $startLoopIndice = $startLoopIndice + $nbDeparts;
        }
    }

    private function traiteDepartArcherArme(Worksheet $worksheet, Array $departs, $curIndice){
        $tabDepartsArmeDiscipline = [];

        $endIndice = $curIndice + count($departs) - 1;
        $worksheet->setCellValue(self::CLM_ARCHER_ARME . $curIndice,$departs[0]->getArme());
        $worksheet->mergeCells(self::CLM_ARCHER_ARME.$curIndice . ":" . self::CLM_ARCHER_ARME.$endIndice);

        /** @var Depart $depart */
        foreach ($departs as $depart) {
            $tabDepartsArmeDiscipline[$depart->getDiscipline()][] = $depart;
        }

        foreach ($tabDepartsArmeDiscipline as $departsArmeDiscipline) {
            $curIndice = $this->traiteDepartArcherArmeDiscipline($worksheet, $departsArmeDiscipline, $curIndice);
        }
        $curIndice -= 1;
        $worksheet->getStyle(self::CLM_ARCHER_ARME.$curIndice.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$curIndice)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $curIndice += 1;
        return $curIndice;
    }

    private function traiteDepartArcherArmeDiscipline(Worksheet $worksheet, Array $departs, $curIndice){

        $endIndice = $curIndice + count($departs) - 1;

        $moyenneSaisonArmeDiscipline = $this->getMoyenne($departs);
        $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE_SAISON . $curIndice,$moyenneSaisonArmeDiscipline);
        $colorMoyenneSaison = $this->getColorFromVal($moyenneSaisonArmeDiscipline);
        if($colorMoyenneSaison){
            $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE_SAISON.$curIndice)->getFont()->setColor($colorMoyenneSaison);
        }
        $worksheet->mergeCells(self::CLM_RESULTAT_MOYENNE_SAISON.$curIndice . ":" . self::CLM_RESULTAT_MOYENNE_SAISON.$endIndice);
        $worksheet->mergeCells(self::CLM_RESULTAT_MOYENNE_SAISON_PREC.$curIndice . ":" . self::CLM_RESULTAT_MOYENNE_SAISON_PREC.$endIndice);
        $worksheet->mergeCells(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$curIndice . ":" . self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$endIndice);

        if(!is_null($moyenneSaisonArmeDiscipline)){
            $objSaison = $departs[0]->getConcours()->getSaison();
            $objArcher = $departs[0]->getArcher();
            $moyenneSaisonPrec = $this->getMoyenneSaisonPrecedente($objArcher,$objSaison,$departs[0]->getDiscipline());
            if(!is_null($moyenneSaisonPrec["prec1"])){
                $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE_SAISON_PREC . $curIndice,$moyenneSaisonPrec["prec1"]);
                $colorMoyenneSaisonPrec1 = $this->getColorFromVal($moyenneSaisonPrec["prec1"]);
                if($colorMoyenneSaisonPrec1){
                    $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE_SAISON_PREC.$curIndice)->getFont()->setColor($colorMoyenneSaisonPrec1);
                }
            }
            if(!is_null($moyenneSaisonPrec["prec2"])){
                $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2 . $curIndice,$moyenneSaisonPrec["prec2"]);
                $colorMoyenneSaisonPrec2 = $this->getColorFromVal($moyenneSaisonPrec["prec2"]);
                if($colorMoyenneSaisonPrec2){
                    $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$curIndice)->getFont()->setColor($colorMoyenneSaisonPrec2);
                }
            }
        }

        $worksheet->setCellValue(self::CLM_ARCHER_DISCIPLINE . $curIndice,Depart::getDisciplineName($departs[0]->getDiscipline()));
        $worksheet->mergeCells(self::CLM_ARCHER_DISCIPLINE.$curIndice . ":" . self::CLM_ARCHER_DISCIPLINE.$endIndice);

        $distance = $departs[0]->getResultat()->getDistance();
        if($distance<=10){
            $distance = "";
        }else{
            $distance .= "m";
        }
        $worksheet->setCellValue(self::CLM_ARCHER_DISTANCE. $curIndice,$distance);
        $worksheet->mergeCells(self::CLM_ARCHER_DISTANCE.$curIndice . ":" . self::CLM_ARCHER_DISTANCE.$endIndice);

        /** @var Depart $depart */
        foreach ($departs as $depart) {
            $complementLieu = "";
            //Cas particulier des departs en mode target
            if($depart->isTarget()){
                $scoreSerie1 = $depart->getResultat()->getScoreDistance1() + $depart->getResultat()->getScoreDistance2();
                $scoreSerie2 = $depart->getResultat()->getScoreDistance3() + $depart->getResultat()->getScoreDistance4();
                $complementLieu = " (T)";
            }else{
                $scoreSerie1 = $depart->getResultat()->getScoreDistance1();
                $scoreSerie2 = $depart->getResultat()->getScoreDistance2();
            }

            $worksheet->setCellValue(self::CLM_CONCOURS_DATE.$curIndice, $depart->getConcours()->getStartDate()->format("d/m/Y"));
            $worksheet->setCellValue(self::CLM_CONCOURS_LIEU.$curIndice, $depart->getConcours()->getLieu().$complementLieu);
            $worksheet->setCellValue(self::CLM_DEPART_NUM.$curIndice, $depart->getNumDepart());
            $worksheet->setCellValue(self::CLM_RESULTAT_SERIE1.$curIndice, $scoreSerie1);
            $worksheet->setCellValue(self::CLM_RESULTAT_SERIE2.$curIndice, $scoreSerie2);
            $worksheet->setCellValue(self::CLM_RESULTAT_SCORE.$curIndice, $depart->getResultat()->getScore());
            $worksheet->setCellValue(self::CLM_RESULTAT_PLACE.$curIndice, $depart->getResultat()->getPlaceDefinitive());
            $worksheet->setCellValue(self::CLM_RESULTAT_MOYENNE.$curIndice, $depart->getResultat()->getMoyenne());
            if($depart->getResultat()->getPlaceDefinitive() < 4){
                $bgColor = $this->bronzeColor;
                if (2 == $depart->getResultat()->getPlaceDefinitive()){
                    $bgColor = $this->silverColor;
                }elseif(1 == $depart->getResultat()->getPlaceDefinitive()){
                    $bgColor = $this->goldColor;
                }
                $worksheet->getStyle(self::CLM_CONCOURS_DATE.$curIndice.":".self::CLM_RESULTAT_PLACE.$curIndice)->getFill()
                    ->setStartColor($bgColor)
                    ->setEndColor($bgColor)
                    ->setFillType(Fill::FILL_SOLID)
                    ;
            }
            $colorMoyenne = $this->getColorFromVal($depart->getResultat()->getMoyenne());
            if($colorMoyenne){
                $worksheet->getStyle(self::CLM_RESULTAT_MOYENNE.$curIndice)->getFont()->setColor($colorMoyenne);
            }
            $curIndice += 1;
        }
        $curIndice -= 1;
        $worksheet->getStyle(self::CLM_ARCHER_DISCIPLINE.$curIndice.":".self::CLM_RESULTAT_MOYENNE_SAISON_PREC2.$curIndice)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        $curIndice += 1;
        return $curIndice;
    }

    private function getMoyenne($departs){
        if(0 == count($departs)){
            return null;
        }
        $totalMoyenne = 0;
        /** @var Depart $depart */
        foreach ($departs as $depart) {
            if(is_null($depart->getResultat()->getMoyenne())){
                return null;
            }
            $totalMoyenne += $depart->getResultat()->getMoyenne();
        }
        $moy = round($totalMoyenne / count($departs),2);
        return $moy;
    }

    /**
     * @param Archer $archer
     * @param Saison $saison
     * @param $discipline
     * @return array[prec1] : Moyenne sur la saison N-1
     * @return array[prec2] : Moyenne sur la saison N-2
     */
    private function getMoyenneSaisonPrecedente(Archer $archer, Saison $saison,$discipline){
        if(is_numeric($saison->getName())){
            $prec1Saison = $saison->getName() -1;
            $prec2Saison = $prec1Saison -1;

            $objSaisonPrec1 = $this->saisonRepo->findOneBy([
                'name'=>$prec1Saison
            ]);
            $departsPrec1 = [];
            if($objSaisonPrec1){
                $departsPrec1 = $this->departRepo->getDepartsForArcherSaisonDiscipline($archer,$objSaisonPrec1,$discipline);
            }

            $objSaisonPrec2 = $this->saisonRepo->findOneBy([
                'name'=>$prec2Saison
            ]);
            $departsPrec2 = [];
            if($objSaisonPrec2){
                $departsPrec2 = $this->departRepo->getDepartsForArcherSaisonDiscipline($archer,$objSaisonPrec2,$discipline);
            }

            return [
                "prec1" => $this->getMoyenne($departsPrec1),
                "prec2" => $this->getMoyenne($departsPrec2),
            ];
        }
    }

    private function getColorFromVal($val){
        $returnColor = null;
        if(9 <= $val){
            $returnColor = $this->yellowColor;
        }elseif(7 <= $val) {
            $returnColor = $this->redColor;
        }elseif(5 <= $val) {
            $returnColor = $this->blueColor;
        }
        return $returnColor;
    }

    private function addImageLogo(Worksheet &$worksheet){
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($this->logoPath);
        $drawing->setWorksheet($worksheet);
//        $drawing->setCoordinates('B15');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);
        $drawing->setHeight(80);
    }
}