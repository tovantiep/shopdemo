<?php

namespace App\Http\Controllers;

use App\Components\Order\Creator;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Mail\OrderApproved;
use App\Mail\OrderCancel;
use App\Mail\OrderCreate;
use App\Mail\OrderShip;
use App\Models\Order;
use App\Models\User;
use App\Transformers\OrderTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\SentMessage;
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
                ->parseIncludes(['order_items', 'user'])
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond();
        });

    }
    /**
     * @param OrderIndexRequest $request
     * @return mixed
     */
    public function ordered(OrderIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->ordered();

            return fractal()
                ->collection($product)
                ->transformWith(new OrderTransformer())
                ->parseIncludes(['order_items', 'user'])
                ->paginateWith(new IlluminatePaginatorAdapter($product))
                ->respond();
        });

    }
    /**
     * @param OrderIndexRequest $request
     * @return mixed
     */
    public function purchase(OrderIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $product = (new Creator($request))->purchase();

            return fractal()
                ->collection($product)
                ->transformWith(new OrderTransformer())
                ->parseIncludes(['order_items', 'user'])
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
            if ($data instanceof Order) {
                DB::commit();
                $this->sendOrderCreatedEmail($data);
                return fractal()
                    ->item($data)
                    ->transformWith(new OrderTransformer())
                    ->parseIncludes(['order_items', 'user'])
                    ->respond();
            } else {
                DB::rollBack();
                return response()->json(['message' => $data], 400);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     * @param $data
     * @return void
     */
    private function sendOrderCreatedEmail($data): void
    {
        $user =  User::whereId($data->user_id)->first();
        Mail::to($user->email)->send(new OrderCreate($data));

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
            return response()->json(['message' => $data['error']], 400);
        }

        try {
            Mail::to($data['mail'])->send(new OrderApproved($data));
            return response()->json(['message' => 'Đơn hàng đã được thanh toán']);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|array
     */
    public function ship(Request $request, $id): JsonResponse|array
    {
        $data = (new Creator($request))->ship($id);

        if (isset($data['error'])) {
            return response()->json(['message' => $data['error']], 400);
        }

        try {
            Mail::to($data['mail'])->send(new OrderShip($data));
            return response()->json(['message' => 'Đơn hàng đã được xử lí']);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|array
     */
    public function cancel(Request $request, $id): JsonResponse|array
    {
        $data = (new Creator($request))->cancel($id);

        if (isset($data['error'])) {
            return response()->json(['message' => $data['error']], 400);
        }

        try {
            Mail::to($data['mail'])->send(new OrderCancel($data));
            return response()->json(['message' => 'Hủy đơn hàng thành công']);
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
