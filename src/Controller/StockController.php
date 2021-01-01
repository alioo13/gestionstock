<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use App\Entity\Image;
use App\Form\ProduitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ CheckboxType;


use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Repository\ProduitRepository;

class StockController extends AbstractController
{
    /**
     * @Route("/stock", name="stock")
     */
    public function index(ProduitRepository $repo)
    { 
    	$repo= $this->getDoctrine()->getRepository(Produit::class);
    	$produits =$repo->findAll();

        return $this->render('stock/index.html.twig', [
            'controller_name' => 'StockController', 
            'produits' => $produits
             ]);
    }


    /**
     * @Route("/acceuil", name="acceuil")
     */
    public function acceuil()
    {
        return $this->render('stock/acceuil.html.twig');
    }


 /**
     * @Route("/voir/{id}", name="voir",
     requirements={"id"="\d+"})
     */


      public function voirAction($id){





      
    $repository=$this->getDoctrine()
                     ->getManager()
                     ->getRepository(Produit::class);
    $produit=$repository->find($id);
    if(null=== $produit){

    	throw new NotFoundHttpException("L'article ayant l'id ".$id." n'exite pas.");
    }

   


         return $this->render('stock/voir.html.twig', array('produit' => $produit));

    


      }



 /**
     * @Route("/ajouter", name="ajouter")


     */


 public function ajouter(Request $request)
{
	//creation d'un produit
	$produit = new produit();
	 //recuperation formulaire
	$form = $this->get('form.factory')
	             ->create(ProduitType::class, $produit)
	             ->add('save',  SubmitType::class, ['label'=>'Ajouter article'  ]);
	          

	 if($request->isMethod('POST'))
     {
     	//lien objet geré par le formulaire->requete soumission du formulaire
     	 $form->handleRequest($request);
     	 if($form->isValid() )
     	 {
     	 	//enregistrement du produit dans bd
     	 	$em = $this->getDoctrine()->getManager();
     	 	$em->persist($produit);
     	 	$em->flush();

     	 	$request->getSession()->getFlashBag()->add('notice', 'article bien ajouté');
     	 	return $this->redirectToRoute('voir',array('id' => $produit->getId()));
     	 }
     } 

     //affichage de la vue    


	       return $this->render('stock/ajouter.html.twig',array('form' => $form->createView()));
}


 public function menu()
    {
    	$listProduits= array(
['id'=>1 ,'intitule'=>'pantalon'],
['id'=>2 ,'intitule'=>'chemise'],
['id'=>3 ,'intitule'=>'robe']

    	                );
    	return $this->render('stock/menu.html.twig', [ 'listProduits' => $listProduits,]);
    }


/**
     * @Route("/layout", name="layout")


     */

public function layout(){
	return $this->render('stock/layout.html.twig');
}



/**
     * @Route("/base", name="base")


     */

public function base(){
	return $this->render('base.html.twig');
}



    /**
     * @Route("/supprimer/{id}", name="supprimer",
     *
     * @return Response
     */
    /*public function supprimer(Produit $produit)
    {
       
           
        // recuperation de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->render('stock/supprimer.html.twig', [
            'id' => $id,
        ]);
        return new Response ('Produit supprimé');

    }*/

     /**
     * @Route("/supprimer/{id}", name="supprimer",
     requirements={"id"="\d+"})
     */
    public function supprimer(Produit $produit )
    { 
    	//recuperation de l'entity manager
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
       return $this->render('stock/supprimer.html.twig');
           
        
    }


    /**
     * @Route("/modifier/{id}", name="modifier",
        requirements={"id"="\d+"})
     */
    public function modifier($id,Request $request)
    {
    	$produit =$this->getDoctrine()->getManager()->getRepository(Produit::class)->find($id);
    	$form =$this->createFormBuilder($produit)
	       ->add('ref',    TextType::class)
	       ->add('designation',    TextareaType::class)
	       ->add('prix',   TextType ::class)
	       ->add('quantite',    TextType::class ) 
	      
	       ->add('save',    SubmitType::class)
	       ->getForm();


     if($request->isMethod('POST'))
     {
     	 $form->handleRequest($request);
     	 if($form->isValid())
     	 {
     	 	$em = $this->getDoctrine()->getManager();
     	 	$em->persist($produit);
     	 	$em->flush();
     	 	$request->getSession()->getFlashBag()->add('notice', 'article bien modifier.');
     	 	return $this->redirectToRoute('voir',array('id' => $produit->getId()));
     	 }
     	}
     	  return $this->render('stock/modifier.html.twig', 
            array('form' => $form->createView()));
  

}
     	 


 /**
     * @Route("/modifier/{id}", name="modifier",
     
     */
/*
     	 public function modifier(Request $request, Produit $produit){
        //Récupération du formulaire
        $form = $this->createForm(ProduitType::class,$produit);//Lien Objet géré par le formulaire -> Requête soumission du formulaire
        $form-> handleRequest($request);
        //si le formulaire à été soumis et est valide
        if($form->isSubmitted() && $form->isValid()){
            //enregistrement du produit dans la bdd
            $em = $this->getDoctrine()->getManager();
            //inutile, l'objet provient de la BDD
            //$em->persist($produit);
            $em->flush();
           // return new Response("Le produit à bien été modifié dans la base de donnée.");
        }
        //Génération du code HTML pour le formulaire créé
        $formView = $form->createView();
        //Affichage de la vue
        return $this->render('stock/ajouter.html.twig', array('form'=> $formView));

    }
        

*/




      
        
       
     

}
