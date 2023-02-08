<?php

namespace App\EventListener;

use App\Events\AddPersonneEvent;
use App\Events\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger)
    {

    }

    public function onPersonneAdd( AddPersonneEvent $event){
        $this->logger->debug("cc je suis en train d'écouter l'evennement personne.add et la personne vient d'etre ajoutée et c'est ".$event->getPersonne()->getName());
    }
    public function onListAllPersonne( ListAllPersonneEvent $event){
        $this->logger->debug("Le nombre de personne dans la base est  ".$event->getNbPersonne());
    } public function onListAllPersonne2( ListAllPersonneEvent $event){
        $this->logger->debug("Le second listener avec le nombre".$event->getNbPersonne());
    }
    public function logKernelRequest( KernelEvent $event){
        dd($event->getRequest());
    }
}