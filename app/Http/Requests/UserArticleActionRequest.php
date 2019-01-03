<?php
/**
 * Gestion de la requête pour les actions des articles par utilisateur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserArticleActionRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => Validation::type('string')
                ->post('required')
                ->get(),
            'value' => Validation::type('string')
                ->post('required')
                ->get(),
        ];
    }
}
