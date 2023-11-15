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
        'order_items'
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
            'user_id' => $model->user_id,
            'total_amount' => $model->total_amount,
            'total_quantity' => $model->total_quantity,

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

}
