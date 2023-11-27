<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'order_items',
        'user'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param Order $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Order $model): array
    {
        return [
            'id' =>$model->id,
            'total_amount' => $model->total_amount,
            'total_quantity' => $model->total_quantity,
            'code' => $model->code,
            'status' => $model->status,
            'created_at' => $model->created_at,
        ];
    }

    /**
     * @param Order $model
     * @return Collection
     */
    public function includeOrderItems(Order $model): Collection
    {
        $order_items = $model->orderItems;
        return $this->collection($order_items, new OrderItemTransformer());
    }

    /**
     * @param Order $model
     * @return Item
     */
    public function includeUser(Order $model): Item
    {
        $user = $model->user;
        return $this->item($user, new UserTransformer());
    }

}
