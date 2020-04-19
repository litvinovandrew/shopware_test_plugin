<?php

namespace SingleCategoryPlugin\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Bundle\StoreFrontBundle\Gateway\CategoryGatewayInterface;
use SingleCategoryPlugin\Bundle\StoreFrontBundle\CustomCategoryService;

class Frontend implements SubscriberInterface
{
    private $customCategoryService = null;
    private $categoryGateway = null;

    public function __constructor(CustomCategoryService $customCategoryService, CategoryGatewayInterface $categoryGateway)
    {
        $this->customCategoryService = $customCategoryService;
        $this->categoryGateway = $categoryGateway;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Listing' => 'onFrontendListingPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendDetailPostDispatch'
        );
    }

    /**
     * Aim - to catch listing request and if it is category 
     * and if category has only one product 
     * -> redirect to the product detail page
     *
     * @param \Enlight_Event_EventArgs $args
     * @return void
     */
    public function onFrontendListingPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $request = $args->getRequest();

        $requestCategoryId = $request->getParam('sCategory');

        $contextService  = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->getShopContext();
        $category = Shopware()->Container()->get('shopware_storefront.category_service')->get($requestCategoryId, $context);
        if (empty($category)) {
            return null;
        }

        $attributes = $category->getAttributes();
        $productCount = $attributes['count_products']->toArray();
        if ($productCount['count_products'] == 1) {
            //redirect here
            $location = ['controller' => 'detail', 'sArticle' => $productCount['firstProductId']];
            $controller->redirect($location, ['code' => 301]);
        }

        $view = $controller->View();
    }

    /**
     * Aim - to catch detail view request and if category of
     * the product has only one product -> hode breadcrumb
     *
     * @param \Enlight_Event_EventArgs $args
     * @return void
     */
    public function onFrontendDetailPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $request = $args->getRequest();

        $requestCategoryId = $request->getParam('sCategory');

        $contextService  = Shopware()->Container()->get('shopware_storefront.context_service');
        $context = $contextService->getShopContext();
        $category = Shopware()->Container()->get('shopware_storefront.category_service')->get($requestCategoryId, $context);
        if (empty($category)) {
            return null;
        }

        $attributes = $category->getAttributes();
        $productCount = $attributes['count_products']->toArray();

        $view = $controller->View();

        if ($productCount['count_products'] == 1) {
            // {$breadCrumbBackLink = $sBreadcrumb[count($sBreadcrumb) - 1]['link']}
            $view->assign('sBreadcrumb', []);
            $view->assign('theOnlyInCategory', true);//additional flag 
        }
    }
}
