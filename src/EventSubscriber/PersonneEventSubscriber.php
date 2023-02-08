<?php

namespace App\EventSubscriber;

use App\Events\AddPersonneEvent;
use App\service\mailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonneEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private mailerService $mailer,private LoggerInterface $logger)
    {

    }

    public static function getSubscribedEvents():array
{
    return [
        AddPersonneEvent::ADD_PERSONNE_EVENT=>['onAddPersonneEvent',3000]
    ];
}
public function onAddPersonneEvent(AddPersonneEvent $event){
    $personne=$event->getPersonne();
    $mailMessage=$personne->getFisrtname().' '.$personne->getName().' a été ajouté avec succes';
    $this->logger->info("envoi d'email pour ".$personne->getFisrtname().' '.$personne->getName());
    $this ->mailer->sendEmail(content: $mailMessage,subject: 'Mail sent from EventSubscriber');

}
}