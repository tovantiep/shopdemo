<?php

namespace App\Components\OrderItem;

use App\Components\Component;
use App\Models\OrderItem;
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
        $orderItem = OrderItem::with([])
            ->when($this->request->filled("amount"), function ($query) {
                $query->where('amount', 'LIKE', '%' . $this->escapeLike($this->request->input('amount')) . '%');
            })
            ->when($this->request->filled("quantity"), function ($query) {
                $query->where('quantity', 'LIKE', '%' . $this->escapeLike($this->request->input('quantity')) . '%');
            });
        return $orderItem->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return OrderItem
     */
    public function store(): OrderItem
    {
        $orderItem = new OrderItem([
            'product_id' => $this->request->input('product_id'),
            'amount' => $this->request->input('amount'),
            'quantity' => $this->request->input('quantity'),
        ]);
        $orderItem->save();
        return $orderItem;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        $orderItem = OrderItem::findOrFail($id);
        $orderItem->delete();
        return $orderItem;
    }

}
