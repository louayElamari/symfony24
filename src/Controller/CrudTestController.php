<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Test;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\TestType;
use App\Repository\EtudiantRepository;



#[Route('/crud/test')]
class CrudTestController extends AbstractController
{

    //show all
 #[Route('/list', name: 'app_crud_test_list')]
 public function list( TestRepository $repository): Response
 {
     $list= $repository->findall();
     return $this->render('crud_test/list.html.twig',['list'=>$list]);
 }

//show one
#[Route('/search/{name}', name: 'app_crud_test_search')]
public function SearchByName(TestRepository $repository, Request $request) :Response  
{
    $name=$request->get('name');
    $list= $repository->findByName($name);
    return $this->render('crud_test/list.html.twig',['list'=>$list]);

}

//add one
#[Route('/new', name: 'app_crud_test_new')]
public function newTest(ManagerRegistry $doctrine,Request $request) :Response
{
    $test = new test();
    $form = $this->createForm(TestType::class, $test);

    // Handle the request
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->persist($test);
        $em->flush();

        return $this->redirectToRoute('app_crud_test_list');
    }

    // Render the form
    return $this->render('crud_test/new.html.twig', [
        'form' => $form->createView(),
    ]);


}

//delete        
#[Route('/delete/{id}', name: 'app_crud_test_delete')]
public function deleteTest(Test $test, ManagerRegistry $doctrine) :Response
{
    
  
        $em=$doctrine->getManager();
        $em->remove($test);
        $em->flush();
    
    return $this->redirectToRoute('app_crud_test_list');
}
//update
#[Route('/update/{id}', name: 'app_crud_test_update')]
public function UpdateTest(Test $test, Request $request, ManagerRegistry $doctrine) :Response
{
    $form = $this->createForm(TestType::class, $test);

    // Handle the request
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('app_crud_test_list');
    }

    // Render the form
    return $this->render('crud_test/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}




#[Route('/search-by-library', name: 'app_crud_test_search_by_etudiant')]
public function searchByLibrary(Request $request, TestRepository $tRepository, EtudiantRepository $eRepository): Response
{
    
    $etudiantName = $request->query->get('etudiant', '');
    $tests = [];
    $etudiant = null;
    if (!empty($etudiantName)) {
        $etudiant = $eRepository->findOneBy(['firstName' => $etudiantName]);
        
     if ($etudiant) {
            $tests = $tRepository->findBy(['etudiant' => $etudiant]);
        }
    }

   
    return $this->render('crud_test/search_by_etudiant.html.twig', [
        'tests' => $tests,
        'etudiant' => $etudiant,
    ]);
}



}
