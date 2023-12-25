<?php

namespace App\Components\Users;

use App\Components\Component;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        $orderCheck = in_array($this->request->input("order"), self::ORDER);
        if ($this->request->input("column") == 'created_at' && $orderCheck) {
            $users->orderBy('created_at', $this->request->input("order"));
        }
        $users->orderByDesc('created_at');

        return $users->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * @return array
     */
    public function overview(): array
    {
        $data = [];
        $user = User::all();
        $order = Order::all();
        $totalUser = $user->where('role_id', 2)->count();
        $totalAdmin = $user->where('role_id', 1)->count();
        $revenue = $order->whereIn('status', [1, 2]);
        $estimatedRevenue = $order->where('status', '<>', 3);
        $totalProduct = Product::all()->count();

        $data['revenue'] = $revenue->sum('total_amount');
        $data['estimated_revenue'] = $estimatedRevenue->sum('total_amount');
        $data['total_user'] = $totalUser;
        $data['total_admin'] = $totalAdmin;
        $data['total_product'] = $totalProduct;

        return $data;
    }

    /**
     * @return array
     */
    #[ArrayShape(['result' => "string", 'data' => "array", 'message' => "string"])] public function store()
    {
        $imagePath = $this->request->file('avatar') ?  $this->request->file('avatar')->store('public/images'): null;
        $user = new User([
            'name' => $this->request->input('name'),
            'role_id' => $this->request->input('role_id'),
            'avatar' => $imagePath ?? null,
            'phone' => $this->request->input('phone'),
            'email' => $this->request->input('email'),
            'password' => bcrypt($this->request->input('password')),
            'gender' => $this->request->input('gender'),
            'address' => $this->request->input('address'),
        ]);
        $user->save();
        return [
            'result' => 'SUCCESS',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
                'created_at' => date_format($user->created_at, 'Y-m-d'),
                'updated_at' =>  date_format($user->updated_at, 'Y-m-d'),
                'gender' => $user->gender,
                'address' => $user->address,
            ],
            'message' => 'Tạo thành công'
        ];
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

        if ($this->request->hasFile("avatar")) {
            $newImagePath = $this->request->file('avatar')->store('public/images');
            Storage::delete($model->avatar);
            $model->setAttribute("avatar", $newImagePath);
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
     * @return array
     */
    #[ArrayShape([])] public function login(): array
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $jsonData = [
                'status' => 'fails',
                'message' => 'Unauthorized'
            ];
        } else {
            $user = User::with('role')
            ->whereEmail(request('email'))
                ->first();

            $jsonData = [
                'result' => 'SUCCESS',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role_id' => $user->role_id,
                    'role_name' => $user->role->name,
                    'avatar' => $user->avatar,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'gender' => $user->gender,
                    'address' => $user->address,
                ],
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
