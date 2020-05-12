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
     * @param Request $request
     * @param BlogRepository $blogRepository
     * @param CategoryRepository $categoryRepository
     * @param int $page
     * @param TagRepository $tagRepository
     * @return Response
     */
    public function list(Request $request, BlogRepository $blogRepository, CategoryRepository $categoryRepository, int $page, TagRepository $tagRepository): Response
    {
        $tag = null;
        if ($request->query->has('tag')) {
            $tag = $tagRepository->findOneBy(['name' => $request->query->get('tag')]);
        }
        return $this->render('blog/list.html.twig', [
            'posts_paginated' => $blogRepository->findAllPaginated($tag, $page),
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
            if ($blog->getMainImage()) {
                $fileService->moveToFolderAndModifyToWebP($this->getParameter($blog->getMainImage()->getFolder()), $blog->getMainImage()->getExt(), $blog->getMainImage()->getCompleteUrl()
                );
                $blog->getMainImage()->setExt('.webp');
            }
            $blog->setUser($this->getUser());
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
            if ($blog->getMainImage() !== null) {
                if ($blog->getMainImage()->getTempFileName() && $blog->getMainImage()->getFile()) {
                    $fileService->uploadFolder($this->getParameter($blog->getMainImage()->getFolder()), $blog->getMainImage()->getExt(), $blog->getMainImage()->getCompleteUrl(),$blog->getMainImage()->getTempFileName().'.webp');
                } elseif ($blog->getMainImage()->getFile()) {
                    $fileService->moveToFolderAndModifyToWebP($this->getParameter($blog->getMainImage()->getFolder()), $blog->getMainImage()->getExt(), $blog->getMainImage()->getCompleteUrl()
                    );
                }
                $blog->getMainImage()->setExt('.webp');
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

    /**
     * @Route("/search", methods="GET", name="blog_search")
     * @param Request $request
     * @param BlogRepository $blogRepository
     * @return Response
     */
    public function search(Request $request, BlogRepository $blogRepository): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('blog/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundPosts = $blogRepository->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle(), ENT_COMPAT | ENT_HTML5),
                'date' => $post->getCreatedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getUser()->getFirstname(), ENT_COMPAT | ENT_HTML5),
                'content' => substr($post->getContent(), 0, 150),
                'url' => $this->generateUrl('blog_show_detail', ['slug' => $post->getSlug()]),
            ];
        }

        return $this->json($results);
    }
}
