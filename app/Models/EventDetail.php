<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasKeyValue;
use App\Models\Event;

class EventDetail extends Model
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé
	protected $table = 'events_details';
	protected $primaryKey = ['event_id', 'key'];
	protected $fillable = [
		'event_id', 'key', 'value', 'type',
	];

	public function event() {
		$this->belongsTo(Event::class);
	}
}