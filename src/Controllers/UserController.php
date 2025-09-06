<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\Controller;
use App\Resources\UserResource;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        $users = $this->user->all();
        return $this->success(UserResource::collection($users));
    }

    public function show(int $id)
    {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }
        return $this->success((new UserResource($user))->response());
    }

    public function store()
    {
        $data = $this->getRequestBody();

        if (!$data) {
            return $this->error('Invalid input data', 400);
        }

        try {
            $request = new \App\Requests\UserRequest($data);
            $validatedData = $request->validated();

            $user = $this->user->create($validatedData);
            return $this->success(
                (new UserResource($user))->response(),
                201
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error(json_decode($e->getMessage(), true), 422);
        } catch (\Exception $e) {
            return $this->error('An error occurred while creating the user', 500);
        }
    }

    public function update(int $id)
    {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $data = $this->getRequestBody();
        if (!$data) {
            return $this->error('Invalid input data', 400);
        }

        try {
            $request = new \App\Requests\UserRequest($data);
            $validatedData = $request->validated();

            $user = $this->user->update($id, $validatedData);

            return $this->success($user);
        } catch (\InvalidArgumentException $e) {
            return $this->error(json_decode($e->getMessage(), true), 422);
        } catch (\Exception $e) {
            return $this->error('An error occurred while updating the user', 500);
        }
    }

    public function delete(int $id)
    {
        $user = $this->user->find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $this->user->delete($id);
        return $this->success(['message' => 'User deleted successfully']);
    }
}
