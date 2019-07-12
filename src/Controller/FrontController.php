<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Repository\SubCategoryRepository;
use App\Entity\SubCategory;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(CategoryRepository $repo)
    {
        return $this->render('front/index.html.twig', [
            'categories' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/category/{id}", name="sub_category")
     */
    public function categoryAction(Category $categorie, SubCategoryRepository $repo)
    {
        return $this->render("front/subcategory.html.twig", [
            'categories' => $repo->findByCategory($categorie)
        ]);
    }

    /**
     * @Route("/subcategory/{id}", name="category_produits")
     */
    public function subCategoryAction(SubCategory $categorie, ProductRepository $repo)
    {

        $produits = $repo->createQueryBuilder('u')
            ->where('u.SubCategory = :group_id')
            ->setParameter('group_id', $categorie->getId())
            ->getQuery()->getResult();

        return $this->render("front/produits.html.twig", [
            'produits' => $produits,
            'categorie' => $categorie
        ]);
    }





    /**
     * @Route("/produit/{id}", name="show_produit")
     */
    public function showProduit(Product $produit)
    {

        return $this->render('front/produit.html.twig', [
            'produit' => $produit,
        ]);
    }


    public function libelleCategorie(CategoryRepository $repo)
    {
        return $this->render('front/categories.html.twig', [
            'categories' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchBar(Request $request, ProductRepository $repo)
    {
        $em = $this->getDoctrine()->getManager();

        $prd = $request->get('q');
        $categorie = $request->get('cat');


        $produits = $repo->createQueryBuilder('u')
            ->innerJoin('u.SubCategory', 's')
            ->innerJoin('s.category', 'c')
            ->where('c.id Like :cat and u.name Like :str')
            ->setParameter('str', '%' . $prd . '%')
            ->setParameter('cat', '%' . $categorie . '%')
            ->getQuery()->getResult();



        return $this->render('front/produitsRecherche.html.twig', [
            'produits' => $produits
        ]);
    }
}
