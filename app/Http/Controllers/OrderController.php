<?php

namespace App\Http\Controllers;

use App\Components\Order\Creator;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Mail\OrderApproved;
use App\Transformers\OrderItemTransformer;
use App\Transformers\OrderTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class OrderController extends Controller
{
    /**
     * @param OrderIndexRequest $request
     * @return mixed
     */
    public function index(OrderIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->index();

            return fractal()
                ->collection($product)
                ->transformWith(new OrderTransformer())
                ->parseIncludes('order_items')
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond();
        });

    }

    /**
     * @param OrderStoreRequest $request
     * @return JsonResponse
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new OrderTransformer())
                ->parseIncludes('order_items')
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
     * @return JsonResponse|array
     */
    public function approve(Request $request, $id): JsonResponse|array
    {
        $data = (new Creator($request))->approve($id);

        if (isset($data['error'])) {
            return response()->json(['message' => $data['error']]);
        }

        try {
            Mail::to('tovantiep2604@gmail.com')->send(new OrderApproved($data));
            return response()->json(['message' => 'Đơn hàng đã được xử lí']);
        } catch (\Exception $e) {
            dd($e->getMessage());
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
