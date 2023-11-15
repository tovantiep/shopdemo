<?php

namespace App\Http\Controllers;

use App\Components\OrderItem\Creator;
use App\Http\Requests\OrderItem\OrderItemIndexRequest;
use App\Http\Requests\OrderItem\OrderItemStoreRequest;
use App\Transformers\OrderItemTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class OrderItemController extends Controller
{
    public function index(OrderItemIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->index();

            return fractal()
                ->collection($product)
                ->transformWith(new OrderItemTransformer())
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond();
        });

    }

    /**
     * @param OrderItemStoreRequest $request
     * @return JsonResponse
     */
    public function store(OrderItemStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new OrderItemTransformer())
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
