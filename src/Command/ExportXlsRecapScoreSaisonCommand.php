<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 29/06/2018
 * Time: 21:31
 */

namespace App\Command;


use App\Service\ExporteurXlsResultatSaison;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportXlsRecapScoreSaisonCommand extends ContainerAwareCommand
{
    /**
     * @var ExporteurXlsResultatSaison
     */
    private $exporteurXlsResultatSaison;

    /**
     * ExportXlsRecapScoreSaisonCommand constructor.
     */
    public function __construct(ExporteurXlsResultatSaison $exporteurXlsResultatSaison)
    {
        parent::__construct();
        $this->exporteurXlsResultatSaison = $exporteurXlsResultatSaison;
    }

    protected function configure()
    {
        $this->setName("archery:exportXls")
            ->setDescription("Export Résultat to Xls")
            ->addArgument("saison",InputArgument::REQUIRED,"saison à exporter")
            ->addArgument("outputFolder",InputArgument::REQUIRED,"dossier ou écrire le fichier")
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->exporteurXlsResultatSaison->exportResultat(
            $input->getArgument("saison"),
            $input->getArgument("outputFolder")
        );
    }

}