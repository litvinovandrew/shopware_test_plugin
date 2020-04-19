<?php

namespace SingleCategoryPlugin\Bundle\StoreFrontBundle;

use Shopware\Bundle\StoreFrontBundle\Gateway\CategoryGatewayInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;
use Shopware\Bundle\StoreFrontBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;

class CustomCategoryService implements CategoryServiceInterface
{
    /**
     * @var CategoryServiceInterface
     */
    private $service;

    /**
     * @var CategoryCalculationService
     */
    private $customService;

    /**
     * @var Gateway\CategoryGatewayInterface
     */
    private $categoryGateway;

    /**
     * @param ListProductServiceInterface $$service
     * @param CustomCategoryService $categoryCalculationService
     * @param CategoryServiceInterface $categoryCalculationService
     */
    public function __construct(CategoryServiceInterface $service, CategoryCalculationService $categoryCalculationService, CategoryGatewayInterface $categoryGateway)
    {
        $this->categoryGateway = $categoryGateway;
        $this->service = $service;
        $this->customService = $categoryCalculationService;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, Struct\ShopContextInterface $context)
    {
        $categories = $this->getList([$id], $context);
        $category = array_shift($categories);

        $data = [
            'count_products' => 0,
            'firstProductId' => null,
        ];

        if ($category) {
            //add to category attribute that indicates count of products in the category
            $products = $this->customService->getAllProducts($category->getId());

            if ($products) {
                $data = [
                    'count_products' => count($products),
                    'firstProductId' => array_shift($products)['articleID']
                ];
            }

            //add attribute
            $attribute = new Struct\Attribute($data);
            $category->addAttribute('count_products', $attribute);
        }

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($ids, Struct\ShopContextInterface $context)
    {
        $categories = $this->categoryGateway->getList($ids, $context);

        return $this->filterValidCategories($categories, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsCategories(array $products, Struct\ShopContextInterface $context)
    {
        $categories = $this->categoryGateway->getProductsCategories($products, $context);

        $result = [];
        foreach ($categories as $key => $productCategories) {
            $result[$key] = $this->filterValidCategories($productCategories, $context);
        }

        return $result;
    }

    /**
     * @param Struct\Category[] $categories
     *
     * @return Struct\Category[] $categories Indexed by the category id
     */
    private function filterValidCategories($categories, Struct\ShopContextInterface $context)
    {
        $customerGroup = $context->getCurrentCustomerGroup();

        return array_filter($categories, function (Struct\Category $category) use ($customerGroup) {
            return !(in_array($customerGroup->getId(), $category->getBlockedCustomerGroupIds()));
        });
    }
}
