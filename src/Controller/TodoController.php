<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route("/todo")]
class TodoController extends AbstractController
{

    /**
     *@Route("/",name="app_todo")
     */
    public function index(Request $request): Response
    {
        $session=$request ->getSession();
        //Affcher notre tableau de todo
        // si j'ai mon tableau todo dans ma session, je ne fais que l'afficher sinon je l'intialise puis je l'affiche
        if(!$session-> has('todos')){
            $todos=[
            'achat'=>'acheter clé usb',
            'cours'=>'Finaliser mon cours',
            'correction'=>'Corriger mes examens'
            ];
            $session -> set ('todos',$todos);
            $this->addFlash('info',"la liste des todo vient d'être initialisée ");
        }
        return $this->render('todo/index.html.twig');
    }
    #[Route(
        '/add/{name?test}/{content?test}',
        name:'todo.add'
    )]
    public function addTodo(Request $request,$name,$content):RedirectResponse{
        $session=$request ->getSession();

        //verifier si j'ai le tableau todo dans la session
        if ($session->has ('todos')){
            //si oui
            // verifier si on a deja un todo avec le meme name
            $todos=$session->get("todos");
            if (isset($todos[$name])){
                $this->addFlash('info',"le todo d'id $name existe deja dans la liste ");}
                else{
                    $todos[$name]= $content;
                    $session-> set('todos',$todos);
                    $this->addFlash('success',"le todo d'id $name a été ajouté avec succes ");

                }

            }
            // afficher erreur
            //si non on l'ajoute+message de succres


        else{
            $this->addFlash('error',"la liste des todo n'est pas encore initinitalisée");

        }
        return $this->redirectToRoute('app_todo');}

    #[Route('/update/{name}/{content}',name:'todo.update')]
    public function updateTodo(Request $request,$name,$content):RedirectResponse{
            $session=$request ->getSession();

            //verifier si j'ai le tableau todo dans la session
            if ($session->has ('todos')){
                //si oui
                // verifier si on a deja un todo avec le meme name
                $todos=$session->get("todos");
                if (!isset($todos[$name])){
                    $this->addFlash('info',"le todo d'id $name n'existe pas dans la liste ");}
                else{
                    $todos[$name]= $content;
                    $session-> set('todos',$todos);
                    $this->addFlash('success',"le todo d'id $name a été modifié avec succes ");

                }

            }
            // afficher erreur
            //si non on l'ajoute+message de succres


            else{
                $this->addFlash('error',"la liste des todo n'est pas encore initinitalisée");

            }
            return $this->redirectToRoute('app_todo');




            //si non
          //afficher erreur et rediriger vers le cobtrolleur initial index
    }

    #[Route('/delete/{name}',name:'todo.delete')]
    public function deleteTodo(Request $request,$name):RedirectResponse{
    $session=$request ->getSession();

    //verifier si j'ai le tableau todo dans la session
    if ($session->has ('todos')){
        //si oui
        // verifier si on a deja un todo avec le meme name
        $todos=$session->get("todos");
        if (!isset($todos[$name])){
            $this->addFlash('error',"le todo d'id $name n'existe pas dans la liste ");}
        else{
            unset($todos[$name]);
            $session-> set('todos',$todos);
            $this->addFlash('success',"le todo d'id $name a été supprimé avec succes ");

        }
        return $this->redirectToRoute('app_todo');

    }
    // afficher erreur
    //si non on l'ajoute+message de succres


    else{
        $this->addFlash('error',"la liste des todo n'est pas encore initinitalisée");

    }
    return $this->redirectToRoute('app_todo');




    //si non
    //afficher erreur et rediriger vers le cobtrolleur initial index
}
    #[Route('/reset',name:'todo.reset')]
    public function resetTodo(Request $request):RedirectResponse{
        $session=$request ->getSession();
        $session->remove('todos');
        return $this->redirectToRoute('app_todo');


    }




}

