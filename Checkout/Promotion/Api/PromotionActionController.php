<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Promotion\Api;

use Shopware\Core\Checkout\Cart\LineItem\Group\LineItemGroupPackagerInterface;
use Shopware\Core\Checkout\Cart\LineItem\Group\LineItemGroupServiceRegistry;
use Shopware\Core\Checkout\Cart\LineItem\Group\LineItemGroupSorterInterface;
use Shopware\Core\Checkout\Promotion\Util\PromotionCodesLoader;
use Shopware\Core\Checkout\Promotion\Util\PromotionCodesRemover;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class PromotionActionController extends AbstractController
{
    /**
     * @var PromotionCodesLoader
     */
    private $codesLoader;

    /**
     * @var PromotionCodesRemover
     */
    private $codesRemover;

    /**
     * @var LineItemGroupServiceRegistry
     */
    private $serviceRegistry;

    public function __construct(PromotionCodesLoader $codesLoader, PromotionCodesRemover $codesRemover, LineItemGroupServiceRegistry $serviceRegistry)
    {
        $this->codesLoader = $codesLoader;
        $this->codesRemover = $codesRemover;
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * @Route("/api/v{version}/_action/promotion/{promotionId}/codes/individual", name="api.action.promotion.codes", methods={"GET"})
     *
     * @throws \Shopware\Core\Framework\Uuid\Exception\InvalidUuidException
     *
     * @return JsonResponse
     */
    public function getIndividualCodes(string $promotionId, Context $context)
    {
        return new JsonResponse($this->codesLoader->loadIndividualCodes($promotionId));
    }

    /**
     * @Route("/api/v{version}/_action/promotion/{promotionId}/codes/individual", name="api.action.promotion.codes.remove", methods={"DELETE"})
     *
     * @throws \Shopware\Core\Framework\Uuid\Exception\InvalidUuidException
     *
     * @return JsonResponse
     */
    public function deleteIndividualCodes(string $promotionId, Context $context)
    {
        $this->codesRemover->removeIndividualCodes($promotionId, $context);

        return new JsonResponse([]);
    }

    /**
     * @Route("/api/v{version}/_action/promotion/setgroup/packager", name="api.action.promotion.setgroup.packager", methods={"GET"})
     *
     * @throws \Shopware\Core\Framework\Uuid\Exception\InvalidUuidException
     *
     * @return JsonResponse
     */
    public function getSetGroupPackagers()
    {
        $packagerKeys = [];

        /** @var LineItemGroupPackagerInterface $packager */
        foreach ($this->serviceRegistry->getPackagers() as $packager) {
            $packagerKeys[] = $packager->getKey();
        }

        return new JsonResponse($packagerKeys);
    }

    /**
     * @Route("/api/v{version}/_action/promotion/setgroup/sorter", name="api.action.promotion.setgroup.sorter", methods={"GET"})
     *
     * @throws \Shopware\Core\Framework\Uuid\Exception\InvalidUuidException
     *
     * @return JsonResponse
     */
    public function getSetGroupSorters()
    {
        $sorterKeys = [];

        /** @var LineItemGroupSorterInterface $sorter */
        foreach ($this->serviceRegistry->getSorters() as $sorter) {
            $sorterKeys[] = $sorter->getKey();
        }

        return new JsonResponse($sorterKeys);
    }
}
