<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType; 
use AppBundle\Repository\ProductRepository;

class ProductController extends Controller {

    /**
     * @Route("/product/view", name="product_view")
     */
    public function indexAction(Request $request) {

        $objProducts = $this->getDoctrine()
                ->getRepository("AppBundle:Product")
                ->findAll();

        return $this->render('product/index.html.twig', array(
                    'products' => $objProducts
        ));
    }
    
    
    /**
     * @Route("/product/add", name="product_add")
     */
    public function addAction( Request $request) {

        $atrributes = array('class' => 'form-control', 'style' => 'margin-bottom:15px');
        $form = $this->createFormBuilder()
                ->add("prdocutName", TextType::class, array("attr" => $atrributes))
                ->add("productDesc", TextType::class, array("attr" => $atrributes))
                ->add("productCode", TextType::class, array("attr" => $atrributes))
                ->add('product_image', FileType::class, array('label' => 'Photo (png, jpeg)'))
                ->add("productPrice", TextType::class, array("attr" => $atrributes))
                ->add("save", SubmitType::class, array("label" => 'Add', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $repo = $this->getDoctrine()->getRepository("AppBundle:Product");
            if ($repo->addProdcut($form) == true) {
                $this->addFlash('message', 'Product created');
                return $this->redirectToRoute('product_view');
            }
        }

        return $this->render('product/add.html.twig', array(
                    'form' => $form->createView(),
                   
        ));
    }
    

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function editAction($id, Request $request) {

        $objProduct = $this->getDoctrine()
                ->getRepository("AppBundle:Product")
                ->find($id);
        if (empty($objProduct)) {
            $this->addFlash('error', 'Product not found');

            return $this->redirectToRoute('product_view');
        }
        $atrributes = array('class' => 'form-control', 'style' => 'margin-bottom:15px');

        $form = $this->createFormBuilder($objProduct)
                ->add("prdocutName", TextType::class, array("attr" => $atrributes))
                ->add("productDesc", TextType::class, array("attr" => $atrributes))
                ->add("productCode", TextType::class, array("attr" => $atrributes))
                ->add("productPrice", TextType::class, array("attr" => $atrributes))
                ->add("save", SubmitType::class, array("label" => 'Edit', 'attr' => array('class' => 'btn btn-primary')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $repo = $this->getDoctrine()->getRepository("AppBundle:Product");
            if ($repo->editProdcut($objProduct, $form) == true) {
                $this->addFlash('message', 'Product updated');
                return $this->redirectToRoute('product_view');
            }
        }

        return $this->render('product/edit.html.twig', array(
                    'form' => $form->createView(),
                    'product_img' => $objProduct->getProductImage()
        ));
    }
    
    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function deleteAction($id){
        
        $product = $this->getDoctrine()->getRepository("AppBundle:Product")->find($id);
        $repo = $this->getDoctrine()->getRepository("AppBundle:Product");
        if($repo->deleteProduct($product) == true){

            $this->addFlash('error', 'Product removed');
            return $this->redirectToRoute('product_view');
        }
        
    }
     
}
