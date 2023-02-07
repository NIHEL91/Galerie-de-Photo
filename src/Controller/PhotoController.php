<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Form\Photo\CommentType;
use App\Form\Photo\NewPhotoType;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class PhotoController extends AbstractController

{
    #[Route('/photo/accueil', name : 'photo.accueil')]
    public function accueil()
    {
        return $this->render('photo/accueil.html.twig');
            
    }
   
    #[Route('/', name: 'photo.list')]
    public function list(PhotoRepository $repository): Response
    {
        $photos = $repository->findAll();
    
        return $this->render('photo/list.html.twig', ['photos' => $photos]) ;
       
    }

    #[Route('/photo/show/{id}', name : 'photo.show')]
    public function show(Photo $photo, Request $request,EntityManagerInterface $entityManager) {
            $user = $this->getUser(); // récupération du User connecté
            if ($user) {
                
                $comment = new Comment(); // création d’un nouveau commentaire
                $form = $this->createForm(CommentType::class, $comment); // création de l’objet form
                $form->handleRequest($request); // on valorise les champs avec les valeurs présentes dans la requête
                if ($form->isSubmitted() && $form->isValid()) {
                    $comment->setUser($user); // valorisation de la propriété « user »
                    $comment->setPhoto($photo); // valorisation de la propriété « photo »
                    $comment->setCreateAtDatetime(new \DateTimeImmutable()); // valorisation de la propriété « create_at
                        // si c’est la soumission et qu’il est valide
                        $entityManager->persist($comment); // préparation à l’enregistrement
                        $entityManager->flush(); // exécution de l’enregistrement en base
                    return $this->redirectToRoute('photo.show', ['id' => $photo->getId()]); // retour à la vue de la photo
                } else {
                    return $this->render('photo/show.html.twig', [ // affichage de la vue photo avec le formulaire
                    'photo' => $photo,
                    'form' => $form->createView(),
                    ]);
                }
            } 
            return $this->render('photo/show.html.twig', [
                'photo' => $photo,
            ]);
        }
    #[Route('/photo/manage', name : 'photo.manage')]
    public function manage(): Response
    {
        $user = $this->getUser() ;
        $photos = $user->getPhotos() ;
        return $this->render('photo/manage.html.twig', ['photos' => $photos]) ;
    }

    #[Route('/photo/delete/{id}', name: 'photo.delete')]
    public function delete (Photo $photo , EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($photo);
        $entityManager->flush();
        return $this->redirectToRoute('photo.manage') ;
    }

    #[Route('/photo/new', name: 'photo.new')]
    public function new(Request $request, EntityManagerInterface $entityManager ): Response
    {
        $photo = new Photo();
        $form = $this->createForm(NewPhotoType::class, $photo);
        $form->handleRequest($request);
        


        if ($form->isSubmitted() && $form->isValid()) { // premiere methode chercher le boutton et la deuxieme methode la valider 
            $photo->setUser($this->getUser());
            $photo->setPostAt(new \DateTimeImmutable());
            $entityManager->persist($photo);
            $entityManager->flush();
            $this->addFlash(
                'succes',
                'votre image à été uploadée'
            );

            return $this->redirectToRoute('photo.manage');
        }
        return $this->render('photo/new.html.twig',[ 'newForm' => $form->createView(), ]);
           
        
    }

}
