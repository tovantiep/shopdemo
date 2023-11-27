<?php

namespace App\Transformers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
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
     * @param User $model
     * @return array
     */
   public function transform(User $model): array
   {
       $imagePath = $model->avatar ? url(Storage::url($model->avatar)) : null;
        return [
            'id' => $model->id,
            'name' => $model->name,
            'avatar' => $imagePath,
            'phone' => $model->phone,
            'email' => $model->email,
            'gender' => $model->gender,
            'address' => $model->address,
            'created_at' => $model->created_at,
        ];
    }

    /**
     * @param User $model
     * @return Item
     */
    public function includeRole(User $model): Item
    {
        $role = $model->role;
        return $this->item($role, new RoleTransformer());
    }
}
