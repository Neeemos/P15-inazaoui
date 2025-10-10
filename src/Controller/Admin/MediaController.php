<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Form\FormError;

class MediaController extends AbstractController
{
    #[Route('/admin/media', name: 'admin_media_index')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $page = $request->query->getInt('page', 1);
        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $mediaRepository = $doctrine->getRepository(Media::class);
        $medias = $mediaRepository->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );

        $total = count($medias);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page,
        ]);
    }

    #[Route('/admin/media/add', name: 'admin_media_add')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $media = new Media();
        return $this->handleMediaForm($request, $doctrine, $media);
    }

    #[Route('/admin/media/update/{id}', name: 'admin_media_update')]
    public function update(Request $request, #[MapEntity] Media $media, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $media);
        return $this->handleMediaForm($request, $doctrine, $media);
    }


    private function handleMediaForm(Request $request, ManagerRegistry $doctrine, Media $media): Response
    {
        $originalPath = $media->getPath();

        $form = $this->createForm(MediaType::class, $media, [
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();

            if ($file !== null) {
                if ($originalPath && file_exists($originalPath)) {
                    @unlink($originalPath);
                }

                $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
                $filename = 'uploads/' . md5(uniqid((string) random_int(0, 1000), true)) . '.' . $extension;
                $media->setPath($filename);
                $file->move('uploads/', basename($filename));
            }

            /** @var User|null $user */
            $user = $this->getUser();
            if (!$this->isGranted('ROLE_ADMIN') && !$media->getUser()) {
                $media->setUser($user);
            }

            $em = $doctrine->getManager();
            $em->persist($media);
            $em->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
        ]);
    }

    #[Route('/admin/media/delete/{id}', name: 'admin_media_delete')]
    public function delete(#[MapEntity] Media $media, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted("DELETE", $media);

        $path = $media->getPath();
        $em = $doctrine->getManager();
        $em->remove($media);
        $em->flush();

        if ($path && file_exists($path)) {
            @unlink($path);
        }

        return $this->redirectToRoute('admin_media_index');
    }
}
