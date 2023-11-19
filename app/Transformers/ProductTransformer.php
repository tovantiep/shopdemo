<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        'category'
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
     * @param Product $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Product $model): array
    {
        return [
            'id' =>$model->id,
            'name' => $model->name,
            'image' => $model->image,
            'color' => $model->color,
            'price' => $model->price,
            'quantity' => $model->quantity,
            'description' => $model->description,
            'total_rating' => $this->calculateAverageRating($model->id) ?? null,
        ];
    }

    /**
     * @param Product $model
     * @return Item
     */
     public function includeCategory(Product $model): Item
     {
        $category = $model->category;

        return $this->item($category, new CategoryTransformer());
    }

    private function calculateAverageRating(int $productId): float
    {
        $feedbacks = Feedback::where('product_id', $productId)->get();
        $totalRating = $feedbacks->sum('rating');
        $totalFeedbacks = $feedbacks->count();

        if ($totalFeedbacks > 0) {
            return $totalRating / $totalFeedbacks;
        }

        return 0;
    }
}
