<?php

namespace App\Components\Category;

use App\Components\Component;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $category = Category::with([])
            ->when($this->request->filled("name"), function ($query) {
                $query->where('name', 'LIKE', '%' . $this->escapeLike($this->request->input('name')) . '%');
            });

        $orderCheck = in_array($this->request->input("order"), self::ORDER);
        if ($this->request->input("column") == 'created_at' && $orderCheck) {
            $category->orderBy('created_at', $this->request->input("order"));
        }
        $category->orderByDesc('created_at');
        return $category->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return Category
     */
    public function store(): Category
    {
        $category = new Category([
            'name' => $this->request->input('name'),
        ]);

        $category->save();

        return $category;
    }

    /**
     * @param Model $model
     * @return Model|Collection|Builder|array|null
     */

    public function show(Model $model): Model|Collection|Builder|array|null
    {
        return $model;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function update(Model $model): Model
    {
        if ($this->request->filled("name")) {
            $model->setAttribute("name", $this->request->input('name'));
        }
        $model->save();

        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $category;
    }

}
