<?php

namespace App\Components\FeedBack;

use App\Components\Component;
use App\Models\Feedback;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;



class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $feedBack = Feedback::with([])
            ->when($this->request->filled("user_id"), function ($query) {
                $query->where('user_id', $this->request->input('user_id'));
            })
            ->when($this->request->filled("rating"), function ($query) {
                $query->where('rating', $this->request->input('rating'));
            })
            ->when($this->request->filled("comment"), function ($query) {
                $query->where('comment', 'LIKE', '%' . $this->escapeLike($this->request->input('comment')) . '%');
            });
        return $feedBack->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return Feedback
     */
    public function store(): Feedback
    {
        $feedBack = new Feedback([
            'user_id' => $this->request->input('user_id'),
            'rating' => $this->request->input('rating'),
            'comment' => $this->request->input('comment'),
        ]);

        $feedBack->save();

        return $feedBack;
    }
}
