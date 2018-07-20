<?php

/*
 * Liste des scopes en fonction des routes
 *   - Définition des scopes:
 *      portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
 *      ex: user-get-user user-get-user-assos user-get-user-assos-followed
 *
 *   - Définition de la portée des scopes:
 *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
 *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
 *
 *   - Définition du verbe:
 *     + manage:  gestion de la ressource entière
 *       + set :  posibilité d'écrire et modifier les données
 *         + get :  récupération des informations en lecture seule
 *         + create:  créer une donnée associée
 *         + edit:    modifier une donnée
 *       + remove:  supprimer une donnée
 */

// Toutes les routes commencant par client-{verbe}-assos-
return [
	'description' => 'Associations',
	'verbs' => [
		'manage' => [
			'description' => 'Gérer les associations suivies et faites par les utilisateurs',
			'scopes' => [
				'joined' => [
					'description' => 'Gérer les associations faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Gérer les associations faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'joining' => [
					'description' => 'Gérer les associations que les utilisateurs souhaitent rejoindre',
					'scopes' => [
						'now' => [
							'description' => 'Gérer les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Gérer les associations suivies par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Gérer les associations suivies par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'get' => [
			'description' => 'Récupérer toutes les associations suivies et faites par les utilisateurs',
			'scopes' => [
				'joined' => [
					'description' => 'Récupérer toutes les associations faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer toutes les associations faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'joining' => [
					'description' => 'Récupérer toutes les associations que les utilisateurs souhaitent rejoindre',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer toutes les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Récupérer toutes les associations suivies par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer toutes les associations suivies par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'members' => [
					'description' => 'Récupérer tous les membres des associations suivies et faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Récupérer tous les membres des associations suivies et faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'set' => [
			'description' => 'Ajouter et modifier les associations suivies et faites par les utilisateurs',
			'scopes' => [
				'joined' => [
					'description' => 'Ajouter et modifier les associations faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter et modifier les associations faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'joining' => [
					'description' => 'Ajouter et modifier les associations que les utilisateurs souhaitent rejoindre',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter et modifier les associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Ajouter et modifier les associations suivies par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter et modifier les associations suivies par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'create' => [
			'description' => 'Ajouter des associations suivies et faites par les utilisateurs',
			'scopes' => [
				'joined' => [
					'description' => 'Ajouter des associations faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter des associations faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'joining' => [
					'description' => 'Ajouter des associations que les utilisateurs souhaitent rejoindre',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter des associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Ajouter des associations suivies par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Ajouter des associations suivies par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
			]
		],
		'remove' => [
			'description' => 'Supprimer des associations suivies et faites par les utilisateurs',
			'scopes' => [
				'joined' => [
					'description' => 'Supprimer des associations faites par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Supprimer des associations faites par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
				'joining' => [
					'description' => 'Supprimer des associations que les utilisateurs souhaitent rejoindre',
					'scopes' => [
						'now' => [
							'description' => 'Supprimer des associations que les utilisateurs souhaitent rejoindre durant l\'actuel semestre',
						],
					]
				],
				'followed' => [
					'description' => 'Supprimer des associations suivies par les utilisateurs',
					'scopes' => [
						'now' => [
							'description' => 'Supprimer des associations suivies par les utilisateurs durant l\'actuel semestre',
						],
					]
				],
			]
		],
	]
];
