<?php

namespace App\Components\Order;

use App\Components\Component;
use App\Mail\OrderApproved;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


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
     * @param $id
     * @return array|string[]
     */
    public function approve($id): array
    {
        $order = Order::findOrFail($id)->first();

        if ($order && $order->status == 0) {
            $order->update(['status' => 1]);
            $user = $order->user;

            $data = [
                'mail' => $user->email,
                'user_name' => $user->name,
                'orderItems' => $order->orderItems,
                'totalAmount' => $order->total_amount,
            ];

            $orderItems = $order->orderItems;

            foreach ($orderItems as $orderItem) {
                $product = $orderItem->product;

                if ($product->quantity - $orderItem->quantity >= 0) {
                    $product->update(['quantity' => $product->quantity - $orderItem->quantity]);
                } else {
                    $data['error'] = 'Không đủ hàng trong kho';
                    return $data;
                }
            }
            return $data;
        } else {
            return ['error' => 'Order không tồn tại hoặc đã được xử lý'];
        }
    }


    /**
     * @return Order
     */
    public function store(): Order
    {
        $order = new Order([
            'user_id' => $this->request->input('user_id'),
            'total_amount' => $this->request->input('total_amount'),
            'total_quantity' => $this->request->input('total_quantity'),
        ]);

        $order->save();

        $orderItems = $this->request->input('order_item_id');
        $order->orderItems()->attach($orderItems);
        return $order;
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
