<?php

namespace App\Http\Controllers;


use App\Components\Faq\Creator;
use App\Http\Requests\Faq\FaqIndexRequest;
use App\Http\Requests\Faq\FaqStoreRequest;
use App\Models\Faq;
use App\Transformers\FaqTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class FaqController extends Controller
{
    public function index(FaqIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $data = (new Creator($request))->index();

            return fractal()
                ->collection($data)
                ->transformWith(new FaqTransformer())
                ->paginateWith(new IlluminatePaginatorAdapter($data))
                ->respond();
        });

    }


    public function getAnswer(Request $request)
    {
        return $this->withErrorHandling(function () use ($request) {
            return (new Creator($request))->getAnswer($request);

        });
    }

    /**
     * @param FaqStoreRequest $request
     * @return JsonResponse
     */
    public function store(FaqStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new FaqTransformer())
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }


    public function update(FaqStoreRequest $request, Faq $faq): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->update($faq);
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new FaqTransformer())
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

}
