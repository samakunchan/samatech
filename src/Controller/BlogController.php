<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Services\FileService;
use DateTime;
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
     * @Route("/admin/blog", name="blog_index", methods={"GET"})
     * @Route("/admin/blog/{page<[1-9]\d*>}", methods="GET", name="blog_index_paginated")
     * @param BlogRepository $blogRepository
     * @param int $page
     * @return Response
     */
    public function index(BlogRepository $blogRepository, int $page): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs_paginate' => $blogRepository->findAllPaginated($page),
        ]);
    }

    /**
     * @Route("/admin/blog/new", name="blog_new", methods={"GET","POST"})
     * @param Request $request
     * @param FileService $fileService
     * @return Response
     */
    public function new(Request $request, FileService $fileService): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentsCollection = [$form->getData()->getMainImage()[0]];
            foreach ($documentsCollection as $key => $result){
                if ($result) {
                    $data = $fileService->transformToWebP($result->getFile());
                    $result->setCompleteUrl($data['filename']);
                    $result->setFolder($data['folder']);
                    $result->setBlog($blog);
                    $result->setExt('.webp');
                    $result->setUpdatedAt(new DateTime('now'));
                    $blog->addMainImage($result);
                    $fileService->moveToFolderAndModifyToWebP($this->getParameter($data['folder']), $data['ext'], $data['filename']);
                }
            }
            $blog->setUser($this->getUser());
            $blog->setSlug($blog->getTitle());
            $blog->setCreatedAt(new DateTime('now'));
            $blog->setUpdatedAt(new DateTime('now'));
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
     */
    public function edit(Request $request, Blog $blog, FileService $fileService): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentsCollection = [$form->getData()->getMainImage()];
            foreach ($documentsCollection as $document) {
                foreach ($document as $image) {
                    if ($image && $image->getFile()) {
                        $dataEdit = $fileService->transformToWebP($image->getFile());
                        $image->setCompleteUrl($dataEdit['filename']);
                        $image->setBlog($blog);
                        $image->setFolder('images');
                        $image->setExt('.webp');
                        $blog->addMainImage($image);
                        if ($image->getTempFileName()) {
                            $fileService->uploadFolder($this->getParameter($dataEdit['folder']), $dataEdit['ext'], $dataEdit['filename'], $image->getTempFileName().'.webp');
                        } else {
                            $image->setUpdatedAt(new DateTime('now'));
                            $fileService->moveToFolderAndModifyToWebP($this->getParameter($dataEdit['folder']), $dataEdit['ext'], $dataEdit['filename']);
                        }
                    }
                }
            }

            $blog->setUpdatedAt(new DateTime('now'));
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
            if ($blog->getMainImage()[0] !== null) {
                $image = $this->getParameter($blog->getMainImage()[0]->getFolder()).'/'.$blog->getMainImage()[0]->getCompleteUrl();
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
