<?php

namespace App\Http\Controllers;

use App\Components\Product\Creator;
use App\Http\Requests\Product\ProductIndexRequest;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ProductController extends Controller
{
    /**
     * @param ProductIndexRequest $request
     * @return mixed
     */
    public function index(ProductIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->index();

            return fractal()
                ->collection($product)
                ->transformWith(new ProductTransformer())
                ->parseIncludes('category')
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond();
        });

    }

    /**
     * @param ProductIndexRequest $request
     * @return mixed
     */
    public function related(ProductIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->related();

            return optional($product) ? fractal()
                ->collection($product)
                ->transformWith(new ProductTransformer())
                ->parseIncludes('category')
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond(): $this->respondBadRequest();

        });

    }


    public function hot(ProductIndexRequest $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return (new Creator($request))->hot();
//            return fractal()
//                ->collection($product)
//                ->transformWith(new ProductTransformer())
//                ->parseIncludes('category')
//                ->paginateWith(new IlluminatePaginatorAdapter($product))
//                ->respond();
        });

    }

    /**
     * @param ProductStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new ProductTransformer())
                ->parseIncludes('category')
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     *
     * @param Request $request
     * @param Product $product
     * @return mixed
     */
    public function show(Request $request, Product $product): mixed
    {
        return $this->withErrorHandling(function () use ($request, $product) {
            $product = (new Creator($request))->show($product);

            return fractal()
                ->item($product)
                ->transformWith(new ProductTransformer())
                ->parseIncludes('category')
                ->respond();
        });
    }


    /**
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->update($product);
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new ProductTransformer())
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request, $id): mixed
    {
        return $this->withErrorHandling(function () use ($request, $id) {
            return (new Creator($request))->destroy($id);
        });
    }

}
