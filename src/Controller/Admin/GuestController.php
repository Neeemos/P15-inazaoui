<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UserRepository $repo */
        $repo = $manager->getRepository(User::class);
        $guests = $repo->findAll();

        return $this->render('admin/guest/index.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/add', name: 'admin_guest_add')]
    public function add(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles($user->getRoles() ?: ['ROLE_GUEST']);

        
            $plain = $user->getPassword();
            if ($plain !== null && $plain !== '') {
                $user->setPassword($hasher->hashPassword($user, $plain));
            }

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin_guest_index');
        }

        return $this->render('admin/guest/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'admin_guest_update')]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // si le champ password contient une valeur en clair, on la hash
            $plain = $user->getPassword();
            if ($plain !== null && $plain !== '') {
                $user->setPassword($hasher->hashPassword($user, $plain));
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // forcer le chargement si nécessaire (évite certains problèmes de lazy collection)
        $user->getMedias()->count();

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_guest_index');
    }
}
