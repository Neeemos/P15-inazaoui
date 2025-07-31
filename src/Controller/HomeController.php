<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route('/guests', name: 'guests')]
    public function guests(ManagerRegistry $doctrine): Response
    {
        $guestRepository = $doctrine->getRepository(User::class);
        $guests = $guestRepository->findBy(['admin' => false]);

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/guest/{id}', name: 'guest')]
    public function guest(ManagerRegistry $doctrine, int $id): Response
    {
        $guest = $doctrine->getRepository(User::class)->find($id);

        if (!$guest) {
            throw $this->createNotFoundException('Guest not found.');
        }

        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
        ]);
    }

    #[Route('/portfolio/{id}', name: 'portfolio')]
    public function portfolio(ManagerRegistry $doctrine, ?int $id = null): Response
    {
        $albumRepository = $doctrine->getRepository(Album::class);
        $userRepository = $doctrine->getRepository(User::class);
        $mediaRepository = $doctrine->getRepository(Media::class);

        $albums = $albumRepository->findAll();
        $album = $id ? $albumRepository->find($id) : null;
        $user = $userRepository->findOneBy(['admin' => true]);

        $medias = $album
            ? $mediaRepository->findBy(['album' => $album])
            : $mediaRepository->findBy(['user' => $user]);

        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias,
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}
