<?php

namespace App\Http\Controllers;


use App\Components\Feedback\Creator;
use App\Http\Requests\FeedBack\FeedBackIndexRequest;
use App\Http\Requests\FeedBack\FeedBackStoreRequest;
use App\Transformers\FeedBackTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class FeedBackController extends Controller
{
    public function index(FeedBackIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $category = (new Creator($request))->index();

            return fractal()
                ->collection($category)
                ->transformWith(new FeedBackTransformer())
                ->parseIncludes('user')
                ->paginateWith(new IlluminatePaginatorAdapter($category))
                ->respond();
        });

    }

    /**
     * @param FeedBackStoreRequest $request
     * @return JsonResponse
     */
    public function store(FeedBackStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new FeedBackTransformer())
                ->parseIncludes('user')
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

}
