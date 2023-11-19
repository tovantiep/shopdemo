<?php

namespace App\Transformers;

use App\Models\Feedback;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class FeedBackTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
       'user', 'product'
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
     * @param Feedback $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Feedback $model): array
    {
        return [
            'id' =>$model->id,
            'rating' => $model->rating,
            'comment' => $model->comment,
        ];
    }

    /**
     * @param Feedback $model
     * @return Item
     */
    public function includeUser(Feedback $model): Item
    {
        $user = $model->user;

        return $this->item($user, new UserTransformer());
    }

    /**
     * @param Feedback $model
     * @return Item
     */
    public function includeProduct(Feedback $model): Item
    {
        $user = $model->product;

        return $this->item($user, new ProductTransformer());
    }
}
