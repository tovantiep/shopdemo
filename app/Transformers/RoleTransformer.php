<?php

namespace App\Transformers;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
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
     * @param Role $model
     * @return array
     */
    #[ArrayShape([])] public function transform(Role $model): array
    {
        return [
            'id' =>$model->id,
            'name' => $model->name
        ];
    }
}
