<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="prdocut_name", type="string", length=200)
     */
    private $prdocutName;

    /**
     * @var string
     *
     * @ORM\Column(name="product_desc", type="string", length=255)
     */
    private $productDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="product_code", type="string", length=60)
     */
    private $productCode;

    /**
     * @var string
     *
     * @ORM\Column(name="product_image", type="string", length=255)
     */
    private $productImage;

    /**
     * @var string
     *
     * @ORM\Column(name="product_price", type="decimal", precision=10, scale=2)
     */
    private $productPrice;

    /**
     * @var int
     *
     * @ORM\Column(name="Product_category", type="integer")
     */
    private $productCategory;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prdocutName
     *
     * @param string $prdocutName
     *
     * @return Product
     */
    public function setPrdocutName($prdocutName)
    {
        $this->prdocutName = $prdocutName;

        return $this;
    }

    /**
     * Get prdocutName
     *
     * @return string
     */
    public function getPrdocutName()
    {
        return $this->prdocutName;
    }

    /**
     * Set productDesc
     *
     * @param string $productDesc
     *
     * @return Product
     */
    public function setProductDesc($productDesc)
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    /**
     * Get productDesc
     *
     * @return string
     */
    public function getProductDesc()
    {
        return $this->productDesc;
    }

    /**
     * Set productCode
     *
     * @param string $productCode
     *
     * @return Product
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get productCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set productImage
     *
     * @param string $productImage
     *
     * @return Product
     */
    public function setProductImage($productImage)
    {
        $this->productImage = $productImage;

        return $this;
    }

    /**
     * Get productImage
     *
     * @return string
     */
    public function getProductImage()
    {
        return $this->productImage;
    }

    /**
     * Set productPrice
     *
     * @param string $productPrice
     *
     * @return Product
     */
    public function setProductPrice($productPrice)
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    /**
     * Get productPrice
     *
     * @return string
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * Set productCategory
     *
     * @param integer $productCategory
     *
     * @return Product
     */
    public function setProductCategory($productCategory)
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * Get productCategory
     *
     * @return int
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }
}

