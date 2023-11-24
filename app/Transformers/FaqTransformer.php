<?php

namespace App\Transformers;

use App\Models\Faq;
use App\Models\Feedback;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class FaqTransformer extends TransformerAbstract
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
     * @param Faq $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Faq $model): array
    {
        return [
            'id' =>$model->id,
            'question' => $model->question,
            'answer' => $model->answer,
            'created_at' => $model->created_at,
        ];
    }

}
