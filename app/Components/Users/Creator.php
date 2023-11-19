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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails())
        {
            return response()->json(['error'=>$validator->errors()->all()],400);
        }

        Config::set('jwt.user', 'App\User');
        Config::set('auth.providers.users.model', User::class);
        $token = null;

        if ($token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'response' => 'success',
                'result' => [
                    'token' => $token,
                ],
            ]);
        }
        return response()->json([
            'response' => 'error',
            'message' => 'invalid_email_or_password',
        ],400);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            Auth::logout();

            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Đăng xuất thành công'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không xác thực'
            ], 401);
        }
    }
}
