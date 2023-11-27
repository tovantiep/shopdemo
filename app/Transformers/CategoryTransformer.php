<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
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
     * @param Category $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Category $model): array
    {
        return [
            'id' =>$model->id,
            'name' => $model->name,
            'created_at' => $model->created_at,
        ];
    }
}
