<?php

namespace App\Controller;

use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/order/{mavar}',name:"test.order.route")]
    public function TestOrderRoute($mavar){
        return new Response("
        <html><body>$mavar<body></html> ");
    }
 #[Route('/first/', name: 'first')]
    public function sayHello(\Symfony\Component\HttpFoundation\Request $request): Response
    {

       return  $this->render('first/index.html.twig', [
           'nom'=>"Moataz",
           'prenom'=>"Chaabane",


       ]);


    }
    #[Route('/first/', name: 'say')]
    public function say(\Symfony\Component\HttpFoundation\Request $request): Response
    {

        return  $this->render('first/hello.html.twig', [
            'nom'=>"Moataz",
            'prenom'=>"Chaabane",


        ]);


    }
    #[route('multi/{e1<\d+>}/{e2<\d+>}',
    name:"multiplication",

    )]
    public function multiplication($e1,$e2){
        $resultat=$e1*$e2;
    return new Response("<h1>$resultat</h1>");
    }
    #[route('template',
        name:"template",

    )]
    public function template(){
        return $this ->render('template.html.twig');
    }

}
