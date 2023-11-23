<?php

namespace App\Components\Order;

use App\Components\Component;
use App\Mail\OrderApproved;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $order = Order::with([])
            ->when($this->request->filled("status"), function ($query) {
                $query->where('status', 'LIKE', '%' . $this->escapeLike($this->request->input('status')) . '%');
            })
            ->when($this->request->filled("user_id"), function ($query) {
                $query->where('user_id', $this->request->input('user_id'));
            })
            ->when($this->request->filled("total_mount"), function ($query) {
                $query->where('total_mount', 'LIKE', '%' . $this->escapeLike($this->request->input('total_mount')) . '%');
            })
            ->when($this->request->filled("total_quantity"), function ($query) {
                $query->where('total_quantity', 'LIKE', '%' . $this->escapeLike($this->request->input('total_quantity')) . '%');
            });
        return $order->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function ordered(): LengthAwarePaginator
    {
        $user_id = $this->request->input('user_id');
        $order = Order::whereUserId($user_id);

        return $order->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function purchase(): LengthAwarePaginator
    {
        $user_id = $this->request->input('user_id');
        $order = Order::whereUserId($user_id)->where('status', 2);

        return $order->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @param $id
     * @return array|string[]
     */
    public function approve($id): array
    {
        $order = Order::whereId($id)->first();

        if ($order && $order->status == 1) {
            $order->update(['status' => 2]);
            $user = $order->user;

            return [
                'mail' => $user->email,
                'user_name' => $user->name,
                'code' => $order->code
            ];
        } else {
            return ['error' => 'Order không tồn tại hoặc đã được xử lý'];
        }
    }

    /**
     * @param $id
     * @return array|string[]
     */
    public function ship($id): array
    {
        $order = Order::whereId($id)->first();
        if ($order && $order->status == 0 || $order->status == 3) {
            $order->update(['status' => 1]);
            $user = $order->user;

            return [
                'mail' => $user->email,
                'user_name' => $user->name,
                'code' => $order->code
            ];
        } else {
            return ['error' => 'Order không tồn tại hoặc đã được xử lý'];
        }
    }

    /**
     * @param $id
     * @return array|string[]
     */
    public function cancel($id): array
    {
        $order = Order::whereId($id)->first();

        if ($order && $order->status == 0 || $order->status == 1) {
            $order->update(['status' => 3]);
            $user = $order->user;

            $data =  [
                'mail' => $user->email,
                'user_name' => $user->name,
                'code' => $order->code
            ];

            $orderItems = $order->orderItems;

            foreach ($orderItems as $orderItem) {
                $product = $orderItem->product;

                if ($orderItem->quantity >= 0) {
                    $product->update(['quantity' => $product->quantity + $orderItem->quantity]);
                } else {
                    throw new \Exception('Không đủ hàng trong kho');
                }
            }
        } else {
            $data['error'] =  'Order không tồn tại hoặc đã được xử lý';
        }

        return  $data;
    }


    /**
     * @return Order|string
     */
    public function store(): Order|string
    {
        try {
            $order = new Order([
                'user_id' => $this->request->input('user_id'),
                'total_amount' => $this->request->input('total_amount'),
                'total_quantity' => $this->request->input('total_quantity'),
                'code' => strtoupper(Str::random(5)),
            ]);

            $orderItems = $this->request->input('order_item_id');

            DB::transaction(function () use ($order, $orderItems) {
                $order->save();
                $order->orderItems()->attach($orderItems);

                $orderItems = $order->orderItems;

                foreach ($orderItems as $orderItem) {
                    $product = $orderItem->product;

                    if ($product->quantity - $orderItem->quantity >= 0) {
                        $product->update(['quantity' => $product->quantity - $orderItem->quantity]);
                    } else {
                        throw new \Exception('Không đủ hàng trong kho');
                    }
                }
            });

            return $order;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return $order;
    }

}
