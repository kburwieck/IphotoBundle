<?php

namespace Burwieck\IphotoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('iphoto:import')
            ->setDescription('Import iPhoto Bibliothek')
            ->addArgument('source', InputArgument::OPTIONAL, 'Source location of your iPhoto Bibliothek')
            ->addArgument('target', InputArgument::OPTIONAL, 'The directory where you create the bibliothek')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force an import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importer = $this->getContainer()->get('burwieck_iphoto.importer');
        $source = $input->getArgument('source');        
        if($source) {
            $importer->getAlbumData()->setSource($source);
        }

        $target = $input->getArgument('target');
        if($target) {
        	$importer->setTarget($target);
        }

        $output->writeLn(sprintf(
	        "\n<comment>Start Import iPhoto Bibliothek</comment> \nSource: <info>%s</info>\nTarget: <info>%s</info>", 
        	$importer->getSource(), 
        	$importer->getTarget()) . "\n"
        );

        // get configuration
        $importKeywords = $importer->getImportConfig('keywords');
        if(count($importKeywords)) {
        	$output->writeLn(sprintf('Import Keywords: <info>%s</info>', implode(',', $importKeywords)));
        }

        $importFaces = $importer->getImportConfig('faces');
        if(count($importFaces)) {
        	$output->writeLn(sprintf('Import Faces: <info>%s</info>', implode(',', $importFaces)));
        }

        $output->writeLn("\nchecking import....");
        $importer->prepareImport();

        if(!$input->getOption('force')) {
	        $info = $importer->getInfo();
	        foreach($info['import'] as $type => $data) {
        		$output->writeLn(sprintf('<info>%d %s</info>', count($data), $type));
	        }
	    } else {
	    	$output->writeLn("start import....");
	    	$importer->startImport();
	    	$output->writeLn("imported: ");
	    	$result = $importer->getResult();
	        foreach($result as $type => $data) {
        		$output->writeLn(sprintf('<info>%d %s</info>', $data['new'], $type));
	        }
        }
    }
}