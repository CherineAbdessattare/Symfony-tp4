<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class IndexController extends AbstractController
{
   
 #[Route('/article_list')]
 
 public function home(EntityManagerInterface $entityManager):Response
 {
 //$articles = ['Artcile1', 'Article 2','Article 3'];
 //return $this->render('articles/index.html.twig',['articles' => $articles]); 
 $articles= $entityManager->getRepository(Article::class)->findAll();
return $this->render('articles/index.html.twig',['articles'=> $articles]);
 }    

 #[Route('/article/save')]
 public function save(EntityManagerInterface $entityManager):Response
  {

 $article = new Article();
 $article->setNom('Article 3');
 $article->setPrix(3000);
 $entityManager->persist($article);
 $entityManager->flush();
 return new Response('Article enregisté avec id '.$article->getId());
 }



#[Route('/article/new', name:'new_article', methods: ['GET', 'POST'])]
public function newArticle(EntityManagerInterface $entityManager,FormFactoryInterface $formFactory,Request $request) {
    $article = new Article();
    $form = $this->createFormBuilder($article)
    ->add('nom', TextType::class)
    ->add('prix', TextType::class)
    ->add('save', SubmitType::class, array('label' => 'Créer'))
    ->getForm();
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
        $article = $form->getData();
        $entityManager->persist($article);
        $entityManager->flush();
        return $this->redirectToRoute('new_article');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
}

#[Route('/article/{id}', name:'article_show')]
public function show($id,EntityManagerInterface $entityManager) {
$article = $entityManager->getRepository(Article::class)
->find($id);
return $this->render('articles/show.html.twig',
array('article' => $article));
 }


#[Route('/article/edit/{id}', name:'edit_article', methods: ['GET', 'POST'])]
public function edit(Request $request, $id,EntityManagerInterface $entityManager) {
    $article = new Article();
    $article = $entityManager->getRepository(Article::class)->find($id);
    
    $form = $this->createFormBuilder($article)
    ->add('nom', TextType::class)
    ->add('prix', TextType::class)
    ->add('save', SubmitType::class, array(
    'label' => 'Modifier' 
    ))->getForm();
    
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $entityManager->flush();
    
    return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

#[Route('/article/delete/{id}', name:'delete_article', methods: ['GET','DELETE'])]
 public function delete(Request $request, $id,EntityManagerInterface $entityManager) {
    $article = $entityManager->getRepository(Article::class)->find($id);
    $entityManager->remove($article);
    $entityManager->flush();
    
    $response = new Response();
    $response->send();
    return $this->redirectToRoute('article_list');
    }
   
   

}
?>
