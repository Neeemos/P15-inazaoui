<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $total = $mediaRepository->count([]);

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
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }

            $filename = 'uploads/' . md5(uniqid()) . '.' . $media->getFile()->guessExtension();
            $media->setPath($filename);
            $media->getFile()->move('uploads/', basename($filename));

            $em = $doctrine->getManager();
            $em->persist($media);
            $em->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/media/delete/{id}', name: 'admin_media_delete')]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $media = $doctrine->getRepository(Media::class)->find($id);

        if (!$media) {
            throw $this->createNotFoundException("Media not found");
        }

        $path = $media->getPath();
        $em = $doctrine->getManager();
        $em->remove($media);
        $em->flush();

        if (file_exists($path)) {
            unlink($path);
        }

        return $this->redirectToRoute('admin_media_index');
    }
}
