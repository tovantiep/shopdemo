<?php

namespace App\Components\Product;

use App\Components\Component;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $product = Product::with(['category'])
            ->when($this->request->filled("name"), function ($query) {
                $query->where('name', 'LIKE', '%' . $this->escapeLike($this->request->input('name')) . '%');
            })
            ->when($this->request->filled("category_id"), function ($query) {
                $query->where('category_id', $this->escapeLike($this->request->input('category_id')));
            });
        return $product->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return Product
     */
    public function store(): Product
    {
        $imagePath = $this->request->file('image')->store('public/images');

        $product = new Product([
            'category_id' => $this->request->input('category_id'),
            'name' => $this->request->input('name'),
            'image' => $imagePath,
            'color' => $this->request->input('color'),
            'price' => $this->request->input('price'),
            'quantity' => $this->request->input('quantity'),
            'description' => $this->request->input('description'),
        ]);
        $product->save();
        return $product;
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
        if ($this->request->filled("category_id")) {
            $model->setAttribute("category_id", $this->request->input('category_id'));
        }
        if ($this->request->filled("color")) {
            $model->setAttribute("color", $this->request->input('color'));
        }
        if ($this->request->hasFile("image")) {
            $newImagePath = $this->request->file('image')->store('public/images');
            Storage::delete($model->image);
            $model->setAttribute("image", $newImagePath);
        }
        if ($this->request->filled("price")) {
            $model->setAttribute("price", $this->request->input('price'));
        }
        if ($this->request->filled("quantity")) {
            $model->setAttribute("quantity", $this->request->input('quantity'));
        }
        if ($this->request->filled("description")) {
            $model->setAttribute("description", $this->request->input('description'));
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
        $category = Product::findOrFail($id);
        $category->delete();
        return $category;
    }

}
