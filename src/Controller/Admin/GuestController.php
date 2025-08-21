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
    public function index(EntityManagerInterface $manager): Response
    {
        $guests = $manager->getRepository(User::class)->findAll();

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/add', name: 'admin_guest_add')]
    public function add(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($user->getRoles() ?: ['ROLE_GUEST']);

            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'admin_guest_update')]
    public function edit(int $id, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = $manager->getRepository(User::class)->find($id);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            }

            $manager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/form.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }



    #[Route('/delete/{id}', name: 'admin_guest_delete')]
    public function delete(User $user, EntityManagerInterface $manager): Response
    {
        foreach ($user->getMedias() as $media) {
            $manager->remove($media);
            $filePath = $media->getPath();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}
