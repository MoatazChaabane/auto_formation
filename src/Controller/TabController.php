<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TabController extends AbstractController
{
    #[Route('/tab/users', name: 'tab')]
    public function users(): Response
    {
        $users=[
            ['firstname'=>'moataz','name'=>'chaabane','age'=>'11'],
            ['firstname'=>'dhafer','name'=>'siala','age'=>'8'],
            ['firstname'=>'abderahmen','name'=>'hadrich','age'=>'28'],
        ];
        return $this->render('tab/users.html.twig',[
            'users'=>$users]);
    }
    #[Route('/tab/{nb<\d+>?5}', name: 'app_tab')]
    public function index($nb): Response
    {
        $note=[];
        for($i=0;$i<$nb;$i++){
            $notes[]=rand(0,20);
        }
        return $this->render('tab/index.html.twig', [
            'notes'=>$notes,
        ]);
    }

}
