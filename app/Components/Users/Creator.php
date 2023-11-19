<?php

namespace App\Components\Users;

use App\Components\Component;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\ArrayShape;
use Tymon\JWTAuth\Facades\JWTAuth;

class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $users = User::with(['role'])
            ->when($this->request->filled("role_id"), function ($query) {
                $query->where('role_id', $this->request->input('role_id'));
            })
            ->when($this->request->filled("name"), function ($query) {
                $query->where('name', 'LIKE', '%' . $this->escapeLike($this->request->input('name')) . '%');
            })
            ->when($this->request->filled("email"), function ($query) {
                $query->where('email', 'LIKE', '%' . $this->escapeLike($this->request->input('email')) . '%');
            })
            ->when($this->request->filled("phone"), function ($query) {
                $query->where('phone', 'LIKE', '%' . $this->escapeLike($this->request->input('phone')) . '%');
            });

        return $users->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return User
     */
    public function store(): User
    {
        $user = new User([
            'name' => $this->request->input('name'),
            'role_id' => $this->request->input('role_id'),
            'phone' => $this->request->input('phone'),
            'email' => $this->request->input('email'),
            'password' => bcrypt($this->request->input('password')),
            'gender' => $this->request->input('gender'),
            'address' => $this->request->input('address'),
        ]);
        $user->save();
        return $user;
    }

    /**
     * Get a User by id
     *
     * @param Model $model
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function show(Model $model): Model|Collection|Builder|array|null
    {
        return $model;
    }

    /**
     * Update a User
     *
     * @param Model $model
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function update(Model $model): Model|Collection|Builder|array|null
    {
        if ($this->request->filled("name")) {
            $model->setAttribute("name", $this->request->input('name'));
        }

        if ($this->request->filled("email")) {
            $model->setAttribute("email", $this->request->input('email'));
        }

        if ($this->request->filled("gender")) {
            $model->setAttribute("gender", $this->request->input('gender'));
        }

        if ($this->request->filled("address")) {
            $model->setAttribute("address", $this->request->input('address'));
        }

        if ($this->request->filled("role_id")) {
            $model->setAttribute("role_id", $this->request->input('role_id'));
        }

        if ($this->request->filled("phone")) {
            $model->setAttribute("phone", $this->request->input('phone'));
        }

        $model->save();

        return $model;
    }

    /**
     * Update a User
     *
     * @param $password
     * @param Model $model
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function updatePassword($password, Model $model): Model|Collection|Builder|array|null
    {
        $model->setAttribute("password", bcrypt($password));

        $model->save();

        return $model;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        $user = User::findOrFail($id);
        $user->delete();
        return $user;
    }


    #[ArrayShape([])] public function login()
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $jsonData = [
                'status' => 'fails',
                'message' => 'Unauthorized'
            ];
        } else {
            $user = User::whereEmail(request('email'))->first();
            $jsonData = [
                'result' => 'SUCCESS',
                'data' => $user,
                'message' => 'Đăng nhập thành công'
            ];
        }
        return $jsonData;
    }

    /**
     * Logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
        ]);
    }
}
