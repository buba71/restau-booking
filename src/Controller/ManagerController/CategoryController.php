<?php

declare(strict_types=1);

namespace App\Controller\ManagerController;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manager')]
#[IsGranted('ROLE_MANAGER')]
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
    public function deleteCategory(Category $category): Response
    {
        
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée.');

        return $this->redirectToRoute('show_categories');
    }
}