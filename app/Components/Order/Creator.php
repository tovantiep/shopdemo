<?php

namespace App\Components\Order;

use App\Components\Component;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


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
            ->when($this->request->filled("total_mount"), function ($query) {
                $query->where('total_mount', 'LIKE', '%' . $this->escapeLike($this->request->input('total_mount')) . '%');
            });
        return $order->paginate($this->getPaginationLimit($this->request));
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
