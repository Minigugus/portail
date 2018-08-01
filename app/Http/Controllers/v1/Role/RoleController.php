<?php

namespace App\Http\Controllers\v1\Role;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Semester;
use App\Exceptions\PortailException;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Role
 */
class RoleController extends Controller
{
	/**
	 * Scopes Role
	 *
	 * Les Scopes requis pour manipuler les Roles
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-roles-types', 'client-get-roles-types'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOneOfDeepestChildren('user-create-roles-types', 'client-create-roles-types'),
				['user:admin']
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOneOfDeepestChildren('user-edit-roles-types', 'client-edit-roles-types'),
				['user:admin']
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOneOfDeepestChildren('user-remove-roles-types', 'client-remove-roles-types'),
				['user:admin']
			),
			['only' => ['destroy']]
		);
	}

	protected function hideRoles(Request $request, $roles) {
		$only_for = [];

		if (\Scopes::has('user-get-roles-types-users'))
			$only_for[] = 'users';

		if (\Scopes::has('user-get-roles-types-assos'))
			$only_for[] = 'assos';

		if (\Scopes::has('user-get-roles-types-groups'))
			$only_for[] = 'groups';

		foreach ($roles as $key => $role) {
			if (!in_array(explode('-', $role->only_for)[0], $only_for))
				$roles->forget($key);
		}

		return $roles;
	}

	protected function checkOnlyFor(Request $request, $only_for, $verb) {
		@list($tableName, $id) = explode('-', $only_for);
		$class = '\\App\\Models\\'.studly_case(str_singular($tableName));

		if (str_singular($tableName) === $tableName || !class_exists($class))
			abort(404, 'La table donnée n\'existe pas !');
		else if (!method_exists($class, 'roles'))
			abort(400, 'La table donnée ne possède pas de roles');
		else if ($id && !resolve($class)->find($id))
			abort(400, 'L\'id associé à la table donnée n\'existe pas');

		if (!\Scopes::has('user-'.$verb.'-roles-types-'.$tableName))
			abort(403, 'Vous n\'être pas autorisé à créer de nouveau rôle pour '.$only_for);

		return $only_for;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(Request $request): JsonResponse {
		$inputs = $request->input();
		// On vérifie que la ressource possède des rôles
		if (isset($inputs['only_for']))
			$this->checkOnlyFor($request, $inputs['only_for'], 'get');

		if ($request->has('stage') || $request->has('fromStage') || $request->has('toStage') || $request->has('allStages')) {
			// On inclue les relations et on les formattent.
			unset($inputs['stage']);
			unset($inputs['fromStage']);
			unset($inputs['toStage']);
			unset($inputs['allStages']);

			$roles = $request->has('stage') ? Role::getStage($request->stage, $inputs) : Role::getStages($request->fromStage, $request->toStage, $inputs);
		}
		else {
			$roles = new Role;

			foreach ($inputs as $key => $value) {
				if (!\Schema::hasColumn($roles->getTable(), $key))
					throw new PortailException('L\'attribut "'.$key.'" n\'existe pas');

				$roles = $roles->where($key, $value);
			}

			$roles = $roles->get();
		}

		return response()->json($roles->hideData(), 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function store(Request $request): JsonResponse {
		$role = new Role;
		$role->type = $request->type;
		$role->name = $request->name;
		$role->description = $request->description;
		$role->limited_at = $request->limited_at;
		$role->only_for = $this->checkOnlyFor($request, $request->only_for, 'set');

		if ($role->save()) {
			if ($request->filled('parent_ids'))
				$role->assignParentRole($request->parent_ids);

			return response()->json($roles->hideData(), 201);
		}
		else
			abort(500, 'Impossible de créer le role');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, $id): JsonResponse {
		$role = $request->has('withChildren') ? Role::with('children') : new Role;
		$role = $request->has('withParents') ? $role->with('parents') : $role;

		// On vérifie que la ressource possède des rôles
		if ($request->only_for)
			$role = $role->where('only_for', $this->checkOnlyFor($request, $request->only_for, 'get'));

		$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();

		if ($role) {
			$role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

			return response()->json($roles->hideData(), 200);
		}
		else
			abort(404, "Role non trouvé");
	}

	/**
	 * Update Role
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function update(Request $request, $id): JsonResponse {
		$role = $request->has('withChildren') ? Role::with('children') : new Role;
		$role = $request->has('withParents') ? $role->with('parents') : $role;

		$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();

		if ($request->filled('type'))
			$role->type = $request->input('type');

		if ($request->filled('name'))
			$role->name = $request->input('name');

		if ($request->filled('description'))
			$role->description = $request->input('description');

		if ($request->filled('limited_at'))
			$role->limited_at = $request->input('limited_at');

		// On vérifie que la ressource possède des rôles
		if ($request->filled('only_for'))
			$role = $role->where('only_for', $this->checkOnlyFor($request, $request->only_for, 'set'));

		if ($role->save()) {
			if ($request->filled('parent_ids')) {
				$role->syncParentRole($request->parent_ids); // Attention ! Ici on change tous ses parents

				$role = $request->has('withChildren') ? $role->with('children') : $role;
				$role = $request->has('withParents') ? $role->with('parents') : $role;
				$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();
			}

			return response()->json($roles->hideData(), 200);
		}
		else
			abort(500, 'Impossible de créer le role');
	}

	/**
	 * Delete Role
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, $id): JsonResponse {
		$role = new Role;

		// On vérifie que la ressource possède des rôles
		if ($request->filled('only_for'))
			$role = $role->where('only_for', $this->checkOnlyFor($request, $request->only_for, 'manage'));

		$role = is_numeric($id) ? $role->find($id) : $role->where('type', $id)->first();

		if ($role) {
			if ($role->isDeletable()) {
				if ($role->delete())
					abort(204);
				else
					abort(500, "Impossible de supprimer le role souhaité");
			}
			else
				abort(403, "Il n'est pas autorisé de supprimer ce rôle (possiblement car déjà assigné ou rôles enfants attachés)");
		}
		else
			abort(404, "Role non trouvé");
	}
}
