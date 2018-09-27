<?php

namespace App\Http\Controllers\v1\Calendar;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasCalendars;
use App\Traits\Controller\v1\HasCreatorsAndOwners;
use App\Models\User;
use App\Models\Asso;
use App\Models\Calendar;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Visible\Visible;
use App\Interfaces\Model\CanHaveCalendars;
use App\Traits\HasVisibility;

/**
 * @resource Calendar
 *
 * Gestion des calendriers
 */
class CalendarController extends Controller
{
	use HasCalendars, HasCreatorsAndOwners;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-calendars', 'client-get-calendars'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-calendars', 'client-create-calendars'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-calendars', 'client-edit-calendars'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-manage-calendars', 'client-manage-calendars'),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Calendars
	 *
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$calendars = Calendar::getSelection()->filter(function ($calendar) use ($request) {
			return ($this->tokenCanSee($request, $calendar, 'get') && (!\Auth::id() || $this->isVisible($calendar, \Auth::id()))) || $this->isCalendarFollowed($request, $calendar, \Auth::id());
		})->values()->map(function ($calendar) {
			return $calendar->hideData();
		});

		return response()->json($calendars, 200);
	}

	/**
	 * Create Calendar
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();

		$owner = $this->getOwner($request, 'calendar', 'calendrier', 'create');
		$creator = $this->getCreatorFromOwner($request, $owner, 'calendar', 'calendrier', 'create');

		$inputs['created_by_id'] = $creator->id;
		$inputs['created_by_type'] = get_class($creator);
		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$calendar = Calendar::create($inputs);

		if ($calendar) {
			$calendar = $this->getCalendar($request, \Auth::user(), $calendar->id);

			return response()->json($calendar->hideSubData(), 201);
		}
		else
			return response()->json(['message' => 'Impossible de créer le calendrier'], 500);
	}

	/**
	 * Show Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $id): JsonResponse {
		$calendar = $this->getCalendar($request, \Auth::user(), $id);

		return response()->json($calendar->hideSubData(), 200);
	}

	/**
	 * Update Calendar
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, string $id): JsonResponse {
		$calendar = $this->getCalendar($request, \Auth::user(), $id, 'edit');
		$inputs = $request->all();

		if ($request->filled('owned_by_type')) {
			$owner = $this->getOwner($request, 'calendar', 'calendrier', 'edit');

			$inputs['owned_by_id'] = $owner->id;
			$inputs['owned_by_type'] = get_class($owner);
		}

		if ($calendar->update($inputs))
			return response()->json($calendar->hideSubData(), 200);
		else
			abort(500, 'Impossible de modifier le calendrier');
	}

	/**
	 * Delete Calendar
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $id): JsonResponse {
		$calendar = $this->getCalendar($request, \Auth::user(), $id, 'manage');
		$calendar->softDelete();

		abort(204);
	}
}
