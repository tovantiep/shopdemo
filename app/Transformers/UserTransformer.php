<?php

namespace App\Transformers;

use App\Models\Product;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
       'role'
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
     * @param User $model
     * @return array
     */
    #[ArrayShape([])] public function transform(User $model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'gender' => $model->gender,
            'address' => $model->address
        ];
    }

    /**
     * @param User $model
     * @return Item
     */
    public function includeRole(User $model): Item
    {
        $category = $model->role;

        return $this->item($category, new RoleTransformer());
    }
}
