<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Services\FileService;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", defaults={"page": "1"}, name="blog_show_list", methods={"GET"})
     * @Route("/blog/{page<[1-9]\d*>}", methods="GET", name="blog_show_list_paginated")
     * @param BlogRepository $blogRepository
     * @param CategoryRepository $categoryRepository
     * @param int $page
     * @return Response
     */
    public function list(BlogRepository $blogRepository, CategoryRepository $categoryRepository, int $page): Response
    {
        return $this->render('blog/list.html.twig', [
            'posts_paginated' => $blogRepository->findAllPaginated($page),
            'categories' => $categoryRepository->findBy(['environnement' => '2'])
        ]);
    }

    /**
     * C'est la sidebar de la page list et de la page simple
     * @param BlogRepository $blogRepository
     * @param CategoryRepository $categoryRepository
     * @param TagRepository $tagRepository
     * @return Response
     */
    public function blogSideBar(BlogRepository $blogRepository, CategoryRepository $categoryRepository, TagRepository $tagRepository)
    {
        return $this->render('blog/blog_side_bar.html.twig', [
            'posts' => $blogRepository->findAllOrderByView(),
            'categories' => $categoryRepository->findAllCategoriesBlog(),
            'tags' => $tagRepository->findAllBlogsTags()
        ]);
    }

    /**
     * @Route("/blog/show/{slug}", name="blog_show_detail", methods={"GET"})
     * @param Blog $blog
     * @return Response
     */
    public function show(Blog $blog): Response
    {
        if ($blog) {
            $blog->setView($blog->getView() + 1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();
        }
        return $this->render('blog/detail.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * @Route("/admin/blog", defaults={"page": "1"}, name="blog_index", methods={"GET"})
     * @Route("/admin/blog/{page<[1-9]\d*>}", methods="GET", name="blog_index_paginated")
     * @param BlogRepository $blogRepository
     * @param int $page
     * @return Response
     */
    public function index(BlogRepository $blogRepository, int $page): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs_paginate' => $blogRepository->findAllPaginatedAdmin($page),
        ]);
    }

    /**
     * @Route("/admin/blog/new", name="blog_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, FileService $fileService): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData()->getMainImage();
            if ($image) {
                $data = $fileService->transformToWebP($image->getFile());
                $image->setCompleteUrl($data['filename']);
                $image->setFolder($data['folder']);
                $image->setExt('.webp');
                $image->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                $fileService->moveToFolderAndModifyToWebP($this->getParameter($data['folder']), $data['ext'], $data['filename']);
            }
            $blog->setMainImage($image);
            $blog->setUser($this->getUser());
            $blog->setSlug($blog->getTitle());
            if ($blog->getCreatedAt() == null) { $blog->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris'))); }
            $blog->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('blog_index_paginated', ['page' => 1]);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/blog/edit/{slug}", name="blog_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Blog $blog
     * @param FileService $fileService
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Blog $blog, FileService $fileService): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->getData()->getMainImage();
            if ($image && $image->getFile()) {
                $dataEdit = $fileService->transformToWebP($image->getFile());
                $image->setCompleteUrl($dataEdit['filename']);
                $image->setFolder('images');
                $image->setExt('.webp');
                if ($image->getTempFileName()) {
                    $fileService->uploadFolder($this->getParameter($dataEdit['folder']), $dataEdit['ext'], $dataEdit['filename'], $image->getTempFileName().'.webp');
                } else {
                    $image->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
                    $fileService->moveToFolderAndModifyToWebP($this->getParameter($dataEdit['folder']), $dataEdit['ext'], $dataEdit['filename']);
                }
            }
            $blog->setUpdatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/blog/delete/{id}", name="blog_delete", methods={"DELETE"})
     * @param Request $request
     * @param Blog $blog
     * @return Response
     */
    public function delete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            if ($blog->getMainImage() !== null) {
                $image = $this->getParameter($blog->getMainImage()->getFolder()).'/'.$blog->getMainImage()->getCompleteUrl();
                if (file_exists($image.'.webp')) {
                    unlink($image.'.webp');
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index_paginated', ['page' => 1]);
    }
}
