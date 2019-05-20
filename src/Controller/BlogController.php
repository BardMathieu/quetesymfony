<?php


namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog_index")
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);


        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles,
                'form' => $form->createView()]
        );
    }

    /**
     * @Route("/blog/show/{slug<^[a-z0-9-]+$>?}", name="blog_show")
     */
    public function show(?string $slug) : Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }
    /**
     * @Route("/blog/category/{name}",
     *     name="show_Category")
     */
    public function showByCategory(Category $category) : Response
    {
        if (!$category) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }
        return $this->render(
            'blog/category.html.twig',
            [
                'category' => $category,
                'articles' => $category->getArticles(),
            ]
        );
    }
    /**
     * @Route ("/add/category", name="category_add")
     * @return Response
     */
    public function add(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('blog/add.html.twig', [
            'formCategory' => $form->createView()
        ]);
    }
}