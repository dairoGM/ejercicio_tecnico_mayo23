<?php

namespace App\Controller;


use App\Entity\Pages;
use App\Entity\Product;
use App\Entity\Sales;
use App\Entity\Stock;
use App\Form\Type\PageFormType;
use App\Form\Type\ProductType;
use App\Repository\PagesRepository;
use App\Repository\ProductRepository;
use App\Repository\SalesRepository;
use App\Repository\StockRepository;
use App\Services\KtlPaginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $possiblesValues;

    public function __construct()
    {
        $this->possiblesValues = ['id', 'name', 'price', 'category', 'description', 'additionalInformation', 'score', 'sku', 'page', 'limit'];
    }

    /**
     * @Route("/api/products", name="api/products", methods={"GET"}, defaults={"_format":"json"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function listProducts(ProductRepository $productRepository)
    {
        try {
            return $this->json($productRepository->findByFilters());
        } catch (Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/add", name="api/add", methods={"POST"}, defaults={"_format":"json"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addProduct(Request $request, EntityManagerInterface $em)
    {
        try {
            $jsonParams = json_decode($request->getContent(), true);
            if (isset($jsonParams['name']) && !empty($jsonParams['name']) &&
                isset($jsonParams['price']) && !empty($jsonParams['price']) &&
                isset($jsonParams['amount']) && !empty($jsonParams['amount']) &&
                isset($jsonParams['category']) && !empty($jsonParams['category'])
            ) {
                $product = new Product();
                $form = $this->createForm(ProductType::class, $product);
                $form->submit($jsonParams);
                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($product);

                    $newStock = new Stock();
                    $newStock->setProduct($product);
                    $newStock->setAmount(intval($jsonParams['amount']));
                    $em->persist($newStock);

                    $em->flush();
                    return $this->json(['product_id' => $product->getId()]);
                }
                return $this->json('Invalid Form.', Response::HTTP_BAD_REQUEST);
            }
            return $this->json('Incorrect Parameters.', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/update", name="/api/update", methods={"PUT"}, defaults={"_format":"json"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function update(Request $request, ProductRepository $productRepository, EntityManagerInterface $em)
    {
        $jsonParams = json_decode($request->getContent(), true);
        try {
            if (isset($jsonParams['id']) && !empty($jsonParams['id'])) {
                $productEntity = $productRepository->find($jsonParams['id']);

                if ($productEntity instanceof Product) {
                    $form = $this->createForm(ProductType::class, $productEntity);
                    $form->submit($jsonParams);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $em->persist($productEntity);
                        $em->flush();
                        return $this->json(['product_id' => $productEntity->getId()]);
                    }
                    return $this->json($form->getErrors(), Response::HTTP_BAD_REQUEST);
                }
                return $this->json('Item Not Found', Response::HTTP_NOT_FOUND);
            }
            return $this->json('Incorrect Parameter', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/get/{productId}", name="/api/get", methods={"GET"}, defaults={"_format":"json"})
     * @param $productId
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getProduct($productId, ProductRepository $productRepository)
    {
        try {
            $product = $productRepository->find($productId);
            if (!empty($product)) {
                $product = $productRepository->findByFilters(['qb.id' => $productId]);
                return $this->json($product);
            }
            return $this->json('Product Not Found.', Response::HTTP_NOT_FOUND);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/delete/{productId}", name="/api/delete", methods={"DELETE"}, defaults={"_format":"json"})
     * @param $productId
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function deleteProduct($productId, ProductRepository $productRepository, EntityManagerInterface $em)
    {
        try {
            $product = $productRepository->find($productId);
            if (!empty($product)) {
                $em->remove($product);
                $em->flush();
                return $this->json('Product Successfully Removed.');
            }
            return $this->json('Product Not Found.', Response::HTTP_NOT_FOUND);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/full_search", name="api/full_search", methods={"POST"}, defaults={"_format":"json"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function fullSearch(Request $request, ProductRepository $productRepository, KtlPaginator $ktlPaginator)
    {
        try {
            $jsonParams = json_decode($request->getContent(), true);
            $search = true;
            foreach ($jsonParams as $key => $value) {
                if (!in_array($key, $this->possiblesValues)) {
                    $search = false;
                    break;
                }
            }
            if ($search) {
                $page = isset($jsonParams['page']) ? $jsonParams['page'] : null;
                if (empty($page)) {
                    $page = 1;
                }
               unset( $jsonParams['page']);

                $limit = isset($jsonParams['limit']) && !empty($jsonParams['limit']) ? $jsonParams['limit'] : 10;
                unset($jsonParams['limit']);
                $offset = $limit * ($page - 1);

                $all = $productRepository->findByFiltersPaginations($jsonParams, $limit, $offset);
                $status = !empty($all);
                $ktlPaginator->setTotalItemsCount($status ? count($productRepository->findByFiltersPaginations($jsonParams, null, null)) : 0);
                $ktlPaginator->setItemCountPerPage($limit);
                $ktlPaginator->setCurrentPageNumber($page);
                $ktlPaginator->setPageRange(4);


                $has_next_page = $ktlPaginator->hasNext();
                $has_prev_page = $ktlPaginator->hasPrevious();
                $page_range = $ktlPaginator->getAdjacentLinks();
                $current_page_number = $ktlPaginator->getCurrentPageNumber();
                $total_items_count = $ktlPaginator->getTotalItemsCount();

                $items_count_per_page = $ktlPaginator->getItemCountPerPage();
                $end = $items_count_per_page * $current_page_number;
                $start = ($end - $items_count_per_page) + 1;
                $end = ($end > $total_items_count) ? $total_items_count : $end;

                $total_pages_count = $ktlPaginator->count();


                $result = [
                    'results' => $all,
                    'status' => $status,
                    'pagination' => [
                        'next' => $has_next_page,
                        'prev' => $has_prev_page,
                        'start' => $start,
                        'end' => $end,
                        'range' => $page_range,
                        'count' => $status ? count($all) : 0,
                        'pages' => $total_pages_count
                    ]
                ];
                return $this->json($result);
            }
            return $this->json('Incorrect Parameters', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/custom_search", name="api/custom_search", methods={"POST"}, defaults={"_format":"json"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function customSearch(Request $request, ProductRepository $productRepository)
    {
        try {
            $jsonParams = json_decode($request->getContent(), true);
            if (1 === count(array_keys($jsonParams)) && in_array(array_keys($jsonParams)[0], $this->possiblesValues)) {
                return $this->json(count($productRepository->findByFilters($jsonParams)));
            }
            return $this->json('Incorrect Parameters', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/sell", name="/api/sell", methods={"GET"}, defaults={"_format":"json"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function sell(Request $request, ProductRepository $productRepository, StockRepository $stockRepository, SalesRepository $salesRepository, EntityManagerInterface $em)
    {
        $jsonParams = json_decode($request->getContent(), true);
        try {
            if (isset($jsonParams['product_id']) && !empty($jsonParams['product_id'])) {
                $productEntity = $productRepository->find($jsonParams['id']);
                if ($productEntity instanceof Product) {
                    $stock = $stockRepository->findBy(['product' => $jsonParams['product_id']]);
                    if ($stock instanceof Stock) {
                        if ($stock->getAmount() > 0) {
                            $sale = new Sales();
                            $sale->setProduct($productEntity);
                            $sale->setAmount(1);
                            $sale->setSaleValue($productEntity->getPrice());
                            $em->persist($sale);

                            $stock->setAmount($stock->getAmount() - 1);
                            $em->persist($stock);
                            $em->flush();
                            return $this->json('Product Successfully Sold');
                        }
                        return $this->json('Stock Not Available', Response::HTTP_NOT_FOUND);
                    }
                    return $this->json('Invalid Stock', Response::HTTP_BAD_REQUEST);
                }
                return $this->json('Item Not Found', Response::HTTP_NOT_FOUND);
            }
            return $this->json('Incorrect Parameter', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/sold_products", name="/api/sold_products", methods={"GET"}, defaults={"_format":"json"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getSoldProducts(ProductRepository $productRepository)
    {
        try {
            return $this->json($productRepository->getSoldProducts());
        } catch (Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/api/total_gain", name="/api/total_gain", methods={"GET"}, defaults={"_format":"json"})
     * @param SalesRepository $salesRepository
     * @return JsonResponse
     */
    public function getTotalGain(SalesRepository $salesRepository)
    {
        try {
            return $this->json($salesRepository->getTotalGain());
        } catch (Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/products_not_stock", name="/api/products_not_stock", methods={"GET"}, defaults={"_format":"json"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getByNotStock(ProductRepository $productRepository)
    {
        try {
            return $this->json($productRepository->getByNotStock());
        } catch (Exception $exc) {
            return $this->json($exc->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
