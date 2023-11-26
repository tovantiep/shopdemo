<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class OrderItemTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
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
     * @param OrderItem $model
     * @return array
     */
    #[ArrayShape([])] public function transform(OrderItem $model): array
    {
        return [
            'id' =>$model->id,
            'quantity' =>$model->quantity,
            'amount' =>$model->amount,
            'size' =>$model->size,
            'created_at' => $model->created_at,
            'product' => fractal()
                ->item($model->product)
                ->transformWith(new ProductTransformer())
                ->toArray(),

        ];
    }

}
