<?php

namespace SingleCategoryPlugin\Bundle\StoreFrontBundle;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Struct\Category;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class CategoryCalculationService
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CategoryServiceInterface
     */
    private $categoryService;

    /**
     * @param Connection $connection
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all products
     *
     * @param integer $id
     * @return void
     */
    public function getAllProducts(int $id) {
        //here we should calculate amount of products in the category
        $result = $this->getProducts($id);

        //and return the value
        return $result;
    }


    // public function countAllProducts(int $id) {
    //     //here we should calculate amount of products in the category

    //     $result = $this->countProducts($id);

    //     //and return the value
    //     return $result;
    // }

   
    /**
     * @param $ids
     * @param $context
     * @return array
     */
    private function getProducts($id)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['s_articles_categories.articleID'])
            ->from('s_articles_categories')
            ->leftJoin('s_articles_categories','s_articles','s_articles','s_articles.id = s_articles_categories.articleID')
            ->andWhere('s_articles.active = 1')
            ->andWhere('s_articles_categories.categoryId = :id')
            ->setParameter(':id', $id);

        return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }


     /**
     * @param $ids
     * @param $context
     * @return array
     */
    private function countProducts($id)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['count(s_articles_categories.articleID)'])
            ->from('s_articles_categories')
            ->leftJoin('s_articles_categories','s_articles','s_articles','s_articles.id = s_articles_categories.articleID')
            ->andWhere('s_articles.active = 1')
            ->andWhere('s_articles_categories.categoryId = :id')
            ->setParameter(':id', $id);

        return $query->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }
}
