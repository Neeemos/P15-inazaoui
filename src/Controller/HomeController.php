<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\AlbumRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Repository\MediaRepository;

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
        $guests = $doctrine->getRepository(User::class)->findGuests();

        return $this->render('front/guests.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/guest/{id}', name: 'guest')]
    public function guest(#[MapEntity] User $guest): Response
    {
        return $this->render('front/guest.html.twig', [
            'guest' => $guest,
        ]);
    }

    #[Route('/portfolio/{id?}', name: 'portfolio')]
    public function portfolio(ManagerRegistry $doctrine, #[MapEntity] ?Album $album = null, MediaRepository $mediaRepository, AlbumRepository $albumRepository ): Response
    {

        $albums = $albumRepository->findAll();
        $medias = $mediaRepository->findByAlbumAndUserRole($album, 'ROLE_GUEST');

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
