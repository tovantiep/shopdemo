<?php

namespace App\Components\Product;

use App\Components\Component;
use App\Models\Feedback;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Manager;


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
            ->when($this->request->filled("code"), function ($query) {
                $query->where('code', 'LIKE', '%' . $this->escapeLike($this->request->input('code')) . '%');
            })
            ->when($this->request->filled("size"), function ($query) {
                $query->where('size', 'LIKE', '%' . $this->escapeLike($this->request->input('size')) . '%');
            })
            ->when($this->request->filled("color"), function ($query) {
                $query->where('color', 'LIKE', '%' . $this->escapeLike($this->request->input('color')) . '%');
            })
            ->when($this->request->filled("price"), function ($query) {
                $query->where('price', $this->request->input('price'));
            })
            ->when($this->request->filled("price_discount"), function ($query) {
                $query->where('price_discount', $this->request->input('price_discount'));
            })
            ->when($this->request->filled("quantity"), function ($query) {
                $query->where('quantity', $this->request->input('quantity'));
            })
            ->when($this->request->filled("category_id"), function ($query) {
                $query->where('category_id', $this->escapeLike($this->request->input('category_id')));
            });

        $orderCheck = in_array($this->request->input("order"), self::ORDER);
        if ($this->request->input("column") == 'created_at' && $orderCheck) {
            $product->orderBy('created_at', $this->request->input("order"));
        }

        $product->orderByDesc('created_at');
        return $product->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function related(): LengthAwarePaginator
    {
        $category_id = $this->request->input('category_id');

        $product = Product::whereCategoryId($category_id);
        return $product->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return array|null
     */
    public function bestSeller(): ?array
    {
        $result = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(12)
            ->get();
        $productIds = $result->pluck('product_id');
        $products = Product::whereIn('id', $productIds)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $productIds->toArray()) . ")"))
            ->get();
        $manager = new Manager();
        $resource = new \League\Fractal\Resource\Collection($products, new ProductTransformer());
        return $manager->createData($resource)->toArray();
    }

    /**
     * @return mixed
     */
    public function hot(): mixed
    {
        $hotProducts = Feedback::select('product_id', DB::raw('AVG(rating) as average_rating'))
            ->groupBy('product_id')
            ->orderByDesc('average_rating')
            ->get();

        $productIds = $hotProducts->pluck('product_id');

        $productDetails = Product::whereIn('id', $productIds)->get();

        return $hotProducts->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->product_id)->first();
            $categoryName = $product->category->name;
            $imageUrl = url(Storage::url($product->image));

            return [
                'product_details' => [
                    'id' => $product->id,
                    'category_id' => $product->category_id,
                    'category_name' => $categoryName,
                    'name' => $product->name,
                    'code' => $product->code,
                    'size' => json_decode($product->size),
                    'image' => $imageUrl,
                    'color' => $product->color,
                    'price' => $product->price,
                    'price_discount' => $product->price_discount,
                    'quantity' => $product->quantity,
                    'description' => $product->description,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ],
                'average_rating' => $item->average_rating,
            ];
        });
    }

    /**
     * @return Product
     */
    public function store(): Product
    {
        $imagePath = $this->request->file('image')->store('public/images');
        $sizeInput = $this->request->input('size');
        $size = is_array($sizeInput) ? $sizeInput : explode(',', $sizeInput);
        $product = new Product([
            'category_id' => $this->request->input('category_id'),
            'name' => $this->request->input('name'),
            'size' => json_encode($size),
            'code' => '',
            'image' => $imagePath,
            'color' => $this->request->input('color'),
            'price' => $this->request->input('price'),
            'price_discount' => $this->request->input('price_discount'),
            'quantity' => $this->request->input('quantity'),
            'description' => $this->request->input('description'),
        ]);
        $product->save();
        $product->code = 'TAKA-SHOP-P' . $product->id;
        $product->update();
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
        if ($this->request->filled("price_discount")) {
            $model->setAttribute("price_discount", $this->request->input('price_discount'));
        }
        if ($this->request->filled("quantity")) {
            $model->setAttribute("quantity", $this->request->input('quantity'));
        }
        if ($this->request->filled("description")) {
            $model->setAttribute("description", $this->request->input('description'));
        }
        if ($this->request->filled("size")) {
            $sizeInput = $this->request->input('size');
            $size = is_array($sizeInput) ? $sizeInput : explode(',', $sizeInput);
            $model->setAttribute("size", json_encode($size));
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
