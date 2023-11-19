<?php

namespace App\Components\Faq;

use App\Components\Component;
use App\Models\Faq;
use App\Models\Feedback;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $feedBack = Faq::with([])
            ->when($this->request->filled("question"), function ($query) {
                $query->where('question', 'LIKE', '%' . $this->escapeLike($this->request->input('question')) . '%');
            })
            ->when($this->request->filled("answer"), function ($query) {
                $query->where('answer', 'LIKE', '%' . $this->escapeLike($this->request->input('answer')) . '%');
            });
        return $feedBack->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return JsonResponse
     */
    public function getAnswer(): JsonResponse
    {
        $question = $this->request->input('question');

        $feedBack = Faq::whereQuestion($question)->first();

        if ($question === 'Có bao nhiêu sản phẩm ?') {
            $productCount = Product::count();
            return response()->json(['answer' => "Hiện có $productCount sản phẩm"]);
        }
        if ($question === 'Sản phẩm nào mới nhất ?') {
            $productNew = Product::orderBy('created_at', 'DESC')->first();
            return response()->json(['answer' => "Sản phẩm mới nhất là: $productNew->name"]);
        }
        if ($question === 'Sản phẩm nào được người dùng đánh giá tốt nhất ?') {
            $bestRatedProduct = Feedback::select('product_id', DB::raw('AVG(rating) as average_rating'))
                ->groupBy('product_id')
                ->orderByDesc('average_rating')
                ->first();
          $product =   Product::whereId($bestRatedProduct->product_id)->first();
            return response()->json(['answer' => "Sản phẩm tốt nhất là: $product->name"]);
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
