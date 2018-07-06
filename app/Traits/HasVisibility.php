<?php

namespace App\Traits;

use App\Exceptions\PortailException;
use App\Models\Visibility;
use App\Models\User;
use App\Models\AuthCas;
use App\Facades\Ginger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasVisibility
{
	private $isContributor = null;
    /**
     * Fonction qui renvoie une nouvelle instance du modèle si celui-ci
     * n'est pas visible par l'utilisateur.
     *
     */
    public function hide($data, bool $remove = true, $callback = null) {
        if ($data instanceof Collection)
            return $this->hideCollection($data, $remove, $callback);
        else if ($data instanceof Model)
            return $this->hideModel($data, $remove, $callback);
    }

    protected function hideCollection(Collection $collection, bool $remove = true, $callback = null) {
        foreach ($collection as $key => $model) {
            if ($this->isVisible($model)) {
                if (method_exists($model, 'hide'))
                    $collection[$key] = $model->hide();

                if ($callback)
                    $collection[$key] = $callback($collection[$key]);
            }
            else {
                if ($remove)
                    $collection->forget($key);
                else {
                    $name = get_class($model);
                    $hidden = new $name;
                    $hidden->id = $model->id;
                    $hidden->visibility = $model->visibility;

                    $collection[$key] = $hidden;
                }
            }
        }

        return $collection;
    }

    protected function hideModel(Model $model, bool $remove = true, $callback = null) {
        if ($this->isVisible($model)) {
            if (method_exists($model, 'hide'))
                $model = $model->hide();

            if ($callback)
                $model = $callback($model);

            return $model;
        }
        else {
            if ($remove)
                return null;
            else {
                $name = get_class($model);
                $hidden = new $name;
                $hidden->id = $model->id;
                $hidden->visibility = $model->visibility;

                return $hidden;
            }
        }
    }

    /**
     * Fonction permettant d'indiquer si la ressource peut-être visible ou non pour l'utilisateur actuel
     *
     * @return bool
     */
    public function isVisible(Model $model) { // TODO Il faut faire passer un userid en option
        // Si on est pas connecté, on regarde si la visibilité est publique ou non
        if (Auth::id() === null)
            return is_null($model->visibility_id) || ($model->visibility_id === Visibility::getTopStage()->first()->id);

        // Si le modèle n'a pas de visibilité, on prend la première visibilité, la plus ouverte.
        if ($model->visibility_id)
            $visibility = $model->visibility;
        else
            $visibility = Visibility::getTopStage()->first();

        if ($visibility === null)
            throw new PortailException('La relation de visibilité n\'a pas été trouvée');

        $type = 'is'.ucfirst($visibility->type);

        if (method_exists(get_class(), $type) && $this->$type(Auth::id(), $model))
            return true;

        return false;
    }

    public function isPublic($user_id = null, $model = null) {
        return true;
    }

    public function isLogged($user_id, $model = null) {
        return User::find($user_id)->exists();
    }

    public function isCas($user_id, $model = null) {
		$cas = AuthCas::find($user_id);

        return $cas && $cas->where('is_active', true)->exists();
    }

    public function isContributorBDE($user_id, $model = null) {
    	$this->isContributor ?? $this->isContributor = Ginger::user(AuthCas::find($user_id)->login)->isContributor();
    	return $this->isContributor;
    }

    public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		if ($model->user_id && $model->user_id == $user_id)
			return true;

		try {
			return $model->currentAllMembers()->wherePivot('user_id', $user_id)->count() > 0;
		}
		catch (Exception $e) {}

        return false;
    }

    public function isInternal($user_id, $model = null) {
        return User::find($user_id)->hasOneRole('superadmin');
    }
}
