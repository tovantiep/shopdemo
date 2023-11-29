<?php

namespace App\Components\Faq;

use App\Components\Component;
use App\Models\Faq;
use App\Models\Feedback;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $faqs = Faq::with([])
            ->when($this->request->filled("question"), function ($query) {
                $query->where('question', 'LIKE', '%' . $this->escapeLike($this->request->input('question')) . '%');
            })
            ->when($this->request->filled("answer"), function ($query) {
                $query->where('answer', 'LIKE', '%' . $this->escapeLike($this->request->input('answer')) . '%');
            });
        $orderCheck = in_array($this->request->input("order"), self::ORDER);
        if ($this->request->input("column") == 'created_at' && $orderCheck) {
            $faqs->orderBy('created_at', $this->request->input("order"));
        }
        $faqs->orderByDesc('created_at');
        return $faqs->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return JsonResponse
     */
    public function getAnswer(): JsonResponse
    {
        $question = $this->request->input('question');

        $feedBack = Faq::whereQuestion($question)->first();

        if ($question == 'Có bao nhiêu sản phẩm ?') {
            $productCount = Product::count();
            return response()->json(['answer' => "Hiện có $productCount sản phẩm"]);
        }
        if ($question == 'Sản phẩm nào mới nhất ?') {
            $productNew = Product::orderBy('created_at', 'DESC')->take(3)->get();
            $manager = new Manager();
            $resource = new Collection($productNew, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => $response]);
        }
        if ($question == 'Sản phẩm giảm giá rẻ nhất ?') {
            $productNew = Product::whereNotNull('price_discount')->orderBy('price_discount', 'ASC')->take(3)->get();
            $manager = new Manager();
            $resource = new Collection($productNew, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => $response]);
        }
        if ($question == 'Sản phẩm giá gốc rẻ nhất ?') {
            $productNew = Product::orderBy('price', 'ASC')->take(3)->get();
            $manager = new Manager();
            $resource = new Collection($productNew, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => $response]);

        }
        if ($question == 'Sản phẩm đang giảm giá ?') {
            $product = Product::whereNotNull('price_discount')->get();
            $manager = new Manager();
            $resource = new Collection($product, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => $response]);
        }

        if ($question == 'Tổng số sản phẩm đang giảm giá ?') {
            $product= Product::whereNotNull('price_discount')->count();

            return response()->json(['answer' => "Hiện có $product sản phẩm đang được giảm giá"]);
        }
        if ($question == 'Sản phẩm được đánh giá gần đây ?') {
            $latestFeedback = Feedback::latest('created_at')->first();
            $productIds = $latestFeedback->product_id;
            $hotProductsDetails = Product::whereId( $productIds)->first();
            $manager = new Manager();
            $resource = new Item($hotProductsDetails, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => ['data' => [$response['data']]]]);
        }
        if ($question == 'Sản phẩm bán nhạy nhất ?') {
            $result = DB::table('order_items')
                ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderByDesc('total_quantity')
                ->first();
            $productIds = $result->product_id;
            $hotProductsDetails = Product::whereId( $productIds)->first();
            $manager = new Manager();
            $resource = new Item($hotProductsDetails, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();
            return response()->json(['answer' => ['data' => [$response['data']]]]);
        }
        if ($question == 'Sản phẩm được đánh giá cao ?' || $question == 'Sản phẩm hot nhất ?' ) {
            $hotProducts = Feedback::select('product_id', DB::raw('AVG(rating) as average_rating'))
                ->groupBy('product_id')
                ->orderByDesc('average_rating')
                ->take(3)
                ->get();

            $productIds = $hotProducts->pluck('product_id');
            $hotProductsDetails = Product::whereIn('id', $productIds)
                ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $productIds->toArray()) . ")"))
                ->get();
            $manager = new Manager();
            $resource = new Collection($hotProductsDetails, new ProductTransformer());
            $response = $manager->createData($resource)->toArray();

            return response()->json(['answer' => $response]);
        }
        if (isset($feedBack)) {
            return response()->json(['answer' => "$feedBack->answer"]);
        }

        return response()->json(['answer' => 'Xin lỗi, chúng tôi không thể trả lời câu hỏi này']);
    }


    /**
     * @return Faq
     */
    public function store(): Faq
    {
        $feedBack = new Faq([
            'question' => $this->request->input('question'),
            'answer' => $this->request->input('answer'),
        ]);

        $feedBack->save();
        return $feedBack;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function update(Model $model): Model
    {
        if ($this->request->filled("question")) {
            $model->setAttribute("question", $this->request->input('question'));
        }
        if ($this->request->filled("answer")) {
            $model->setAttribute("answer", $this->request->input('answer'));
        }
        $model->save();

        return $model;
    }
}
