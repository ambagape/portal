<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ChatConversation
 *
 * @property-read \App\Models\User $clientUser
 * @property-read \App\Models\User $coachUser
 * @property-read \App\Models\ChatMessage|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatParticipant[] $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation query()
 */
	class ChatConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatMessage
 *
 * @property-read \App\Models\ChatConversation $conversation
 * @property-read \App\Models\ChatParticipant $participant
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatParticipant
 *
 * @property-read \App\Models\ChatConversation $conversation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant query()
 */
	class ChatParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Token
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 */
	class Token extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 */
	class User extends \Eloquent {}
}

