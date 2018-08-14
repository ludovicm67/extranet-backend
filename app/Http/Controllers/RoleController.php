<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Right;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $this->needPermission('roles', 'show');
      return response()->json([
        'success' => true,
        'data' => Role::all(),
      ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->needPermission('roles', 'add');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:roles',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name is required and should be unique',
        ], 409);
      }

      $role = new Role;
      $role->name = $request->name;
      $role->save();

      $rc = new RightController();
      $permissions = $rc->getPermissions(); // fetch list
      $permissionsNames = array_keys($permissions);
      Right::where('role_id', $role->id)->delete();
      if (!empty($request->permissions)) {
        foreach ($request->permissions as $name => $values) {
          if (!in_array($name, $permissionsNames)) continue;
          $permission = $permissions[$name];

          $show = 0;
          $add = 0;
          $edit = 0;
          $delete = 0;

          if ($permission->show && in_array('show', $values)) $show = 1;
          if ($permission->add && in_array('add', $values)) $add = 1;
          if ($permission->edit && in_array('edit', $values)) $edit = 1;
          if ($permission->delete && in_array('delete', $values)) $delete = 1;

          if (!$show && !$add && !$edit && !$delete) continue;

          Right::create([
            'role_id' => $role->id,
            'name' => $name,
            'show' => $show,
            'add' => $add,
            'edit' => $edit,
            'delete' => $delete,
          ]);

        }
      }

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
      $this->needPermission('roles', 'show');
      return response()->json([
        'success' => true,
        'data' => $role
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
      $this->needPermission('roles', 'edit');
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:255|unique:roles,name,' . $role->id,
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'name should be unique',
        ], 409);
      }

      $role->name = $request->name;
      $role->save();

      $rc = new RightController();
      $permissions = $rc->getPermissions(); // fetch list
      $permissionsNames = array_keys($permissions);
      Right::where('role_id', $role->id)->delete();
      if (!empty($request->permissions)) {
        foreach ($request->permissions as $name => $values) {
          if (!in_array($name, $permissionsNames)) continue;
          $permission = $permissions[$name];

          $show = 0;
          $add = 0;
          $edit = 0;
          $delete = 0;

          if ($permission->show && in_array('show', $values)) $show = 1;
          if ($permission->add && in_array('add', $values)) $add = 1;
          if ($permission->edit && in_array('edit', $values)) $edit = 1;
          if ($permission->delete && in_array('delete', $values)) $delete = 1;

          if (!$show && !$add && !$edit && !$delete) continue;

          Right::create([
            'role_id' => $role->id,
            'name' => $name,
            'show' => $show,
            'add' => $add,
            'edit' => $edit,
            'delete' => $delete,
          ]);

        }
      }

      return response()->json([
        'success' => true,
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
      $this->needPermission('roles', 'delete');
      User::where('role_id', $role->id)->update([
        'role_id' => null,
      ]);
      $role->delete();

      return response()->json([
        'success' => true,
      ]);
    }
}
