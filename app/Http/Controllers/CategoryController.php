<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryIndexRequest;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Components\Category\Creator;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CategoryController extends Controller
{
    public function index(CategoryIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $category = (new Creator($request))->index();

            return fractal()
                ->collection($category)
                ->transformWith(new CategoryTransformer())
                ->paginateWith(new IlluminatePaginatorAdapter($category))
                ->respond();
        });

    }


    public function show(Request $request, Category $category): mixed
    {
        return $this->withErrorHandling(function () use ($request, $category) {
            $category = (new Creator($request))->show($category);

            return fractal()
                ->item($category)
                ->transformWith(new CategoryTransformer())
                ->respond();
        });
    }

    /**
     * @param CategoryStoreRequest $request
     * @return JsonResponse
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new CategoryTransformer())
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }


    /**
     * @param CategoryUpdateRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->update($category);
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new CategoryTransformer())
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();

            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request, $id): mixed
    {
        return $this->withErrorHandling(function () use ($request, $id) {
            return (new Creator($request))->destroy($id);
        });
    }

}
