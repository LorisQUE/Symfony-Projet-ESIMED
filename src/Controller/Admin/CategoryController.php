<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("show/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, AdvertRepository $advertRepository, PictureRepository $pictureRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $advertRepository->findBy(['category'=>$category->getId()]);
        $adverts = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            30
        );
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'adverts' => $adverts
        ]);
    }

    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été ajouté avec succès.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category, AdvertRepository $advertRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $nbAdvert = count($advertRepository->findBy(['category'=>$category->getId()]));
            if($nbAdvert == 0) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($category);
                $entityManager->flush();
                $this->addFlash('success', 'La catégorie a bien été supprimée.');
            } else {
                $this->addFlash('danger', 'Vous ne pouvez pas supprimer une catégorie ayant une ou plusieurs annonces.');
            }
        }

        return $this->redirectToRoute('category_index');
    }
}
