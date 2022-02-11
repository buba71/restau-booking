<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

#[Route('/manager')]
final class CategoryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    
    #[Route('/show_categories', name: 'show_categories')]
    public function showCategories(Request $request): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findBy([],['name' => 'asc']);

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $this->redirectToRoute('show_categories');
        }
        
        return $this->renderForm('BackOffice/ManagerAccount/category/show_categories.html.twig', [
            'categories' => $categories,
            'form'  => $form
        ]);
    }

    #[Route('/delete_category/{id}', name: 'delete_category', requirements: ['id' => '\d+'])]
    public function deleteCategory(Category $category) 
    {
        
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée.');

        return $this->redirectToRoute('show_categories');
    }
}