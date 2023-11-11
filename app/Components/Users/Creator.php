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
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

class Creator extends Component
{
    /**
     * Get all data of Admin
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $users = User::with([])
            ->when($this->request->filled("name"), function ($query) {
                $query->where('name', 'LIKE', '%' . $this->escapeLike($this->request->input('name')) . '%');
            })
            ->when($this->request->filled("email"), function ($query) {
                $query->where('email', 'LIKE', '%' . $this->escapeLike($this->request->input('email')) . '%');
            });

        return $users->paginate($this->getPaginationLimit($this->request));
    }

    /**
     * Store a new User
     *
     * @return User
     */
    public function store(): User
    {
        $user = new User([
            'name' => $this->request->input('name'),
            'email' => $this->request->input('email'),
            'password' => bcrypt($this->request->input('password'))
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
     * Remove a record by id
     *
     * @param Model $model
     * @return bool|null
     */
    public function destroy(Model $model): ?bool
    {
        return $model->delete();
    }

    /**
     * Admin Login
     *
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

            $user = $this->request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;

            if ($this->request->input('remember_me')) {
                $token->expires_at = Carbon::now()->addWeeks();
            }

            $token->save();

            $jsonData = [
                'status' => 'success',
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ];
        }

        return $jsonData;
    }

    /**
     * Logout
     *
     * @param $request
     * @return JsonResponse
     */
    public function logout($request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 'success',
        ]);
    }
}
