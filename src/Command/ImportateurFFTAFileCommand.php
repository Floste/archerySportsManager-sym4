<?php
/**
 * Created by PhpStorm.
 * User: floste
 * Date: 17/06/2018
 * Time: 15:20
 */

namespace App\Command;


use App\Service\ImportateurFFTAFileCsv;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportateurFFTAFileCommand extends ContainerAwareCommand
{
    /**
     * @var ImportateurFFTAFileCsv
     */
    private $importateurFFTAFileCsv;

    /**
     * ImportateurFFTAFileCommand constructor.
     * @param ImportateurFFTAFileCsv $importateurFFTAFileCsv
     */
    public function __construct(ImportateurFFTAFileCsv $importateurFFTAFileCsv)
    {
        parent::__construct();
        $this->importateurFFTAFileCsv = $importateurFFTAFileCsv;
    }

    protected function configure()
    {
        $this->setName("archery:importFFTAFile")
            ->setDescription("Import FFTA File")
            ->addArgument("fftaFile",InputArgument::REQUIRED,"FFTA File path");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->importateurFFTAFileCsv->importCsvFFTAFile($input->getArgument("fftaFile"));
    }

}