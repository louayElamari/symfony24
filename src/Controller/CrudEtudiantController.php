<?php

namespace App\Controller;
use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry ;
use App\Form\EtudiantType;
use LDAP\Result;

#[Route('/crud/etudiant')]
class CrudEtudiantController extends AbstractController
{
   
 //show all
 #[Route('/list', name: 'app_crud_etudiant_list')]
 public function list( EtudiantRepository $repository): Response
 {
     $list= $repository->findall();
     return $this->render('crud_etudiant/list.html.twig',['list'=>$list]);
 }

//show one
#[Route('/search/{name}', name: 'app_crud_etudiant_search')]
public function SearchByName(EtudiantRepository $repository, Request $request) :Response  
{
    $name=$request->get('name');
    $list= $repository->findByName($name);
    return $this->render('crud_etudiant/list.html.twig',['list'=>$list]);

}

//add one
#[Route('/new', name: 'app_crud_etudiant_new')]
public function newEtudiant(ManagerRegistry $doctrine,Request $request) :Response
{
    $etudiant = new etudiant();
    $form = $this->createForm(EtudiantType::class, $etudiant);

    // Handle the request
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($etudiant);
        $em->flush();

        return $this->redirectToRoute('app_crud_etudiant_list');
    }

    // Render the form
    return $this->render('crud_etudiant/new.html.twig', [
        'form' => $form->createView(),
    ]);


}

//delete        
#[Route('/delete/{id}', name: 'app_crud_etudiant_delete')]
public function deleteEtudiant(Etudiant $etudiant, ManagerRegistry $doctrine) :Response
{
    
  
        $em=$doctrine->getManager();
        $em->remove($etudiant);
        $em->flush();
    
    return $this->redirectToRoute('app_crud_etudiant_list');
}
//update
#[Route('/update/{id}', name: 'app_crud_etudiant_update')]
public function UpdateEtudiant(Etudiant $etudiant, Request $request, ManagerRegistry $doctrine) :Response
{
    $form = $this->createForm(EtudiantType::class, $etudiant);

    // Handle the request
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('app_crud_etudiant_list');
    }

    // Render the form
    return $this->render('crud_etudiant/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}
public function CalculMoyenne( EtudiantRepository $repository): Response
{
    $moyenne = 0;
    $etudiants = $this->repository->findAll();

    foreach ($etudiants as $etudiant) {
        $moyenne += $etudiant->getMoyenne();
    }

    return $this->render('crud_etudiant/moyenne.html.twig', ['moyenne' => $moyenne / count($etudiants)]);
}

}
