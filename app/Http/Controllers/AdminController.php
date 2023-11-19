<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UserIndexRequest;
use App\Http\Requests\Users\UserLoginRequest;
use App\Http\Requests\Users\UserStoreRequest;
use App\Http\Requests\Users\UserUpdateRequest;
use App\Models\User;
use App\Components\Users\Creator;
use App\Transformers\UserTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AdminController extends Controller
{
    /**
     * @param UserIndexRequest $request
     * @return mixed
     */
    public function index(UserIndexRequest $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $users = (new Creator($request))->index();
            return fractal()
                ->collection($users)
                ->transformWith(new UserTransformer())
                ->parseIncludes('role')
                ->paginateWith(new IlluminatePaginatorAdapter($users))
                ->respond();
        });

    }

    /**
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->store();
            DB::commit();
            return fractal()
                ->item($data)
                ->transformWith(new UserTransformer())
                ->parseIncludes('role')
                ->respond();
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->message($exception->getMessage())
                ->respondBadRequest();
        }
    }

    /**
     * Show a User
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function show(Request $request, User $user): mixed
    {
        return $this->withErrorHandling(function () use ($request, $user) {
            $user = (new Creator($request))->show($user);

            return fractal()
                ->item($user)
                ->transformWith(new UserTransformer())
                ->parseIncludes('role')
                ->respond();
        });
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = (new Creator($request))->update($user);
            DB::commit();

            return fractal()
                ->item($data)
                ->transformWith(new UserTransformer())
                ->parseIncludes('role')
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
        return $this->withErrorHandling(function () use ($request,$id) {
            return (new Creator($request))->destroy($id);
        });
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            return (new Creator($request))->login();
        });
    }

    /**
     * User Logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->withErrorHandling(function () use ($request) {
            return (new Creator($request))->logout();
        });
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getProfile(Request $request): mixed
    {
        return $this->withErrorHandling(function () use ($request) {
            $data = $request->user();
            return fractal()
                ->item($data)
                ->transformWith(new UserTransformer())
                ->respond();
        });
    }

    /**
     * Update password of a User
     *
     * @param Request $request
     * @param User $user
     * @return mixed
     */
    public function updatePassword(Request $request, User $user): mixed
    {
        return $this->withErrorHandling(function () use ($request, $user) {
            $request->validate([
                'password' => 'required|string|confirmed'
            ]);

            $data = (new Creator($request))->updatePassword($request->input('password'), $user);

            return fractal()
                ->item($data)
                ->transformWith(new UserTransformer())
                ->respond();
        });
    }
}
