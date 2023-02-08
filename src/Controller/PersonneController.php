<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Events\AddPersonneEvent;
use App\Events\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\service\Helpers;
use App\service\mailerService;
use App\service\PDFService;
use App\service\uploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[
    Route('personne'),
    IsGranted('ROLE_USER')
]

class PersonneController extends AbstractController
{
    public function __construct(private LoggerInterface $logger,
                                private Helpers $helper,
    private EventDispatcherInterface $dispatcher)
    {
    }

    #[Route('/', name: 'personne')]

    public function index(ManagerRegistry $doctrine):Response{
        $repository=$doctrine->getRepository(personne::class);
        $personnes=$repository->findAll();
        return $this->render('personne/index.html.twig',['personnes'=>$personnes]);


    }
    #[Route('/pdf/{id}',name:"personne.pdf")]
    public function generatePDFPersonne(Personne $personne=null,PDFService $pdf){
        $html=$this->render('personne/detail.html.twig',['personne'=>$personne]);
        $pdf->showPDFFile($html);
    }
    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]

    public function personneByAge(ManagerRegistry $doctrine,$ageMin,$ageMax):Response{

        $repository=$doctrine->getRepository(personne::class);
        $personnes=$repository->findPersonneByAgeInterval($ageMin,$ageMax);
         return $this->render('personne/index.html.twig',['personnes'=>$personnes]);


    }
    #[Route('/stat/age/{ageMin}/{ageMax}', name: 'personne.list.age')]

    public function StatpersonneByAge(ManagerRegistry $doctrine,$ageMin,$ageMax):Response{
        $repository=$doctrine->getRepository(personne::class);
        $stat=$repository->statsPersonneByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/stat.html.twig',['stats'=>$stat[0],
        'ageMin'=>$ageMin,
        'ageMax'=>$ageMax]);


    }

    #[
        Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.all'),
        IsGranted("ROLE_USER")]

    public function indexAlls(ManagerRegistry $doctrine,$page,$nbre):Response{
        $repository=$doctrine->getRepository(personne::class);
            $nbpersonne=$repository->count([]);
            $nbPage=ceil($nbpersonne/$nbre);

            $personnes=$repository->findBy([],[],$nbre,($page-1)*$nbre);
        $listAllPersonneEvent=new ListAllPersonneEvent(count($personnes));
        $this->dispatcher->dispatch($listAllPersonneEvent,ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);

        return $this->render('personne/index.html.twig',['personnes'=>$personnes,'isPaginated'=>true,
        'nbrePage'=>$nbPage,
            'page'=>$page,
            'nbre'=>$nbre]);


    }
    #[Route('/{id<\d+>}', name: 'personne.detail')]

    public function detail(Personne $personne=null):Response{

        if(!$personne){
            $this-> addFlash('error',"la personne n'existe pas");
            return $this->redirectToRoute('personne');
        }
        return $this->render('personne/detail.html.twig',['personne'=>$personne]);


    }


    #[Route('/add', name: 'app_personne')]
    public function addPersonne(ManagerRegistry $doctrine,Request $resquest): Response
    {
//        $this->getDoctrine(): version 9dima
        $entityManager=$doctrine->getManager();
        $personne=new Personne();
        // $personne est l'image de notre formulaire
        $form=$this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //mon formulaire va aller traiter la requette

        $form->handleRequest($resquest);
        // est ce que le formulaire a ete soumi
        if($form->isSubmitted() && $form->isValid()){
            $manager=$doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();


            //oui:ajouter objet dans bd
            // rediriger vers liste personne
            //afficher lmessage succers
            $this->addFlash("success",$personne->getName(). "a été ajouté avec succes");
            return $this->redirectToRoute('personne');
        }else
        {

                //afficher formulaire
        }
        return $this->render('personne/add-personne.html.twig', [
                'form'=>$form->createView()
        ]);
    }
    #[Route('/edit/{id?0}', name: 'edit')]
    public function editPersonne(
        ManagerRegistry $doctrine,
        Request $resquest,$id,Personne $personne=null,
        uploaderService $uploaderService,
        mailerService $mailer,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $new=false;
//        $this->getDoctrine(): version 9dima
        $entityManager=$doctrine->getManager();
        if(!$personne){
            $new=true;
            $personne=new Personne();

        }
        // $personne est l'image de notre formulaire
        $form=$this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //mon formulaire va aller traiter la requette

        $form->handleRequest($resquest);
        // est ce que le formulaire a ete soumi
        if($form->isSubmitted()){
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory=$this->getParameter('personne_directory');
 $personne->setImage($uploaderService->uploadFile($photo,$directory));
            }
            $message=" a été mis à jour avec succès";

            if($new){
                $message=" a été ajouté avec succès";
                $personne->setCreatedBy($this->getUser());
            }
            $manager=$doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            if($new){
                //on a créé notre evennement
                $addPersonneEvent= new AddPersonneEvent($personne);
                // on va dispatcher cet event
                $this->dispatcher->dispatch($addPersonneEvent,AddPersonneEvent::ADD_PERSONNE_EVENT);

            }
            $this->addFlash('success',$personne->getName(). $message);


            return $this->redirectToRoute('personne');
        }else
        {

            //afficher formulaire
        }
        return $this->render('personne/add-personne.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('/add/{firstname}/{name}/{age}', name: 'add_personne')]
    public function addPersonne2(ManagerRegistry $doctrine,$firstname,$name,$age): Response
    {
//        $this->getDoctrine(): version 9dima
        $entityManager=$doctrine->getManager();
        $personne=new Personne();
        $personne->setFisrtname($firstname);
        $personne->setName($name);
        $personne->setAge($age);

        //ajouter l'poeration dinsertion de la personne das la transaction
        $entityManager->persist($personne);
        //excuter la transaction
        $entityManager->flush();
        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }
    #[
        Route('/delete/{id}',name:'personne.delete'),
    IsGranted("ROLE_ADMIN")]
    public function deletePersonne(Personne $personne=null,$id,ManagerRegistry $doctrine):RedirectResponse
    {
        //récupéréer la personne
        //si la personne existe=> le supprimer et retourner un flashmessage de succes

        if ($personne) {
            $manager = $doctrine->getManager();
            //ajoute la fn de suppression
            $manager->remove($personne);
            //executer la transaction
            $manager->flush();
            $this->addFlash('success', "la personne a été supprimé avec succès");

        } else {
            //sinon: retourner un flashmessage d'erreur
            $this->addFlash('error', "Personne inexistante");


        }
        RETURN $this->redirectToRoute("personne.list.all");


    }
    #[Route('/update/{id}/{name}/{firstname}/{age}',name:'Personne.update')]
    public function updatePersonne(Personne $personne=null,$id,$name,$firstname,$age,ManagerRegistry $doctrine):RedirectResponse{
        //verifier que la personne existe

            //si la personne existe
        if($personne){
            //MAJ personne
            $personne->setName($name);
            $personne->setFisrtname($firstname);
            $personne->setAge($age);
            $manager=$doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            // message de succès
            $this->addFlash('success', "la personne a été mis à jour avec succès");


        }else{

            //sinon
            // message de erreur
        $this->addFlash('error', "persone inexistante");
        }
        RETURN $this->redirectToRoute("personne.list.all");



    }
}