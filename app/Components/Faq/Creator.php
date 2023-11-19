<?php

namespace App\Components\Faq;

use App\Components\Component;
use App\Models\Faq;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        if ($this->request->filled("answer")) {
            $model->setAttribute("answer", $this->request->input('answer'));
        }
        $model->save();

        return $model;
    }
}
