<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/guests')]
class GuestController extends AbstractController
{
    #[Route('/', name: 'admin_guest_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $guests = $em->getRepository(User::class)->findGuests();

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/add', name: 'admin_guest_add')]
    public function add(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => false, 
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($user->getRoles() ?: ['ROLE_GUEST']); 

            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     #[Route('/add', name: 'admin_guest_update')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true, 
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    


    #[Route('/delete/{id}', name: 'admin_guest_delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        foreach ($user->getMedias() as $media) {
            $em->remove($media);
            $filePath = $media->getPath();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}