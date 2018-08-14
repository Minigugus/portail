<?php

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use App\Models\Client;

class CheckPassport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$input = $request->all();
		// On vérifie la requête uniquement s'il s'agit d'une authentification par client uniquement
        if ($request->input('grant_type') === 'client_credentials') {
			if ($request->filled('scope'))
				throw new \Exception('Les scopes sont définis à l\'avance pour chaque clé, il ne faut pas les définir dans la requête');

			$clientId = $input['client_id'] ?? $_SERVER['PHP_AUTH_USER'] ?? null;
			$client = Client::find($clientId);

			// Si on n'arrive pas à récupérer le client_id, on refuse l'accès
			if ($clientId === null || $client === null)
				throw OAuthServerException::accessDenied();

			// On récupère la liste des scopes définis pour le client
			$scopes = json_decode($client->scopes, true);

			if ($scopes === null)
				$input['scope'] = '';
			else
				$input['scope'] = implode(' ', $scopes); // On override pour l'instant, on gèrera ça plus tard via la bdd

            $request->replace($input);
        }

 		// On vérifie que les scopes sont bien définis et pour le bon type d'authentification
		if (isset($input['scope']) && $input['scope'] !== '')
			\Scopes::checkScopesForGrantType(explode(' ', $input['scope']), $input['grant_type'] ?? null);
		elseif (isset($input['scopes']) && $input['scopes'] !== '')
			\Scopes::checkScopesForGrantType($input['scopes'], $input['grant_type'] ?? null);

		return $next($request);
    }
}
