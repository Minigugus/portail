<?php
/**
 * Gestion de la requête pour les salles.
 *
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * Tout est réalisé dans les controlleurs.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'location_id' => Validation::type('uuid')
                ->exists('locations', 'id')
                ->post('required')
                ->get(),
            'created_by_type' => Validation::type('string')
                ->get(),
            'created_by_id' => Validation::type('integer')
                ->get(),
            'owned_by_type' => Validation::type('string')
                ->post('required')
                ->get(),
            'owned_by_id' => Validation::type('integer')
                ->post('required')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
            'calendar_id' => Validation::type('uuid')
                ->exists('calendars', 'id')
                ->post('required')
                ->get(),
            'capacity' => Validation::type('integer')
                ->get()
        ];
    }
}
