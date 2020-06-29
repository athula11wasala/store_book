<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ProductsList;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CartController extends Controller {

    /**
     * @Route("/",name="home_route")
     */
    public function indexAction(Request $request) {

        $session = new Session();
        if (!isset($session)) {
            $session->start();
        }
        $form = $this->createFormBuilder()
                ->add('search', ChoiceType::class, array('choices' => array('all' => '0', 'Childern' => 1, 'Fiction' => 2),
                    'attr' => array('onclick' => "formsubmit(this)", 'class' => 'form-control  sel_cat')))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category = $form['search']->getData();
            if (!empty($category)) {
                $product = $this->getDoctrine()->getRepository("AppBundle:Product")->findBy(['productCategory' => $category]);
            }   
        }
        if (empty($category)) {

            $product = $this->getDoctrine()->getRepository("AppBundle:Product")->findAll();
        }

        return $this->render("shop/home.html.twig", ['form' => $form->createView(), 'product_list' => $product, 'count' => !empty($session->get("products")) ? count($session->get("products")) : 0]);
    }
   
    /**
     * @Route("/cart/add",name="cart_add_route")
     */
    public function cartAddAction(Request $request) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
        $session = new Session();

            if ($request->getMethod() == 'POST') {

            if ($request->request->get('product_code')) {
                $product_code = $request->request->get('product_code');
                $result = $repo->findByProductCode($product_code);

                $new_product["product_name"] = $result[0]->getPrdocutName();
                $new_product["product_price"] = $result[0]->getProductPrice();
                $new_product["product_code"] = $result[0]->getProductCode();
                $new_product["product_qty"] = $request->request->get('product_qty');
                $new_product["product_category"] = $result[0]->getProductCategory();
                $arr['products'] = $session->get("products");

                if (!empty($session->get("products")) && !empty($arr)) {

                    $session->remove('products');
                    if (!isset($arr['products'] [$new_product['product_code']])) {
                        $arr['products'] [$new_product['product_code']] = $new_product;
                    } else {
                        $arr['products'] [$new_product['product_code']] = $new_product;
                    }

                    $session->set('products', $arr['products']);
                } else {

                    $session->set('products', [$new_product["product_code"] => $new_product]);
                }
                $session->set('product_count', count($session->get("products")));
                $total_items = count($session->get("products"));

                $response = new Response(json_encode(array('items' => $total_items)));
                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }

            ################## list products in cart ###################
            if (($request->request->get('load_cart')) && $request->request->get('load_cart') == 1) {

                if ($session->get("products") && count($session->get("products")) > 0) { //if we have session variable
                    $arrays = $session->get("products");
                    foreach ($arrays as $rows) {
                        $array[] = ['qty' => $rows['product_qty'], 'price' => $rows['product_price'], 'product_category' => $rows['product_category']];
                    }
                    $cart_added_book = ($repo->arrayKeyValueSearch($array, $key = "product_category"));
                    $discount_details = $repo->priceDiscount($cart_added_book['chilernBook'], $cart_added_book['fictionBook']);
                    $cart_box = '<ul class="cart-products-loaded">';
                    $total = 0;
                    foreach ($session->get("products") as $product) { //loop though items and prepare html content
                        //set variables to use them in HTML content below
                        $product_name = $product["product_name"];
                        $product_price = $product["product_price"];
                        $product_code = $product["product_code"];
                        $product_qty = $product["product_qty"];

                        $cart_box .= "<li> $product_name (Qty : $product_qty   ) &mdash; $ " . sprintf("%01.2f", ($product_price * $product_qty)) . " <a href=\"#\" class=\"remove-item\" data-code=\"$product_code\">&times;</a></li>";
                        $subtotal = ($product_price * $product_qty);
                        $total = ($total + $subtotal);
                    }
                    $cart_box .= "</ul>";
                    $path = $this->get('router')->generate('cart_invoice_route');

                    $cart_box .= '<div class="cart-products-total">SubTotal : ' . '$' . sprintf("%01.2f", $total) . '<br> Discount : ' . '$' . ($discount_details['discount']) . ' <br> Additional Discount : ' . '$' . ($discount_details['additionalDiscount']) . ' <br> Toal : ' . '$' . ($total - ($discount_details['discount'] + $discount_details['additionalDiscount'])) . ' <br><u><a  href=' . $path . ' title="Review Cart and Check-Out">Check-out</a></u></div>';

                    return new Response($cart_box, 200, array('Content-Type' => 'text/html'));
                } else {
                    return new Response("Your Cart is empty", 200, array('Content-Type' => 'text/html'));
                }
            }
        }
    }

    /**
     * @Route("/cart/remove",name="cart_remove_route")
     */
    public function cartRemoveAction(Request $request) {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
        $session = new Session();
        if ($request->query->get('remove_code')) {
            $product_code = $request->query->get('remove_code');

            $arr['products'] = $session->get("products");
            unset($arr['products'][$product_code]);


            if (!empty($session->get("products")) && !empty($arr)) {
                $session->remove('products');
                $session->set('products', $arr['products']);
            }
            $session->set('product_count', !empty(count($session->get("products"))) ? count($session->get("products")) : 0 );
            $total_items = count($session->get("products"));

            $response = new Response(json_encode(array('items' => $total_items)));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    /**
     * @Route("/cart/invoice",name="cart_invoice_route")
     */
    public function viewInvoiceAction() {

        return $this->render("shop/invoice.html.twig");
    }

    /**
     * @Route("/cart/ajax-invoice",name="cart_ajax_invoice_route")
     */
    public function ajaxInvoiceAction() {

        $session = new Session();
        $products = $session->get("products");
        $repo = $this->getDoctrine()->getRepository("AppBundle:Product");
        $total = 0;
        $list_tax = '';
        $cart_box = '<ul class="view-cart">';
        if (count($session->get("products")) > 0) {
            ;
            foreach ($session->get("products") as $rows) {
                $array[] = ['qty' => $rows['product_qty'], 'price' => $rows['product_price'], 'product_category' => $rows['product_category']];
            }

            $cart_added_book = ($repo->arrayKeyValueSearch($array, $key = "product_category"));
            $discount_details = $repo->priceDiscount($cart_added_book['chilernBook'], $cart_added_book['fictionBook']);
            foreach ($products as $row) {

                $product_name = $row['product_name'];
                $product_qty = $row['product_qty'];
                $product_price = $row['product_price'];
                $product_code = $row['product_code'];
                $item_price = ($product_price * $product_qty);
                $cart_box .= "<li>   $product_name (Qty : $product_qty ) <span> $. $item_price </span></li>";
                $subtotal = ($product_price * $product_qty); //Multiply item quantity * price
                $total = ($total + $subtotal); //Add up to total price
            }
            $grand_total = $total; //grand total
            $cart_box .= "<li class='view-cart-total'>  $list_tax <hr>SubTotal  :  " . sprintf("%01.2f", $grand_total) . "</li>";
            $cart_box .= "<li class='view-cart-total'>  $list_tax <hr>Discount :  " . sprintf(($discount_details['discount'])) . "</li>";
            $cart_box .= "<li class='view-cart-total'>  $list_tax <hr>Additional Discount :  " . ($discount_details['additionalDiscount']) . "</li>";
            $cart_box .= "<li class='view-cart-total'>  $list_tax <hr>Payable Amount :  " . ($total - ($discount_details['discount'] + $discount_details['additionalDiscount'])) . "</li>";

            $cart_box .= "</ul>";
        } else {
            $cart_box .= "<li class='view-cart-total'>  Your Cart is empty</li>";
            $cart_box .= "</ul>";
        }
        return new Response($cart_box, 200, array('Content-Type' => 'text/html'));
    }

}
